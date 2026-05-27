<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
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
            'gender' => fake()->randomElement(['homme', 'femme', 'autre']),
            'age' => fake()->numberBetween(18, 65),
            'religion' => fake()->randomElement(['Islam', 'Christianisme', 'Autre', 'Aucune']),
            'profession' => fake()->jobTitle(),
            'bio' => fake()->text(200),
            'city' => fake()->randomElement(['Abidjan', 'Yamoussoukro', 'Bouaké', 'Daloa', 'Gagnoa']),
            'country' => 'Côte d\'Ivoire',
            'education' => fake()->randomElement(['Primaire', 'Secondaire', 'Licence', 'Master', 'Doctorat']),
            'marital_status' => fake()->randomElement(['Célibataire', 'Divorcé(e)', 'Veuf(ve)']),
            'height' => fake()->numberBetween(150, 200),
            'complexion' => fake()->randomElement(['Clair', 'Moyen', 'Foncé']),
            'looking_for' => fake()->text(100),
            'is_verified' => false,
        ];
    }

    /**
     * Indicate that the profile is verified.
     *
     * @return static
     */
    public function verified()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_verified' => true,
            ];
        });
    }
}
