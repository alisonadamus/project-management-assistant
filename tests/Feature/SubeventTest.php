<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Subevent;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class SubeventTest extends TestCase
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
    public function teacher_supervisor_can_create_subevent()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        $supervisor = Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);

        $response = $this->actingAs($teacher)->get(route('teacher.subevents.create', $event));
        $response->assertStatus(200);
        $response->assertSee('Створення підподії');

        $subeventData = [
            'name' => 'Тестова підподія',
            'description' => 'Опис тестової підподії',
            'start_date' => $event->start_date->addDay()->format('Y-m-d\TH:i'),
            'end_date' => $event->start_date->addDays(2)->format('Y-m-d\TH:i'),
            'bg_color' => '#FF5733',
            'fg_color' => '#FFFFFF',
        ];

        $response = $this->actingAs($teacher)->post(route('teacher.subevents.store', $event), $subeventData);
        $response->assertRedirect(route('events.show', $event));
        
        $this->assertDatabaseHas('subevents', [
            'name' => 'Тестова підподія',
            'event_id' => $event->id,
        ]);
    }

    /** @test */
    public function non_supervisor_teacher_cannot_create_subevent()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($teacher)->get(route('teacher.subevents.create', $event));
        $response->assertStatus(403);
    }

    /** @test */
    public function teacher_supervisor_can_edit_subevent()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        $supervisor = Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);
        $subevent = Subevent::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($teacher)->get(route('teacher.subevents.edit', [$event, $subevent]));
        $response->assertStatus(200);
        $response->assertSee('Редагування підподії');

        $updateData = [
            'name' => 'Оновлена підподія',
            'description' => 'Оновлений опис',
            'start_date' => $event->start_date->addDay()->format('Y-m-d\TH:i'),
            'end_date' => $event->start_date->addDays(3)->format('Y-m-d\TH:i'),
            'bg_color' => '#33FF57',
            'fg_color' => '#000000',
        ];

        $response = $this->actingAs($teacher)->put(route('teacher.subevents.update', [$event, $subevent]), $updateData);
        $response->assertRedirect(route('events.show', $event));
        
        $this->assertDatabaseHas('subevents', [
            'id' => $subevent->id,
            'name' => 'Оновлена підподія',
        ]);
    }

    /** @test */
    public function teacher_supervisor_can_delete_subevent()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        $supervisor = Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);
        $subevent = Subevent::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($teacher)->delete(route('teacher.subevents.destroy', [$event, $subevent]));
        $response->assertRedirect(route('events.show', $event));
        
        $this->assertDatabaseMissing('subevents', [
            'id' => $subevent->id,
        ]);
    }

    /** @test */
    public function cannot_delete_subevent_with_dependencies()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        $supervisor = Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);
        
        $parentSubevent = Subevent::factory()->create(['event_id' => $event->id]);
        $childSubevent = Subevent::factory()->create([
            'event_id' => $event->id,
            'depends_on' => $parentSubevent->id
        ]);

        $response = $this->actingAs($teacher)->delete(route('teacher.subevents.destroy', [$event, $parentSubevent]));
        $response->assertRedirect();
        $response->assertSessionHasErrors(['delete']);
        
        $this->assertDatabaseHas('subevents', [
            'id' => $parentSubevent->id,
        ]);
    }

    /** @test */
    public function gantt_data_endpoint_returns_correct_format()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $subevent1 = Subevent::factory()->create([
            'event_id' => $event->id,
            'name' => 'Підподія 1',
            'start_date' => now(),
            'end_date' => now()->addDays(2),
            'bg_color' => '#FF5733',
        ]);
        
        $subevent2 = Subevent::factory()->create([
            'event_id' => $event->id,
            'name' => 'Підподія 2',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(3),
            'depends_on' => $subevent1->id,
            'bg_color' => '#33FF57',
        ]);

        $response = $this->actingAs($user)->get(route('subevents.gantt-data', $event));
        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertCount(2, $data);
        
        $this->assertEquals($subevent1->id, $data[0]['id']);
        $this->assertEquals('Підподія 1', $data[0]['name']);
        $this->assertEquals('#FF5733', $data[0]['bg_color']);
        $this->assertArrayHasKey('fg_color', $data[0]);
        $this->assertEmpty($data[0]['dependencies']);

        $this->assertEquals($subevent2->id, $data[1]['id']);
        $this->assertEquals('Підподія 2', $data[1]['name']);
        $this->assertEquals('#33FF57', $data[1]['bg_color']);
        $this->assertArrayHasKey('fg_color', $data[1]);
        $this->assertEquals([$subevent1->id], $data[1]['dependencies']);
    }

    /** @test */
    public function student_can_view_gantt_data_for_accessible_event()
    {
        $student = User::factory()->create(['course_number' => 2]);
        $student->assignRole('student');
        
        $category = Category::factory()->create(['course_number' => 2]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        $subevent = Subevent::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($student)->get(route('subevents.gantt-data', $event));
        $response->assertStatus(200);
    }

    /** @test */
    public function student_cannot_view_gantt_data_for_inaccessible_event()
    {
        $student = User::factory()->create(['course_number' => 1]);
        $student->assignRole('student');
        
        $category = Category::factory()->create(['course_number' => 2]);
        $event = Event::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($student)->get(route('subevents.gantt-data', $event));
        $response->assertStatus(403);
    }

    /** @test */
    public function subevent_validation_prevents_dates_outside_event_range()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'start_date' => now(),
            'end_date' => now()->addDays(10)
        ]);
        $supervisor = Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);

        $subeventData = [
            'name' => 'Тестова підподія',
            'start_date' => $event->start_date->subDay()->format('Y-m-d\TH:i'), // До початку події
            'end_date' => $event->end_date->addDay()->format('Y-m-d\TH:i'), // Після завершення події
        ];

        $response = $this->actingAs($teacher)->post(route('teacher.subevents.store', $event), $subeventData);
        $response->assertSessionHasErrors(['start_date']);
    }
}
