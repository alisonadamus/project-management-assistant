<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Offer;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Subject;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherOfferRandomAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Створення ролей та дозволів
        $teacherRole = Role::create(['name' => 'teacher']);
        $studentRole = Role::create(['name' => 'student']);
        
        Permission::create(['name' => 'view offers']);
        Permission::create(['name' => 'create offers']);
        Permission::create(['name' => 'delete offers']);
        
        $teacherRole->givePermissionTo(['view offers']);
        $studentRole->givePermissionTo(['view offers', 'create offers', 'delete offers']);
    }

    public function test_teacher_can_randomly_assign_student_to_project()
    {
        // Створення викладача
        $teacher = User::factory()->create([
            'email' => 'teacher@uzhnu.edu.ua',
            'course_number' => null,
        ]);
        $teacher->assignRole('teacher');

        // Створення студентів
        $student1 = User::factory()->create([
            'email' => 'student1@student.uzhnu.edu.ua',
            'course_number' => 1,
        ]);
        $student1->assignRole('student');

        $student2 = User::factory()->create([
            'email' => 'student2@student.uzhnu.edu.ua',
            'course_number' => 1,
        ]);
        $student2->assignRole('student');

        // Створення категорії та події
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create([
            'category_id' => $category->id,
        ]);

        // Створення наукового керівника
        $supervisor = Supervisor::factory()->create([
            'user_id' => $teacher->id,
            'event_id' => $event->id,
            'slot_count' => 2,
        ]);

        // Створення проекту
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => null,
        ]);

        // Створення заявок
        Offer::create([
            'project_id' => $project->id,
            'student_id' => $student1->id,
        ]);

        Offer::create([
            'project_id' => $project->id,
            'student_id' => $student2->id,
        ]);

        // Авторизація як викладач
        $this->actingAs($teacher);

        // Випадкове призначення студента
        $response = $this->post(route('teacher.offers.assign-random', ['project' => $project->id]));

        // Перевірка успішного перенаправлення
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Студента успішно призначено до проекту');

        // Перевірка, що проект має призначеного студента
        $project->refresh();
        $this->assertNotNull($project->assigned_to);
        $this->assertContains($project->assigned_to, [$student1->id, $student2->id]);

        // Перевірка, що всі заявки на цей проект видалені
        $this->assertDatabaseMissing('offers', ['project_id' => $project->id]);
    }

    public function test_random_assignment_fails_when_no_offers_exist()
    {
        // Створення викладача
        $teacher = User::factory()->create([
            'email' => 'teacher@uzhnu.edu.ua',
            'course_number' => null,
        ]);
        $teacher->assignRole('teacher');

        // Створення категорії та події
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create([
            'category_id' => $category->id,
        ]);

        // Створення наукового керівника
        $supervisor = Supervisor::factory()->create([
            'user_id' => $teacher->id,
            'event_id' => $event->id,
            'slot_count' => 2,
        ]);

        // Створення проекту без заявок
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => null,
        ]);

        // Авторизація як викладач
        $this->actingAs($teacher);

        // Спроба випадкового призначення
        $response = $this->post(route('teacher.offers.assign-random', ['project' => $project->id]));

        // Перевірка помилки
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Немає заявок для випадкового призначення');

        // Перевірка, що проект не має призначеного студента
        $project->refresh();
        $this->assertNull($project->assigned_to);
    }

    public function test_random_assignment_fails_when_project_already_assigned()
    {
        // Створення викладача та студентів
        $teacher = User::factory()->create([
            'email' => 'teacher@uzhnu.edu.ua',
            'course_number' => null,
        ]);
        $teacher->assignRole('teacher');

        $student1 = User::factory()->create([
            'email' => 'student1@student.uzhnu.edu.ua',
            'course_number' => 1,
        ]);
        $student1->assignRole('student');

        $student2 = User::factory()->create([
            'email' => 'student2@student.uzhnu.edu.ua',
            'course_number' => 1,
        ]);
        $student2->assignRole('student');

        // Створення категорії та події
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create([
            'category_id' => $category->id,
        ]);

        // Створення наукового керівника
        $supervisor = Supervisor::factory()->create([
            'user_id' => $teacher->id,
            'event_id' => $event->id,
            'slot_count' => 2,
        ]);

        // Створення проекту з уже призначеним студентом
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => $student1->id,
        ]);

        // Створення заявки
        Offer::create([
            'project_id' => $project->id,
            'student_id' => $student2->id,
        ]);

        // Авторизація як викладач
        $this->actingAs($teacher);

        // Спроба випадкового призначення
        $response = $this->post(route('teacher.offers.assign-random', ['project' => $project->id]));

        // Перевірка помилки
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Цей проект вже має призначеного студента');

        // Перевірка, що проект все ще має того самого студента
        $project->refresh();
        $this->assertEquals($student1->id, $project->assigned_to);
    }

    public function test_unauthorized_user_cannot_randomly_assign_student()
    {
        // Створення викладача та студента
        $teacher = User::factory()->create([
            'email' => 'teacher@uzhnu.edu.ua',
            'course_number' => null,
        ]);
        $teacher->assignRole('teacher');

        $unauthorizedTeacher = User::factory()->create([
            'email' => 'other@uzhnu.edu.ua',
            'course_number' => null,
        ]);
        $unauthorizedTeacher->assignRole('teacher');

        $student = User::factory()->create([
            'email' => 'student@student.uzhnu.edu.ua',
            'course_number' => 1,
        ]);
        $student->assignRole('student');

        // Створення категорії та події
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create([
            'category_id' => $category->id,
        ]);

        // Створення наукового керівника
        $supervisor = Supervisor::factory()->create([
            'user_id' => $teacher->id,
            'event_id' => $event->id,
            'slot_count' => 2,
        ]);

        // Створення проекту
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => null,
        ]);

        // Створення заявки
        Offer::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
        ]);

        // Авторизація як неавторизований викладач
        $this->actingAs($unauthorizedTeacher);

        // Спроба випадкового призначення
        $response = $this->post(route('teacher.offers.assign-random', ['project' => $project->id]));

        // Перевірка помилки доступу
        $response->assertStatus(403);

        // Перевірка, що проект не має призначеного студента
        $project->refresh();
        $this->assertNull($project->assigned_to);
    }
}
