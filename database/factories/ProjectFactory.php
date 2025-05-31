<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);

        return [
            'event_id' => Event::factory(),
            'supervisor_id' => Supervisor::factory(),
            'assigned_to' => User::factory(),
            'slug' => Str::slug($name),
            'name' => $name,
            'appendix' => $this->faker->optional()->url(),
            'body' => $this->faker->paragraphs(3, true),
        ];
    }
}
