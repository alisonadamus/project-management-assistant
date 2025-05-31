<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'freezing_period' => $this->faker->numberBetween(3, 5),
            'course_number' => $this->faker->numberBetween(1, 4),
            'period' => $this->faker->numberBetween(1, 5),
            'attachments' => json_encode([
                'file1' => $this->faker->url(),
                'file2' => $this->faker->url(),
            ]),
        ];
    }
}
