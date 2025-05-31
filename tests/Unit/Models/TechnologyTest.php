<?php

namespace Tests\Unit\Models;

use Alison\ProjectManagementAssistant\Models\Technology;
use Alison\ProjectManagementAssistant\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnologyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_valid_data()
    {
        $technology = Technology::factory()->create();

        $this->assertDatabaseHas('technologies', [
            'id' => $technology->id,
            'name' => $technology->name,
        ]);
    }

    /** @test */
    public function it_uses_ulids_for_ids()
    {
        $technology = Technology::factory()->create();
        
        $this->assertIsString($technology->id);
        $this->assertEquals(26, strlen($technology->id));
    }

    /** @test */
    public function it_can_belong_to_many_projects()
    {
        $technology = Technology::factory()->create();
        $project = Project::factory()->create();

        $technology->projects()->attach($project);

        $this->assertCount(1, $technology->projects);
        $this->assertEquals($project->id, $technology->projects->first()->id);
    }

    /** @test */
    public function it_can_filter_by_name()
    {
        $technology1 = Technology::factory()->create(['name' => 'Laravel']);
        $technology2 = Technology::factory()->create(['name' => 'React']);
        
        $results = Technology::byName('Lara')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($technology1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_project()
    {
        $technology1 = Technology::factory()->create();
        $technology2 = Technology::factory()->create();
        $project = Project::factory()->create();
        
        $technology1->projects()->attach($project);
        
        $results = Technology::byProject($project->id)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($technology1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_with_link()
    {
        $technology1 = Technology::factory()->create(['link' => 'https://example.com']);
        $technology2 = Technology::factory()->create(['link' => null]);
        
        $results = Technology::withLink()->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($technology1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_without_link()
    {
        $technology1 = Technology::factory()->create(['link' => 'https://example.com']);
        $technology2 = Technology::factory()->create(['link' => null]);
        $technology3 = Technology::factory()->create(['link' => '']);
        
        $results = Technology::withoutLink()->get();
        
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($technology2));
        $this->assertTrue($results->contains($technology3));
    }

    /** @test */
    public function it_can_search_by_description()
    {
        $technology1 = Technology::factory()->create(['description' => 'A PHP framework for web artisans']);
        $technology2 = Technology::factory()->create(['description' => 'A JavaScript library for building user interfaces']);
        
        $results = Technology::searchByDescription('PHP framework')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($technology1->id, $results->first()->id);
    }
}
