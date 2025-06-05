<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Notifications\EventStartNotification;
use Alison\ProjectManagementAssistant\Notifications\EventStartingSoonNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SendEventStartNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Створюємо ролі
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'student']);
    }

    /** @test */
    public function it_sends_notifications_for_events_starting_today()
    {
        Notification::fake();

        // Створюємо категорію
        $category = Category::factory()->create(['course_number' => 1]);

        // Створюємо подію, яка починається сьогодні
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->startOfDay()->addHours(10),
            'end_date' => now()->addDays(5),
        ]);

        // Створюємо студента з відповідним курсом
        $student = User::factory()->create(['course_number' => 1]);
        $student->assignRole('student');

        // Створюємо викладача та призначаємо його науковим керівником
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');

        Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
        ]);

        // Запускаємо команду
        Artisan::call('app:send-event-start-notifications');

        // Перевіряємо, що повідомлення надіслані
        Notification::assertSentTo($student, EventStartNotification::class);
        Notification::assertSentTo($teacher, EventStartNotification::class);
    }

    /** @test */
    public function it_sends_notifications_for_events_starting_in_two_days()
    {
        Notification::fake();

        // Створюємо категорію
        $category = Category::factory()->create(['course_number' => 2]);

        // Створюємо подію, яка починається через 2 дні
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->addDays(2)->startOfDay()->addHours(14),
            'end_date' => now()->addDays(7),
        ]);

        // Створюємо студента з відповідним курсом
        $student = User::factory()->create(['course_number' => 2]);
        $student->assignRole('student');

        // Створюємо викладача та призначаємо його науковим керівником
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');

        Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
        ]);

        // Запускаємо команду
        Artisan::call('app:send-event-start-notifications');

        // Перевіряємо, що повідомлення надіслані
        Notification::assertSentTo($student, EventStartingSoonNotification::class);
        Notification::assertSentTo($teacher, EventStartingSoonNotification::class);
    }

    /** @test */
    public function it_does_not_send_notifications_to_students_with_wrong_course()
    {
        Notification::fake();

        // Створюємо категорію для курсу 1
        $category = Category::factory()->create(['course_number' => 1]);

        // Створюємо подію
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->startOfDay()->addHours(10),
            'end_date' => now()->addDays(5),
        ]);

        // Створюємо студента з курсом 2 (не відповідає події)
        $student = User::factory()->create(['course_number' => 2]);
        $student->assignRole('student');

        // Запускаємо команду
        Artisan::call('app:send-event-start-notifications');

        // Перевіряємо, що повідомлення НЕ надіслані
        Notification::assertNotSentTo($student, EventStartNotification::class);
    }

    /** @test */
    public function it_does_not_send_notifications_to_teachers_who_are_not_supervisors()
    {
        Notification::fake();

        // Створюємо категорію
        $category = Category::factory()->create(['course_number' => 1]);

        // Створюємо подію
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->startOfDay()->addHours(10),
            'end_date' => now()->addDays(5),
        ]);

        // Створюємо викладача, який НЕ є науковим керівником цієї події
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');

        // Запускаємо команду
        Artisan::call('app:send-event-start-notifications');

        // Перевіряємо, що повідомлення НЕ надіслані
        Notification::assertNotSentTo($teacher, EventStartNotification::class);
    }
}
