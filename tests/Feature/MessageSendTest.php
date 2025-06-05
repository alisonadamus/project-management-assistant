<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Subject;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\Message;
use Spatie\Permission\Models\Role;

class MessageSendTest extends TestCase
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
    public function teacher_can_send_message_to_assigned_project()
    {
        // Створюємо користувачів
        $teacher = User::factory()->create([
            'email' => 'teacher@uzhnu.edu.ua',
            'course_number' => null
        ]);
        $teacher->assignRole('teacher');

        $student = User::factory()->create([
            'email' => 'student@student.uzhnu.edu.ua',
            'course_number' => 1
        ]);
        $student->assignRole('student');

        // Створюємо предмет та категорію
        $subject = Subject::factory()->create();
        $category = Category::factory()->create([
            'course_number' => 1
        ]);
        $category->subjects()->attach($subject);

        // Створюємо подію
        $event = Event::factory()->create([
            'category_id' => $category->id
        ]);

        // Створюємо керівника
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
            'slot_count' => 5
        ]);

        // Створюємо проект з призначеним студентом
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => $student->id
        ]);

        // Тестуємо відправку повідомлення від викладача
        $response = $this->actingAs($teacher)
            ->postJson("/projects/{$project->id}/messages", [
                'message' => 'Тестове повідомлення від викладача'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message' => [
                'id',
                'message',
                'message_html',
                'sender_id',
                'sender_name',
                'is_read',
                'created_at',
                'is_mine'
            ]
        ]);

        // Перевіряємо, що повідомлення збережено в базі
        $this->assertDatabaseHas('messages', [
            'project_id' => $project->id,
            'sender_id' => $teacher->id,
            'message' => 'Тестове повідомлення від викладача'
        ]);
    }

    /** @test */
    public function student_can_send_message_to_assigned_project()
    {
        // Створюємо користувачів
        $teacher = User::factory()->create([
            'email' => 'teacher@uzhnu.edu.ua',
            'course_number' => null
        ]);
        $teacher->assignRole('teacher');

        $student = User::factory()->create([
            'email' => 'student@student.uzhnu.edu.ua',
            'course_number' => 1
        ]);
        $student->assignRole('student');

        // Створюємо предмет та категорію
        $subject = Subject::factory()->create();
        $category = Category::factory()->create([
            'course_number' => 1
        ]);
        $category->subjects()->attach($subject);

        // Створюємо подію
        $event = Event::factory()->create([
            'category_id' => $category->id
        ]);

        // Створюємо керівника
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
            'slot_count' => 5
        ]);

        // Створюємо проект з призначеним студентом
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => $student->id
        ]);

        // Тестуємо відправку повідомлення від студента
        $response = $this->actingAs($student)
            ->postJson("/projects/{$project->id}/messages", [
                'message' => 'Тестове повідомлення від студента'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message' => [
                'id',
                'message',
                'message_html',
                'sender_id',
                'sender_name',
                'is_read',
                'created_at',
                'is_mine'
            ]
        ]);

        // Перевіряємо, що повідомлення збережено в базі
        $this->assertDatabaseHas('messages', [
            'project_id' => $project->id,
            'sender_id' => $student->id,
            'message' => 'Тестове повідомлення від студента'
        ]);
    }

    /** @test */
    public function unauthorized_user_cannot_send_message()
    {
        // Створюємо користувачів
        $teacher = User::factory()->create([
            'email' => 'teacher@uzhnu.edu.ua',
            'course_number' => null
        ]);
        $teacher->assignRole('teacher');

        $student = User::factory()->create([
            'email' => 'student@student.uzhnu.edu.ua',
            'course_number' => 1
        ]);
        $student->assignRole('student');

        $unauthorizedUser = User::factory()->create([
            'email' => 'other@student.uzhnu.edu.ua',
            'course_number' => 2
        ]);
        $unauthorizedUser->assignRole('student');

        // Створюємо предмет та категорію
        $subject = Subject::factory()->create();
        $category = Category::factory()->create([
            'course_number' => 1
        ]);
        $category->subjects()->attach($subject);

        // Створюємо подію
        $event = Event::factory()->create([
            'category_id' => $category->id
        ]);

        // Створюємо керівника
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
            'slot_count' => 5
        ]);

        // Створюємо проект з призначеним студентом
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => $student->id
        ]);

        // Тестуємо відправку повідомлення від неавторизованого користувача
        $response = $this->actingAs($unauthorizedUser)
            ->postJson("/projects/{$project->id}/messages", [
                'message' => 'Тестове повідомлення від неавторизованого користувача'
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function message_validation_works()
    {
        // Створюємо користувачів
        $teacher = User::factory()->create([
            'email' => 'teacher@uzhnu.edu.ua',
            'course_number' => null
        ]);
        $teacher->assignRole('teacher');

        $student = User::factory()->create([
            'email' => 'student@student.uzhnu.edu.ua',
            'course_number' => 1
        ]);
        $student->assignRole('student');

        // Створюємо предмет та категорію
        $subject = Subject::factory()->create();
        $category = Category::factory()->create([
            'course_number' => 1
        ]);
        $category->subjects()->attach($subject);

        // Створюємо подію
        $event = Event::factory()->create([
            'category_id' => $category->id
        ]);

        // Створюємо керівника
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
            'slot_count' => 5
        ]);

        // Створюємо проект з призначеним студентом
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => $student->id
        ]);

        // Тестуємо валідацію - порожнє повідомлення
        $response = $this->actingAs($teacher)
            ->postJson("/projects/{$project->id}/messages", [
                'message' => ''
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);

        // Тестуємо валідацію - занадто довге повідомлення
        $response = $this->actingAs($teacher)
            ->postJson("/projects/{$project->id}/messages", [
                'message' => str_repeat('a', 1001)
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }
}
