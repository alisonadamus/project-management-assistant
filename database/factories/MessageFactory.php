<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => ProjectFactory::new(),
            'sender_id' => UserFactory::new(),
            'message' => $this->faker->paragraph(),
            'is_read' => false,
        ];
    }
}
