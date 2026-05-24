<?php

namespace Database\Factories;

use App\Models\Studio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Studio>
 */
class StudioFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company().' Studio';

        return [
            'owner_id' => User::factory()->producer(),
            'name' => $name,
            'slug' => Str::slug($name.' '.fake()->unique()->numberBetween(100, 999)),
            'description' => fake()->paragraph(3),
            'address' => fake()->streetAddress(),
            'city' => fake()->randomElement(['Guadalajara', 'Zapopan', 'Tlaquepaque', 'Tonalá']),
            'state' => 'Jalisco',
            'hourly_rate' => fake()->numberBetween(250, 1200),
            'capacity' => fake()->numberBetween(2, 12),
            'is_active' => fake()->boolean(90),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
