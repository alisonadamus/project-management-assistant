<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class EventUpdateTest extends TestCase
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
    public function teacher_can_update_event_without_deleting_it()
    {
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create([
            'category_id' => $category->id,
            'name' => 'Original Event Name',
            'description' => 'Original description'
        ]);
        
        // Додаємо викладача як наукового керівника
        Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);

        $updateData = [
            'name' => 'Updated Event Name',
            'description' => 'Updated description',
            'category_id' => $category->id,
            'start_date' => now()->format('Y-m-d\TH:i'),
            'end_date' => now()->addDays(7)->format('Y-m-d\TH:i'),
            'bg_color' => '#FF5733',
            'fg_color' => '#FFFFFF',
        ];

        $response = $this->actingAs($teacher)->put(route('teacher.events.update', $event), $updateData);

        // Перевіряємо, що подія була оновлена, а не видалена
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => 'Updated Event Name',
            'description' => 'Updated description',
            'bg_color' => '#FF5733',
            'fg_color' => '#FFFFFF',
        ]);

        // Перевіряємо редирект
        $response->assertRedirect(route('events.show', $event));
    }

    /** @test */
    public function teacher_can_delete_event_using_delete_form()
    {
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        // Додаємо викладача як наукового керівника
        Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);

        $response = $this->actingAs($teacher)->delete(route('teacher.events.destroy', $event));

        // Перевіряємо, що подія була видалена
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
        
        // Перевіряємо редирект
        $response->assertRedirect(route('events.index'));
    }

    /** @test */
    public function update_and_delete_forms_are_separate()
    {
        $teacher = User::factory()->create(['course_number' => null]);
        $teacher->assignRole('teacher');
        
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        // Додаємо викладача як наукового керівника
        Supervisor::factory()->create(['user_id' => $teacher->id, 'event_id' => $event->id]);

        // Отримуємо сторінку редагування
        $response = $this->actingAs($teacher)->get(route('teacher.events.edit', $event));
        
        $response->assertStatus(200);
        
        // Перевіряємо, що є дві окремі форми
        $content = $response->getContent();
        
        // Форма оновлення
        $this->assertStringContainsString('action="' . route('teacher.events.update', $event) . '"', $content);
        $this->assertStringContainsString('method="POST"', $content);
        $this->assertStringContainsString('@method(\'PUT\')', $content);
        
        // Форма видалення (прихована)
        $this->assertStringContainsString('id="delete-form"', $content);
        $this->assertStringContainsString('action="' . route('teacher.events.destroy', $event) . '"', $content);
        $this->assertStringContainsString('@method(\'DELETE\')', $content);
        $this->assertStringContainsString('class="hidden"', $content);
    }
}
