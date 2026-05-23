<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
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
            'subscription_id' => Subscription::factory(),
            'amount' => fake()->randomElement([5000, 50000]),
            'currency' => 'XOF',
            'status' => 'pending',
            'provider' => fake()->randomElement(['orange-money', 'mtn-money', 'moov-money', 'wave']),
            'transaction_id' => fake()->uuid(),
            'reference' => fake()->numerify('REF-########'),
            'phone_number' => fake()->phoneNumber(),
        ];
    }

    /**
     * Indicate that the payment is completed.
     *
     * @return static
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'paid_at' => now(),
            ];
        });
    }

    /**
     * Indicate that the payment failed.
     *
     * @return static
     */
    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'failed',
                'error_message' => 'Insufficient funds',
            ];
        });
    }
}
