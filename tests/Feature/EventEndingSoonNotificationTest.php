<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Notifications\EventEndingSoonNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EventEndingSoonNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Створення ролей
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'student']);
    }

    /** @test */
    public function it_sends_event_ending_soon_notifications_to_correct_users()
    {
        Notification::fake();

        // Створення категорії
        $category = Category::factory()->create(['course_number' => 2]);

        // Створення події, яка закінчується через 2 дні
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(2)->setHour(14)->setMinute(0),
        ]);

        // Створення викладача-керівника
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
        ]);

        // Створення студентів
        $student1 = User::factory()->create(['course_number' => 2]);
        $student1->assignRole('student');
        
        $student2 = User::factory()->create(['course_number' => 3]); // Інший курс
        $student2->assignRole('student');

        // Запуск команди
        Artisan::call('app:send-event-start-notifications');

        // Перевірка, що повідомлення надіслано правильним користувачам
        Notification::assertSentTo($teacher, EventEndingSoonNotification::class);
        Notification::assertSentTo($student1, EventEndingSoonNotification::class);
        Notification::assertNotSentTo($student2, EventEndingSoonNotification::class);
    }

    /** @test */
    public function it_does_not_send_notifications_for_events_ending_on_different_days()
    {
        Notification::fake();

        // Створення категорії
        $category = Category::factory()->create(['course_number' => 1]);

        // Створення подій з різними датами закінчення
        $eventEndingToday = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->subDays(3),
            'end_date' => now()->setHour(14)->setMinute(0),
        ]);

        $eventEndingTomorrow = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDay()->setHour(14)->setMinute(0),
        ]);

        $eventEndingInThreeDays = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(3)->setHour(14)->setMinute(0),
        ]);

        // Створення користувача
        $student = User::factory()->create(['course_number' => 1]);
        $student->assignRole('student');

        // Запуск команди
        Artisan::call('app:send-event-start-notifications');

        // Перевірка, що повідомлення надіслано тільки для події, яка закінчується через 2 дні
        Notification::assertNotSentTo($student, EventEndingSoonNotification::class, function ($notification) use ($eventEndingToday) {
            return $notification->event->id === $eventEndingToday->id;
        });

        Notification::assertNotSentTo($student, EventEndingSoonNotification::class, function ($notification) use ($eventEndingTomorrow) {
            return $notification->event->id === $eventEndingTomorrow->id;
        });

        Notification::assertNotSentTo($student, EventEndingSoonNotification::class, function ($notification) use ($eventEndingInThreeDays) {
            return $notification->event->id === $eventEndingInThreeDays->id;
        });
    }

    /** @test */
    public function it_sends_notifications_only_to_supervisors_of_the_event()
    {
        Notification::fake();

        // Створення категорії
        $category = Category::factory()->create(['course_number' => 1]);

        // Створення події
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now()->subDays(3),
            'end_date' => now()->addDays(2)->setHour(14)->setMinute(0),
        ]);

        // Створення викладачів
        $supervisorTeacher = User::factory()->create();
        $supervisorTeacher->assignRole('teacher');
        
        $nonSupervisorTeacher = User::factory()->create();
        $nonSupervisorTeacher->assignRole('teacher');

        // Тільки один викладач є керівником події
        Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $supervisorTeacher->id,
        ]);

        // Запуск команди
        Artisan::call('app:send-event-start-notifications');

        // Перевірка, що повідомлення надіслано тільки керівнику
        Notification::assertSentTo($supervisorTeacher, EventEndingSoonNotification::class);
        Notification::assertNotSentTo($nonSupervisorTeacher, EventEndingSoonNotification::class);
    }
}
