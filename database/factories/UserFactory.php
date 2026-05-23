<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->unique()->phoneNumber(),
            'phone_verified_at' => now(),
            'password' => bcrypt('Password123!'), // Compatible avec la regex
            'provider' => null,
            'provider_id' => null,
            'remember_token' => Str::random(10),
            'is_banned' => false,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user is banned.
     *
     * @return static
     */
    public function banned()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_banned' => true,
                'banned_at' => now(),
            ];
        });
    }

    /**
     * Indicate that the user is a Google OAuth user.
     *
     * @return static
     */
    public function google()
    {
        return $this->state(function (array $attributes) {
            return [
                'provider' => 'google',
                'provider_id' => fake()->uuid(),
                'password' => null,
            ];
        });
    }

    /**
     * Indicate that the user is a Facebook OAuth user.
     *
     * @return static
     */
    public function facebook()
    {
        return $this->state(function (array $attributes) {
            return [
                'provider' => 'facebook',
                'provider_id' => fake()->numerify('##############'),
                'password' => null,
            ];
        });
    }
}
