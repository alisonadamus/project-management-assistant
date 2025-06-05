<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Message;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Notifications\NewChatMessageNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NewChatMessageNotificationTest extends TestCase
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
    public function it_sends_notification_to_supervisor_when_student_sends_message()
    {
        Notification::fake();

        // Створення користувачів
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        $student = User::factory()->create(['course_number' => 2]);
        $student->assignRole('student');

        // Створення події та проекту
        $category = Category::factory()->create(['course_number' => 2]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
        ]);
        
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => $student->id,
        ]);

        // Авторизація як студент
        $this->actingAs($student);

        // Відправка повідомлення
        $response = $this->postJson(route('messages.send', $project), [
            'message' => 'Привіт, у мене є питання щодо проекту'
        ]);

        $response->assertStatus(200);

        // Перевірка, що повідомлення надіслано викладачу
        Notification::assertSentTo($teacher, NewChatMessageNotification::class);
        Notification::assertNotSentTo($student, NewChatMessageNotification::class);
    }

    /** @test */
    public function it_sends_notification_to_student_when_supervisor_sends_message()
    {
        Notification::fake();

        // Створення користувачів
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        $student = User::factory()->create(['course_number' => 2]);
        $student->assignRole('student');

        // Створення події та проекту
        $category = Category::factory()->create(['course_number' => 2]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
        ]);
        
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => $student->id,
        ]);

        // Авторизація як викладач
        $this->actingAs($teacher);

        // Відправка повідомлення
        $response = $this->postJson(route('messages.send', $project), [
            'message' => 'Відповідаю на ваше питання'
        ]);

        $response->assertStatus(200);

        // Перевірка, що повідомлення надіслано студенту
        Notification::assertSentTo($student, NewChatMessageNotification::class);
        Notification::assertNotSentTo($teacher, NewChatMessageNotification::class);
    }

    /** @test */
    public function it_does_not_send_notification_for_project_without_assigned_student()
    {
        Notification::fake();

        // Створення користувача
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        // Створення події та проекту без призначеного студента
        $category = Category::factory()->create(['course_number' => 2]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
        ]);
        
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => null, // Немає призначеного студента
        ]);

        // Авторизація як викладач
        $this->actingAs($teacher);

        // Спроба відправки повідомлення
        $response = $this->postJson(route('messages.send', $project), [
            'message' => 'Тестове повідомлення'
        ]);

        $response->assertStatus(403); // Доступ заборонено

        // Перевірка, що повідомлення не надіслано
        Notification::assertNothingSent();
    }

    /** @test */
    public function notification_contains_correct_message_data()
    {
        Notification::fake();

        // Створення користувачів
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        $student = User::factory()->create(['course_number' => 2]);
        $student->assignRole('student');

        // Створення події та проекту
        $category = Category::factory()->create(['course_number' => 2]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
        ]);
        
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => $student->id,
        ]);

        // Авторизація як студент
        $this->actingAs($student);

        $messageText = 'Це тестове повідомлення для перевірки';

        // Відправка повідомлення
        $response = $this->postJson(route('messages.send', $project), [
            'message' => $messageText
        ]);

        $response->assertStatus(200);

        // Перевірка вмісту повідомлення
        Notification::assertSentTo($teacher, NewChatMessageNotification::class, function ($notification) use ($messageText, $project, $student) {
            return $notification->message->message === $messageText &&
                   $notification->project->id === $project->id &&
                   $notification->sender->id === $student->id;
        });
    }
}
