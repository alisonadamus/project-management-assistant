<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        Subject::query()->insert([
        $subjects = [
            [
                'slug' => 'basic-programming-and-algorithmic-languages',
                'name' => 'Основи програмування та алгоритмічні мови',
                'course_number' => 2,
                'description' => 'Основи програмування та алгоритмічні мови',
                'image' => null,
            ],
            [
                'slug' => 'databases',
                'name' => 'Бази даних',
                'course_number' => 3,
                'description' => 'Бази даних',
                'image' => null,
            ],
            [
                'slug' => 'object-oriented-programming',
                'name' => 'Oб\'єктно-орієнтоване програмування',
                'course_number' => 2,
                'description' => 'Oб\'єктно-орієнтоване програмування',
                'image' => null,
            ],
            [
                'slug' => 'mobile-app-programming',
                'name' => 'Програмування мобільних додатків',
                'course_number' => 3,
                'description' => 'Програмування мобільних додатків',
                'image' => null,
            ],
            [
                'slug' => 'web-development',
                'name' => 'Веб-розробка',
                'course_number' => 4,
                'description' => 'Веб-розробка',
                'image' => null,
            ],
        ];

        collect($subjects)->each(function ($subject) {
            Subject::query()->create($subject);
        });
    }
}
