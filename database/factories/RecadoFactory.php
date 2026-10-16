<?php

namespace Database\Factories;

use App\Models\Recado;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recado>
 */
class RecadoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'texto' => fake()->realText(200),
        ];
    }
}
