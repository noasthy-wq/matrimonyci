<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Violation>
 */
class ViolationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['warning', 'suspension', 'ban']),
            'reason' => fake()->randomElement(['spam', 'harassment', 'inappropriate-content', 'fraud']),
            'status' => 'active',
            'suspended_until' => now()->addDays(7),
            'details' => fake()->text(100),
        ];
    }

    /**
     * Indicate that the violation is resolved.
     *
     * @return static
     */
    public function resolved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'resolved',
            ];
        });
    }
}
