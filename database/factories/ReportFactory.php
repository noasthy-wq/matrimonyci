<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
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
            'reported_user_id' => User::factory(),
            'reason' => fake()->randomElement(['fraud', 'harassment', 'inappropriate-content', 'spam', 'fake-profile']),
            'description' => fake()->text(200),
            'status' => 'pending',
        ];
    }

    /**
     * Indicate that the report is resolved.
     *
     * @return static
     */
    public function resolved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolution_notes' => 'User banned for violation of terms',
            ];
        });
    }
}
