<?php

namespace Database\Factories;

use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'middle_name' => fake()->boolean(70) ? fake()->firstName() : null,
            'description' => fake()->boolean(80) ? fake()->text(200) : null,
            'avatar' => null,
            'course_number' => fake()->boolean(80) ? fake()->numberBetween(1, 6) : null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Dummy method to support tests that expect teams functionality.
     * This is a no-op since teams are not used in this application.
     */
    public function withPersonalTeam(): static
    {
        return $this->state(function (array $attributes) {
            return $attributes;
        });
    }
}
