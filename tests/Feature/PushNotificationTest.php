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
use NotificationChannels\WebPush\PushSubscription;
use NotificationChannels\WebPush\WebPushChannel;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PushNotificationTest extends TestCase
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
    public function user_can_create_push_subscription()
    {
        $user = User::factory()->create();
        $user->assignRole('student');

        $subscriptionData = [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
            'keys' => [
                'auth' => 'test-auth-key-12345',
                'p256dh' => 'test-p256dh-key-67890'
            ]
        ];

        $response = $this->actingAs($user)
            ->postJson('/push-subscriptions', $subscriptionData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Push підписка успішно створена'
            ]);

        // Перевіряємо, що підписка збережена в базі даних
        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $user->id,
            'subscribable_type' => User::class,
            'endpoint' => $subscriptionData['endpoint'],
            'auth_token' => $subscriptionData['keys']['auth'],
            'public_key' => $subscriptionData['keys']['p256dh']
        ]);
    }

    /** @test */
    public function user_can_delete_push_subscription()
    {
        $user = User::factory()->create();
        $user->assignRole('student');

        // Створюємо підписку через метод моделі
        $user->updatePushSubscription(
            'https://fcm.googleapis.com/fcm/send/test-endpoint',
            'test-public-key',
            'test-auth-token'
        );

        $subscription = $user->pushSubscriptions()->first();

        $response = $this->actingAs($user)
            ->deleteJson('/push-subscriptions', [
                'endpoint' => $subscription->endpoint
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Push підписка успішно видалена'
            ]);

        // Перевіряємо, що підписка видалена з бази даних
        $this->assertDatabaseMissing('push_subscriptions', [
            'id' => $subscription->id
        ]);
    }

    /** @test */
    public function user_can_get_push_subscription_status()
    {
        $user = User::factory()->create();
        $user->assignRole('student');

        // Спочатку без підписок
        $response = $this->actingAs($user)
            ->getJson('/push-subscriptions/status');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'has_subscriptions' => false,
                'subscriptions_count' => 0
            ]);

        // Створюємо підписку через метод моделі
        $user->updatePushSubscription(
            'https://fcm.googleapis.com/fcm/send/test-endpoint',
            'test-public-key',
            'test-auth-token'
        );

        // Тепер з підпискою
        $response = $this->actingAs($user)
            ->getJson('/push-subscriptions/status');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'has_subscriptions' => true,
                'subscriptions_count' => 1
            ]);
    }

    /** @test */
    public function chat_notification_includes_webpush_channel_when_user_has_subscription()
    {
        Notification::fake();

        // Створення користувачів
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        $student = User::factory()->create(['course_number' => 2]);
        $student->assignRole('student');

        // Створення push підписки для викладача
        $teacher->updatePushSubscription(
            'https://fcm.googleapis.com/fcm/send/test-endpoint',
            'test-public-key',
            'test-auth-token'
        );

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

        // Створення повідомлення
        $message = Message::factory()->create([
            'project_id' => $project->id,
            'sender_id' => $student->id,
            'message' => 'Тестове повідомлення'
        ]);

        // Створення notification
        $notification = new NewChatMessageNotification($message);

        // Перевіряємо, що webpush канал включено
        $channels = $notification->via($teacher);
        $this->assertContains('mail', $channels);
        $this->assertContains(WebPushChannel::class, $channels);
    }

    /** @test */
    public function chat_notification_excludes_webpush_channel_when_user_has_no_subscription()
    {
        Notification::fake();

        // Створення користувачів без push підписок
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

        // Створення повідомлення
        $message = Message::factory()->create([
            'project_id' => $project->id,
            'sender_id' => $student->id,
            'message' => 'Тестове повідомлення'
        ]);

        // Створення notification
        $notification = new NewChatMessageNotification($message);

        // Перевіряємо, що webpush канал НЕ включено
        $channels = $notification->via($teacher);
        $this->assertContains('mail', $channels);
        $this->assertNotContains(WebPushChannel::class, $channels);
    }

    /** @test */
    public function webpush_message_contains_correct_data()
    {
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

        // Створення повідомлення
        $message = Message::factory()->create([
            'project_id' => $project->id,
            'sender_id' => $student->id,
            'message' => 'Це тестове повідомлення для перевірки webpush'
        ]);

        // Створення notification
        $notification = new NewChatMessageNotification($message);

        // Отримуємо webpush повідомлення
        $webPushMessage = $notification->toWebPush($teacher);

        // Перевіряємо структуру повідомлення через toArray()
        $messageArray = $webPushMessage->toArray();

        $this->assertEquals('💬 Нове повідомлення в чаті', $messageArray['title']);
        $this->assertStringContainsString($student->full_name, $messageArray['body']);
        $this->assertStringContainsString('Це тестове повідомлення для перевірки webpush', $messageArray['body']);
        $this->assertEquals('/favicon.ico', $messageArray['icon']);
        $this->assertEquals('chat-' . $project->id, $messageArray['tag']);

        // Перевіряємо дані
        $this->assertEquals($project->id, $messageArray['data']['project_id']);
        $this->assertEquals($student->id, $messageArray['data']['sender_id']);
        $this->assertEquals('new_chat_message', $messageArray['data']['type']);
        $this->assertEquals(route('projects.show', $project), $messageArray['data']['url']);
    }
}
