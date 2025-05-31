<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->word();

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            'description' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl(640, 480, 'technology'),
            'link' => $this->faker->url(),
        ];
    }
}
