<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(EventSeeder::class);
        $this->call(SubeventSeeder::class);
        $this->call(SupervisorSeeder::class);
        $this->call(TechnologySeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(MessageSeeder::class);
        $this->call(OfferSeeder::class);
    }
}
