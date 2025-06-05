<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Subevent;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Notifications\SubeventStartNotification;
use Alison\ProjectManagementAssistant\Notifications\SubeventStartingSoonNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SendSubeventNotificationsTest extends TestCase
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
    public function it_sends_notifications_for_subevents_starting_today()
    {
        Notification::fake();

        // Створюємо категорію та подію
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(10),
        ]);

        // Створюємо підподію, яка починається сьогодні
        $subevent = Subevent::factory()->create([
            'event_id' => $event->id,
            'start_date' => now()->startOfDay()->addHours(12),
            'end_date' => now()->addDays(2),
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
        Artisan::call('app:send-subevent-notifications');

        // Перевіряємо, що повідомлення надіслані
        Notification::assertSentTo($student, SubeventStartNotification::class);
        Notification::assertSentTo($teacher, SubeventStartNotification::class);
    }

    /** @test */
    public function it_sends_notifications_for_subevents_starting_tomorrow()
    {
        Notification::fake();

        // Створюємо категорію та подію
        $category = Category::factory()->create(['course_number' => 2]);
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(10),
        ]);

        // Створюємо підподію, яка починається завтра
        $subevent = Subevent::factory()->create([
            'event_id' => $event->id,
            'start_date' => now()->addDay()->startOfDay()->addHours(15),
            'end_date' => now()->addDays(3),
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
        Artisan::call('app:send-subevent-notifications');

        // Перевіряємо, що повідомлення надіслані
        Notification::assertSentTo($student, SubeventStartingSoonNotification::class);
        Notification::assertSentTo($teacher, SubeventStartingSoonNotification::class);
    }

    /** @test */
    public function it_does_not_send_notifications_to_students_with_wrong_course()
    {
        Notification::fake();

        // Створюємо категорію для курсу 1 та подію
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(10),
        ]);

        // Створюємо підподію
        $subevent = Subevent::factory()->create([
            'event_id' => $event->id,
            'start_date' => now()->startOfDay()->addHours(12),
            'end_date' => now()->addDays(2),
        ]);

        // Створюємо студента з курсом 3 (не відповідає події)
        $student = User::factory()->create(['course_number' => 3]);
        $student->assignRole('student');

        // Запускаємо команду
        Artisan::call('app:send-subevent-notifications');

        // Перевіряємо, що повідомлення НЕ надіслані
        Notification::assertNotSentTo($student, SubeventStartNotification::class);
    }

    /** @test */
    public function it_does_not_send_notifications_to_teachers_who_are_not_supervisors()
    {
        Notification::fake();

        // Створюємо категорію та подію
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(10),
        ]);

        // Створюємо підподію
        $subevent = Subevent::factory()->create([
            'event_id' => $event->id,
            'start_date' => now()->startOfDay()->addHours(12),
            'end_date' => now()->addDays(2),
        ]);

        // Створюємо викладача, який НЕ є науковим керівником цієї події
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');

        // Запускаємо команду
        Artisan::call('app:send-subevent-notifications');

        // Перевіряємо, що повідомлення НЕ надіслані
        Notification::assertNotSentTo($teacher, SubeventStartNotification::class);
    }
}
