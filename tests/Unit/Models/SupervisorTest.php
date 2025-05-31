<?php

namespace Tests\Unit\Models;

use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupervisorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_valid_data()
    {
        $supervisor = Supervisor::factory()->create();

        $this->assertDatabaseHas('supervisors', [
            'id' => $supervisor->id,
            'slot_count' => $supervisor->slot_count,
        ]);
    }

    /** @test */
    public function it_uses_ulids_for_ids()
    {
        $supervisor = Supervisor::factory()->create();
        
        $this->assertIsString($supervisor->id);
        $this->assertEquals(26, strlen($supervisor->id));
    }

    /** @test */
    public function it_belongs_to_an_event()
    {
        $event = Event::factory()->create();
        $supervisor = Supervisor::factory()->create(['event_id' => $event->id]);
        
        $this->assertEquals($event->id, $supervisor->event->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $supervisor = Supervisor::factory()->create(['user_id' => $user->id]);
        
        $this->assertEquals($user->id, $supervisor->user->id);
    }

    /** @test */
    public function it_has_many_projects()
    {
        $supervisor = Supervisor::factory()->create();
        $project = Project::factory()->create(['supervisor_id' => $supervisor->id]);
        
        $this->assertCount(1, $supervisor->projects);
        $this->assertEquals($project->id, $supervisor->projects->first()->id);
    }

    /** @test */
    public function it_can_filter_by_event()
    {
        $event = Event::factory()->create();
        $supervisor1 = Supervisor::factory()->create(['event_id' => $event->id]);
        $supervisor2 = Supervisor::factory()->create();
        
        $results = Supervisor::byEvent($event->id)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($supervisor1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_user()
    {
        $user = User::factory()->create();
        $supervisor1 = Supervisor::factory()->create(['user_id' => $user->id]);
        $supervisor2 = Supervisor::factory()->create();
        
        $results = Supervisor::byUser($user->id)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($supervisor1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_search_by_note()
    {
        $supervisor1 = Supervisor::factory()->create(['note' => 'Experienced in web development']);
        $supervisor2 = Supervisor::factory()->create(['note' => 'Specializes in mobile apps']);
        
        $results = Supervisor::searchByNote('web development')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($supervisor1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_active_event()
    {
        $activeEvent = Event::factory()->create(['end_date' => now()->addDays(5)]);
        $pastEvent = Event::factory()->create(['end_date' => now()->subDays(5)]);
        
        $supervisor1 = Supervisor::factory()->create(['event_id' => $activeEvent->id]);
        $supervisor2 = Supervisor::factory()->create(['event_id' => $pastEvent->id]);
        
        $results = Supervisor::activeEvent()->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($supervisor1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_slot_count()
    {
        $supervisor1 = Supervisor::factory()->create(['slot_count' => 10]);
        $supervisor2 = Supervisor::factory()->create(['slot_count' => 15]);
        
        $results = Supervisor::bySlotCount(10)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($supervisor1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_min_slot_count()
    {
        $supervisor1 = Supervisor::factory()->create(['slot_count' => 5]);
        $supervisor2 = Supervisor::factory()->create(['slot_count' => 10]);
        $supervisor3 = Supervisor::factory()->create(['slot_count' => 15]);
        
        $results = Supervisor::minSlotCount(10)->get();
        
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($supervisor2));
        $this->assertTrue($results->contains($supervisor3));
    }

    /** @test */
    public function it_can_filter_by_max_slot_count()
    {
        $supervisor1 = Supervisor::factory()->create(['slot_count' => 5]);
        $supervisor2 = Supervisor::factory()->create(['slot_count' => 10]);
        $supervisor3 = Supervisor::factory()->create(['slot_count' => 15]);
        
        $results = Supervisor::maxSlotCount(10)->get();
        
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($supervisor1));
        $this->assertTrue($results->contains($supervisor2));
    }
}
