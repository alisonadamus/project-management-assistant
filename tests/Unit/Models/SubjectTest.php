<?php

namespace Tests\Unit\Models;

use Alison\ProjectManagementAssistant\Models\Subject;
use Alison\ProjectManagementAssistant\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_valid_data()
    {
        $subject = Subject::factory()->create();

        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => $subject->name,
        ]);
    }

    /** @test */
    public function it_uses_ulids_for_ids()
    {
        $subject = Subject::factory()->create();
        
        $this->assertIsString($subject->id);
        $this->assertEquals(26, strlen($subject->id));
    }

    /** @test */
    public function it_can_belong_to_many_categories()
    {
        $subject = Subject::factory()->create();
        $category = Category::factory()->create();

        $subject->categories()->attach($category);

        $this->assertCount(1, $subject->categories);
        $this->assertEquals($category->id, $subject->categories->first()->id);
    }

    /** @test */
    public function it_can_filter_by_name()
    {
        $subject1 = Subject::factory()->create(['name' => 'Programming']);
        $subject2 = Subject::factory()->create(['name' => 'Mathematics']);
        
        $results = Subject::byName('Program')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($subject1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_course()
    {
        $subject1 = Subject::factory()->create(['course_number' => 1]);
        $subject2 = Subject::factory()->create(['course_number' => 2]);
        
        $results = Subject::byCourse(1)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($subject1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_search_by_description()
    {
        $subject1 = Subject::factory()->create(['description' => 'This is about programming']);
        $subject2 = Subject::factory()->create(['description' => 'This is about mathematics']);
        
        $results = Subject::searchByDescription('programming')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($subject1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_search_by_category()
    {
        $subject1 = Subject::factory()->create();
        $subject2 = Subject::factory()->create();
        $category = Category::factory()->create();
        
        $subject1->categories()->attach($category);
        
        $results = Subject::byCategory($category->id)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($subject1->id, $results->first()->id);
    }
}
