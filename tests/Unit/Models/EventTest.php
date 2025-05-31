<?php

namespace Tests\Unit\Models;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_valid_data()
    {
        $event = Event::factory()->create();

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => $event->name,
        ]);
    }

    /** @test */
    public function it_uses_ulids_for_ids()
    {
        $event = Event::factory()->create();
        
        $this->assertIsString($event->id);
        $this->assertEquals(26, strlen($event->id));
    }

    /** @test */
    public function it_belongs_to_a_category()
    {
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        
        $this->assertEquals($category->id, $event->category->id);
    }

    /** @test */
    public function it_has_many_supervisors()
    {
        $event = Event::factory()->create();
        $supervisor = Supervisor::factory()->create(['event_id' => $event->id]);
        
        $this->assertCount(1, $event->supervisors);
        $this->assertEquals($supervisor->id, $event->supervisors->first()->id);
    }

    /** @test */
    public function it_has_many_projects()
    {
        $event = Event::factory()->create();
        $project = Project::factory()->create(['event_id' => $event->id]);
        
        $this->assertCount(1, $event->projects);
        $this->assertEquals($project->id, $event->projects->first()->id);
    }

    /** @test */
    public function it_can_filter_by_category()
    {
        $category = Category::factory()->create();
        $event1 = Event::factory()->create(['category_id' => $category->id]);
        $event2 = Event::factory()->create();
        
        $results = Event::byCategory($category->id)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($event1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_active_events()
    {
        $event1 = Event::factory()->create(['end_date' => now()->addDays(5)]);
        $event2 = Event::factory()->create(['end_date' => now()->subDays(5)]);
        
        $results = Event::active()->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($event1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_past_events()
    {
        $event1 = Event::factory()->create(['end_date' => now()->addDays(5)]);
        $event2 = Event::factory()->create(['end_date' => now()->subDays(5)]);
        
        $results = Event::past()->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($event2->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_upcoming_events()
    {
        $event1 = Event::factory()->create(['start_date' => now()->addDays(5)]);
        $event2 = Event::factory()->create(['start_date' => now()->subDays(5)]);
        
        $results = Event::upcoming()->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($event1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_search_by_name()
    {
        $event1 = Event::factory()->create(['name' => 'Programming Contest']);
        $event2 = Event::factory()->create(['name' => 'Math Competition']);
        
        $results = Event::searchByName('Program')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($event1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_between_dates()
    {
        $event1 = Event::factory()->create(['start_date' => '2023-01-15']);
        $event2 = Event::factory()->create(['start_date' => '2023-02-15']);
        $event3 = Event::factory()->create(['start_date' => '2023-03-15']);
        
        $results = Event::betweenDates('2023-01-10', '2023-02-20')->get();
        
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($event1));
        $this->assertTrue($results->contains($event2));
    }
}
