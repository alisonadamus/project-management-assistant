<?php

namespace Tests\Unit\Models;

use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\Technology;
use Alison\ProjectManagementAssistant\Models\User;
use Alison\ProjectManagementAssistant\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_valid_data()
    {
        $project = Project::factory()->create();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => $project->name,
        ]);
    }

    /** @test */
    public function it_uses_ulids_for_ids()
    {
        $project = Project::factory()->create();
        
        $this->assertIsString($project->id);
        $this->assertEquals(26, strlen($project->id));
    }

    /** @test */
    public function it_belongs_to_an_event()
    {
        $event = Event::factory()->create();
        $project = Project::factory()->create(['event_id' => $event->id]);
        
        $this->assertEquals($event->id, $project->event->id);
    }

    /** @test */
    public function it_belongs_to_a_supervisor()
    {
        $supervisor = Supervisor::factory()->create();
        $project = Project::factory()->create(['supervisor_id' => $supervisor->id]);
        
        $this->assertEquals($supervisor->id, $project->supervisor->id);
    }

    /** @test */
    public function it_belongs_to_an_assigned_user()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['assigned_to' => $user->id]);
        
        $this->assertEquals($user->id, $project->assignedTo->id);
    }

    /** @test */
    public function it_can_belong_to_many_technologies()
    {
        $project = Project::factory()->create();
        $technology = Technology::factory()->create();

        $project->technologies()->attach($technology);

        $this->assertCount(1, $project->technologies);
        $this->assertEquals($technology->id, $project->technologies->first()->id);
    }

    /** @test */
    public function it_has_many_offers()
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        
        Offer::factory()->create([
            'project_id' => $project->id,
            'student_id' => $user->id
        ]);
        
        $this->assertCount(1, $project->offers);
    }

    /** @test */
    public function it_can_filter_by_event()
    {
        $event = Event::factory()->create();
        $project1 = Project::factory()->create(['event_id' => $event->id]);
        $project2 = Project::factory()->create();
        
        $results = Project::byEvent($event->id)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($project1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_supervisor()
    {
        $supervisor = Supervisor::factory()->create();
        $project1 = Project::factory()->create(['supervisor_id' => $supervisor->id]);
        $project2 = Project::factory()->create();
        
        $results = Project::bySupervisor($supervisor->id)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($project1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_assigned_student()
    {
        $user = User::factory()->create();
        $project1 = Project::factory()->create(['assigned_to' => $user->id]);
        $project2 = Project::factory()->create();
        
        $results = Project::byAssignedStudent($user->id)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($project1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_name()
    {
        $project1 = Project::factory()->create(['name' => 'Laravel Project']);
        $project2 = Project::factory()->create(['name' => 'React Project']);
        
        $results = Project::byName('Laravel')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($project1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_without_supervisor()
    {
        $project1 = Project::factory()->create(['supervisor_id' => null]);
        $project2 = Project::factory()->create(['supervisor_id' => Supervisor::factory()->create()->id]);
        
        $results = Project::withoutSupervisor()->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($project1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_with_supervisor()
    {
        $project1 = Project::factory()->create(['supervisor_id' => null]);
        $project2 = Project::factory()->create(['supervisor_id' => Supervisor::factory()->create()->id]);
        
        $results = Project::withSupervisor()->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($project2->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_with_assigned_to()
    {
        $project1 = Project::factory()->create(['assigned_to' => null]);
        $project2 = Project::factory()->create(['assigned_to' => User::factory()->create()->id]);
        
        $results = Project::withAssignedTo()->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($project2->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_without_assigned_to()
    {
        $project1 = Project::factory()->create(['assigned_to' => null]);
        $project2 = Project::factory()->create(['assigned_to' => User::factory()->create()->id]);
        
        $results = Project::withoutAssignedTo()->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($project1->id, $results->first()->id);
    }
}
