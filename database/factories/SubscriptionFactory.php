<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
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
            'tier' => 'free',
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => null,
        ];
    }

    /**
     * Indicate that the subscription is premium monthly.
     *
     * @return static
     */
    public function premiumMonthly()
    {
        return $this->state(function (array $attributes) {
            return [
                'tier' => 'premium_monthly',
                'expires_at' => now()->addDays(30),
            ];
        });
    }

    /**
     * Indicate that the subscription is premium annual.
     *
     * @return static
     */
    public function premiumAnnual()
    {
        return $this->state(function (array $attributes) {
            return [
                'tier' => 'premium_annual',
                'expires_at' => now()->addDays(365),
            ];
        });
    }

    /**
     * Indicate that the subscription is expired.
     *
     * @return static
     */
    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'expired',
                'expires_at' => now()->subDays(1),
            ];
        });
    }
}
