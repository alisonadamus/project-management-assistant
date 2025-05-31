<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Database\Seeder;

class SupervisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::query()->count() === 0) {
            $this->call(UserSeeder::class);
        }

        if (Event::query()->count() === 0) {
            $this->call(EventSeeder::class);
        }

        $users = User::role('teacher')->get();
        $events = Event::all();
        Supervisor::factory()->count(20)->create([
            'event_id' => fn() => $events->random()->id,
            'user_id' => fn() => $users->random()->id,
        ]);
    }
}
