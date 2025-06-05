<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Subevent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subevent>
 */
class SubeventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 week', '+1 month');

        return [
            'event_id' => Event::factory(),
            'depends_on' => null,
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->text(200),
            'start_date' => $startDate,
            'end_date' => $this->faker->dateTimeBetween($startDate, '+2 months'),
            'bg_color' => $this->faker->hexColor(),
            'fg_color' => $this->faker->hexColor(),
        ];
    }
}
