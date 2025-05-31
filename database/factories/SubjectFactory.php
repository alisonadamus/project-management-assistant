<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            'course_number' => $this->faker->numberBetween(1, 4),
            'description' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl(640, 480, 'education'),
        ];

    }
}
