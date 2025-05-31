<?php

namespace Tests\Unit\Models;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_valid_data()
    {
        $category = Category::factory()->create();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    /** @test */
    public function it_uses_ulids_for_ids()
    {
        $category = Category::factory()->create();
        
        $this->assertIsString($category->id);
        $this->assertEquals(26, strlen($category->id));
    }

    /** @test */
    public function it_can_belong_to_many_subjects()
    {
        $category = Category::factory()->create();
        $subject = Subject::factory()->create();

        $category->subjects()->attach($subject);

        $this->assertCount(1, $category->subjects);
        $this->assertEquals($subject->id, $category->subjects->first()->id);
    }

    /** @test */
    public function it_can_filter_by_course_number()
    {
        $category1 = Category::factory()->create(['course_number' => 1]);
        $category2 = Category::factory()->create(['course_number' => 2]);
        
        $results = Category::byCourseNumber(1)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($category1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_search_by_name()
    {
        $category1 = Category::factory()->create(['name' => 'Programming']);
        $category2 = Category::factory()->create(['name' => 'Mathematics']);
        
        $results = Category::searchByName('Program')->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($category1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_with_subject()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $subject = Subject::factory()->create();
        
        $category1->subjects()->attach($subject);
        
        $results = Category::withSubject($subject->id)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($category1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_min_freezing_period()
    {
        $category1 = Category::factory()->create(['freezing_period' => 10]);
        $category2 = Category::factory()->create(['freezing_period' => 5]);
        
        $results = Category::minFreezingPeriod(7)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($category1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_max_freezing_period()
    {
        $category1 = Category::factory()->create(['freezing_period' => 10]);
        $category2 = Category::factory()->create(['freezing_period' => 5]);
        
        $results = Category::maxFreezingPeriod(7)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($category2->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_min_period()
    {
        $category1 = Category::factory()->create(['period' => 10]);
        $category2 = Category::factory()->create(['period' => 5]);
        
        $results = Category::minPeriod(7)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($category1->id, $results->first()->id);
    }

    /** @test */
    public function it_can_filter_by_max_period()
    {
        $category1 = Category::factory()->create(['period' => 10]);
        $category2 = Category::factory()->create(['period' => 5]);
        
        $results = Category::maxPeriod(7)->get();
        
        $this->assertCount(1, $results);
        $this->assertEquals($category2->id, $results->first()->id);
    }

    /** @test */
    public function it_casts_attachments_as_array()
    {
        $attachments = ['file1' => 'path/to/file1', 'file2' => 'path/to/file2'];
        $category = Category::factory()->create(['attachments' => $attachments]);
        
        $this->assertIsArray($category->attachments);
        $this->assertEquals($attachments, $category->attachments);
    }
}
