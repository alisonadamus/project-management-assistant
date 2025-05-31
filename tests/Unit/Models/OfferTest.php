<?php

namespace Tests\Unit\Models;

use Alison\ProjectManagementAssistant\Models\Offer;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_valid_data()
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        $offer = Offer::factory()->create([
            'project_id' => $project->id,
            'student_id' => $user->id
        ]);

        $this->assertDatabaseHas('offers', [
            'project_id' => $project->id,
            'student_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_belongs_to_a_student()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create(['student_id' => $user->id]);

        $this->assertEquals($user->id, $offer->student->id);
    }

    /** @test */
    public function it_belongs_to_a_project()
    {
        $project = Project::factory()->create();
        $offer = Offer::factory()->create(['project_id' => $project->id]);

        $this->assertEquals($project->id, $offer->project->id);
    }

    /** @test */
    public function it_can_filter_by_student()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $offer1 = Offer::factory()->create(['student_id' => $user1->id]);
        $offer2 = Offer::factory()->create(['student_id' => $user2->id]);

        $results = Offer::byStudent($user1->id)->get();

        $this->assertCount(1, $results);
        $this->assertEquals($user1->id, $results->first()->student_id);
    }

    /** @test */
    public function it_can_filter_by_project()
    {
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        $offer1 = Offer::factory()->create(['project_id' => $project1->id]);
        $offer2 = Offer::factory()->create(['project_id' => $project2->id]);

        $results = Offer::byProject($project1->id)->get();

        $this->assertCount(1, $results);
        $this->assertEquals($project1->id, $results->first()->project_id);
    }

    /** @test */
    public function it_can_filter_recent_offers()
    {
        // Create offers with specific dates
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create an offer from 10 days ago
        $oldOffer = Offer::factory()->create([
            'project_id' => $project1->id,
            'student_id' => $user1->id,
            'created_at' => now()->subDays(10)
        ]);

        // Create a recent offer
        $recentOffer = Offer::factory()->create([
            'project_id' => $project2->id,
            'student_id' => $user2->id,
            'created_at' => now()->subDays(3)
        ]);

        $results = Offer::recent(7)->get();

        $this->assertCount(1, $results);
        $this->assertEquals($recentOffer->project_id, $results->first()->project_id);
        $this->assertEquals($recentOffer->student_id, $results->first()->student_id);
    }

    /** @test */
    public function it_can_get_latest_offers()
    {
        // Create projects and users for offers
        $projects = Project::factory()->count(10)->create();
        $users = User::factory()->count(10)->create();

        // Create 10 offers with different timestamps
        for ($i = 0; $i < 10; $i++) {
            Offer::factory()->create([
                'project_id' => $projects[$i]->id,
                'student_id' => $users[$i]->id,
                'created_at' => now()->subDays($i)
            ]);
        }

        $results = Offer::latestOffers(3)->get();

        $this->assertCount(3, $results);

        // Check that they're in the right order (newest first)
        $this->assertTrue($results[0]->created_at > $results[1]->created_at);
        $this->assertTrue($results[1]->created_at > $results[2]->created_at);
    }

    /** @test */
    public function it_can_filter_offers_from_today()
    {
        // Create projects and users
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create an offer from yesterday
        $yesterdayOffer = Offer::factory()->create([
            'project_id' => $project1->id,
            'student_id' => $user1->id,
            'created_at' => now()->subDay()
        ]);

        // Create an offer from today
        $todayOffer = Offer::factory()->create([
            'project_id' => $project2->id,
            'student_id' => $user2->id,
            'created_at' => now()
        ]);

        $results = Offer::today()->get();

        $this->assertCount(1, $results);
        $this->assertEquals($todayOffer->project_id, $results->first()->project_id);
        $this->assertEquals($todayOffer->student_id, $results->first()->student_id);
    }
}
