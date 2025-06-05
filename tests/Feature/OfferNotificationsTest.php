<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Offer;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Notifications\NewOfferNotification;
use Alison\ProjectManagementAssistant\Notifications\OfferApprovedNotification;
use Alison\ProjectManagementAssistant\Notifications\OfferRejectedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OfferNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Створюємо ролі
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'student']);

        // Створюємо дозволи
        Permission::create(['name' => 'create offers']);
        Permission::create(['name' => 'view offers']);
        Permission::create(['name' => 'delete offers']);

        // Призначаємо дозволи ролям
        $studentRole = Role::findByName('student');
        $teacherRole = Role::findByName('teacher');

        $studentRole->givePermissionTo(['create offers', 'view offers', 'delete offers']);
        $teacherRole->givePermissionTo(['view offers']);
    }

    /** @test */
    public function it_sends_notification_to_supervisor_when_student_creates_offer()
    {
        Notification::fake();

        // Створюємо дані
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');
        
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
            'slot_count' => 2,
        ]);
        
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => null,
        ]);

        $student = User::factory()->create(['course_number' => 1]);
        $student->assignRole('student');

        // Студент подає заявку
        $response = $this->actingAs($student)->post(route('offers.store', $project));

        $response->assertRedirect();
        
        // Перевіряємо, що повідомлення надіслано викладачу
        Notification::assertSentTo($teacher, NewOfferNotification::class);
    }

    /** @test */
    public function it_sends_approval_notification_when_teacher_approves_offer()
    {
        Notification::fake();

        // Створюємо дані
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');
        
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
            'slot_count' => 2,
        ]);
        
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => null,
        ]);

        $student = User::factory()->create(['course_number' => 1]);
        $student->assignRole('student');

        // Створюємо заявку
        $offer = Offer::factory()->create([
            'project_id' => $project->id,
            'student_id' => $student->id,
        ]);

        // Викладач затверджує заявку
        $response = $this->actingAs($teacher)->post(
            route('teacher.offers.approve', ['project' => $project->id, 'studentId' => $student->id])
        );

        $response->assertRedirect();
        
        // Перевіряємо, що повідомлення надіслано студенту
        Notification::assertSentTo($student, OfferApprovedNotification::class);
    }

    /** @test */
    public function it_sends_rejection_notification_when_teacher_rejects_offer()
    {
        Notification::fake();

        // Створюємо дані
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');
        
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
            'slot_count' => 2,
        ]);
        
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => null,
        ]);

        $student = User::factory()->create(['course_number' => 1]);
        $student->assignRole('student');

        // Створюємо заявку
        $offer = Offer::factory()->create([
            'project_id' => $project->id,
            'student_id' => $student->id,
        ]);

        // Викладач відхиляє заявку
        $response = $this->actingAs($teacher)->delete(
            route('teacher.offers.reject', ['project' => $project->id, 'studentId' => $student->id])
        );

        $response->assertRedirect();
        
        // Перевіряємо, що повідомлення надіслано студенту
        Notification::assertSentTo($student, OfferRejectedNotification::class, function ($notification) {
            return $notification->reason === 'manual';
        });
    }

    /** @test */
    public function it_sends_rejection_notifications_to_other_students_when_approving_offer()
    {
        Notification::fake();

        // Створюємо дані
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');
        
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
            'slot_count' => 2,
        ]);
        
        $project = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => null,
        ]);

        $student1 = User::factory()->create(['course_number' => 1]);
        $student1->assignRole('student');
        
        $student2 = User::factory()->create(['course_number' => 1]);
        $student2->assignRole('student');

        // Створюємо заявки від обох студентів
        Offer::factory()->create([
            'project_id' => $project->id,
            'student_id' => $student1->id,
        ]);
        
        Offer::factory()->create([
            'project_id' => $project->id,
            'student_id' => $student2->id,
        ]);

        // Викладач затверджує заявку першого студента
        $response = $this->actingAs($teacher)->post(
            route('teacher.offers.approve', ['project' => $project->id, 'studentId' => $student1->id])
        );

        $response->assertRedirect();
        
        // Перевіряємо повідомлення
        Notification::assertSentTo($student1, OfferApprovedNotification::class);
        Notification::assertSentTo($student2, OfferRejectedNotification::class, function ($notification) {
            return $notification->reason === 'other_student_approved';
        });
    }

    /** @test */
    public function it_sends_rejection_notifications_when_supervisor_slots_are_full()
    {
        Notification::fake();

        // Створюємо дані з лімітом 1 слот
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');
        
        $supervisor = Supervisor::factory()->create([
            'event_id' => $event->id,
            'user_id' => $teacher->id,
            'slot_count' => 1, // Тільки 1 слот
        ]);
        
        $project1 = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => null,
        ]);

        $project2 = Project::factory()->create([
            'event_id' => $event->id,
            'supervisor_id' => $supervisor->id,
            'assigned_to' => null,
        ]);

        $student1 = User::factory()->create(['course_number' => 1]);
        $student1->assignRole('student');
        
        $student2 = User::factory()->create(['course_number' => 1]);
        $student2->assignRole('student');

        // Створюємо заявки
        Offer::factory()->create([
            'project_id' => $project1->id,
            'student_id' => $student1->id,
        ]);
        
        Offer::factory()->create([
            'project_id' => $project2->id,
            'student_id' => $student2->id,
        ]);

        // Викладач затверджує першу заявку (заповнює єдиний слот)
        $response = $this->actingAs($teacher)->post(
            route('teacher.offers.approve', ['project' => $project1->id, 'studentId' => $student1->id])
        );

        $response->assertRedirect();
        
        // Перевіряємо повідомлення
        Notification::assertSentTo($student1, OfferApprovedNotification::class);
        Notification::assertSentTo($student2, OfferRejectedNotification::class, function ($notification) {
            return $notification->reason === 'supervisor_slots_full';
        });
    }
}
