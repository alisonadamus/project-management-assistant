<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Subevent;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class SubeventUpdateTest extends TestCase
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
    public function teacher_can_update_subevent_without_deleting_it()
    {
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        $supervisor = Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);
        
        $subevent = Subevent::factory()->create([
            'event_id' => $event->id,
            'name' => 'Original Subevent Name',
            'description' => 'Original description'
        ]);

        $updateData = [
            'name' => 'Updated Subevent Name',
            'description' => 'Updated description',
            'start_date' => $event->start_date->format('Y-m-d\TH:i'),
            'end_date' => $event->start_date->addDays(2)->format('Y-m-d\TH:i'),
            'bg_color' => '#FF5733',
            'fg_color' => '#FFFFFF',
        ];

        $response = $this->actingAs($teacher)->put(route('teacher.subevents.update', [$event, $subevent]), $updateData);

        // Перевіряємо, що підподія була оновлена, а не видалена
        $this->assertDatabaseHas('subevents', [
            'id' => $subevent->id,
            'name' => 'Updated Subevent Name',
            'description' => 'Updated description',
            'bg_color' => '#FF5733',
            'fg_color' => '#FFFFFF',
        ]);

        // Перевіряємо редирект
        $response->assertRedirect(route('events.show', $event));
    }

    /** @test */
    public function teacher_can_delete_subevent_using_delete_form()
    {
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        $supervisor = Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);
        
        $subevent = Subevent::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($teacher)->delete(route('teacher.subevents.destroy', [$event, $subevent]));

        // Перевіряємо, що підподія була видалена
        $this->assertDatabaseMissing('subevents', ['id' => $subevent->id]);
        
        // Перевіряємо редирект
        $response->assertRedirect(route('events.show', $event));
    }

    /** @test */
    public function update_and_delete_forms_are_separate_for_subevents()
    {
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        $supervisor = Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);
        
        $subevent = Subevent::factory()->create(['event_id' => $event->id]);

        // Отримуємо сторінку редагування
        $response = $this->actingAs($teacher)->get(route('teacher.subevents.edit', [$event, $subevent]));
        
        $response->assertStatus(200);
        
        // Перевіряємо, що є дві окремі форми
        $content = $response->getContent();
        
        // Форма оновлення
        $this->assertStringContainsString('action="' . route('teacher.subevents.update', [$event, $subevent]) . '"', $content);
        $this->assertStringContainsString('method="POST"', $content);
        $this->assertStringContainsString('@method(\'PUT\')', $content);
        
        // Форма видалення (прихована)
        $this->assertStringContainsString('id="delete-subevent-form"', $content);
        $this->assertStringContainsString('action="' . route('teacher.subevents.destroy', [$event, $subevent]) . '"', $content);
        $this->assertStringContainsString('@method(\'DELETE\')', $content);
        $this->assertStringContainsString('class="hidden"', $content);
    }
}
