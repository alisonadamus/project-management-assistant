<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Offer;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Project::query()->count() === 0) {
            $this->call(ProjectSeeder::class);
        }

        if (User::query()->count() === 0) {
            $this->call(UserSeeder::class);
        }

        $projects = Project::all()->where('assigned_to', null);
        $users = User::role('student')->get();

        Offer::factory()->count(30)->create([
            'project_id' => fn() => $projects->random()->id,
            'student_id' => fn() => $users->random()->id,
        ]);
    }
}
