<?php

namespace Database\Factories;

use App\Models\Studio;
use App\Models\StudioSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<StudioSession>
 */
class StudioSessionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = Carbon::instance(fake()->dateTimeBetween('+1 day', '+1 month'));
        $endsAt = $startsAt->copy()->addHours(fake()->numberBetween(1, 4));

        return [
            'studio_id' => Studio::factory(),
            'booked_by' => User::factory()->musician(),
            'title' => fake()->randomElement([
                'Grabación de demo',
                'Sesión de mezcla',
                'Producción vocal',
                'Grabación de podcast',
                'Mastering de sencillo',
                'Ensayo preproducción',
            ]),
            'notes' => fake()->optional()->paragraph(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed']),
            'total_price' => fake()->numberBetween(500, 5000),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
