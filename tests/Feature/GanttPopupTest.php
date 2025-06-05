<?php

namespace Tests\Feature;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Subevent;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class GanttPopupTest extends TestCase
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
    public function gantt_api_returns_complete_subevent_data()
    {
        $user = User::factory()->create(['course_number' => 1]);
        $user->assignRole('student');
        
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $subevent = Subevent::factory()->create([
            'event_id' => $event->id,
            'name' => 'Test Subevent',
            'description' => 'Test Description',
            'start_date' => now(),
            'end_date' => now()->addDays(2),
            'bg_color' => '#FF5733',
            'fg_color' => '#FFFFFF',
        ]);

        $response = $this->actingAs($user)->getJson(route('subevents.gantt-data', $event));

        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertCount(1, $data);
        
        $subeventData = $data[0];
        
        // Перевіряємо основні поля
        $this->assertEquals($subevent->id, $subeventData['id']);
        $this->assertEquals('Test Subevent', $subeventData['name']);
        $this->assertEquals('Test Description', $subeventData['description']);
        
        // Перевіряємо дати
        $this->assertArrayHasKey('start', $subeventData);
        $this->assertArrayHasKey('end', $subeventData);
        $this->assertArrayHasKey('start_datetime', $subeventData);
        $this->assertArrayHasKey('end_datetime', $subeventData);
        
        // Перевіряємо кольори
        $this->assertEquals('#FF5733', $subeventData['bg_color']);
        $this->assertEquals('#FFFFFF', $subeventData['fg_color']);
        
        // Перевіряємо інші поля
        $this->assertArrayHasKey('progress', $subeventData);
        $this->assertArrayHasKey('dependencies', $subeventData);
        $this->assertArrayHasKey('custom_class', $subeventData);
        
        $this->assertEquals(0, $subeventData['progress']);
        $this->assertEquals([], $subeventData['dependencies']);
    }

    /** @test */
    public function gantt_api_handles_subevent_without_description()
    {
        $user = User::factory()->create(['course_number' => 1]);
        $user->assignRole('student');
        
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $subevent = Subevent::factory()->create([
            'event_id' => $event->id,
            'name' => 'Test Subevent',
            'description' => null, // Без опису
            'start_date' => now(),
            'end_date' => now()->addDays(2),
        ]);

        $response = $this->actingAs($user)->getJson(route('subevents.gantt-data', $event));

        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertCount(1, $data);
        $this->assertEquals('', $data[0]['description']); // Порожній рядок замість null
    }

    /** @test */
    public function gantt_api_formats_dates_correctly()
    {
        $user = User::factory()->create(['course_number' => 1]);
        $user->assignRole('student');
        
        $category = Category::factory()->create(['course_number' => 1]);
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $startDate = now()->startOfDay();
        $endDate = $startDate->copy()->addDays(3);
        
        $subevent = Subevent::factory()->create([
            'event_id' => $event->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $response = $this->actingAs($user)->getJson(route('subevents.gantt-data', $event));

        $response->assertStatus(200);
        $data = $response->json();
        
        $subeventData = $data[0];
        
        // Перевіряємо формат дат
        $this->assertEquals($startDate->format('Y-m-d'), $subeventData['start']);
        $this->assertEquals($endDate->format('Y-m-d'), $subeventData['end']);
        $this->assertEquals($startDate->format('Y-m-d H:i:s'), $subeventData['start_datetime']);
        $this->assertEquals($endDate->format('Y-m-d H:i:s'), $subeventData['end_datetime']);
    }
}
