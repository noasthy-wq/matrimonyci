<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'profile_id' => Profile::factory(),
            'path' => 'profiles/' . fake()->uuid() . '.jpg',
            'type' => 'photo',
            'file_size' => fake()->numberBetween(500000, 5000000),
            'mime_type' => 'image/jpeg',
            'is_approved' => false,
            'is_main' => false,
        ];
    }

    /**
     * Indicate that the media is approved.
     *
     * @return static
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_approved' => true,
            ];
        });
    }

    /**
     * Indicate that the media is a video.
     *
     * @return static
     */
    public function video()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'video',
                'path' => 'profiles/' . fake()->uuid() . '.mp4',
                'mime_type' => 'video/mp4',
            ];
        });
    }
}
