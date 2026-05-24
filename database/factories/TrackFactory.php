<?php

namespace Database\Factories;

use App\Models\StudioSession;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Track>
 */
class TrackFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extension = fake()->randomElement(['wav', 'mp3']);
        $fileName = fake()->slug().'_'.$this->faker->unique()->numberBetween(1000, 9999).'.'.$extension;

        return [
            'studio_session_id' => StudioSession::factory(),
            'uploaded_by' => User::factory()->musician(),
            'title' => fake()->randomElement([
                'Demo vocal',
                'Guitarra principal',
                'Batería toma 1',
                'Mezcla preliminar',
                'Referencia de mastering',
                'Bajo limpio',
            ]),
            'original_name' => $fileName,
            'path' => 'tracks/'.$fileName,
            'mime_type' => $extension === 'wav' ? 'audio/wav' : 'audio/mpeg',
            'size' => fake()->numberBetween(500_000, 50_000_000),
            'version' => fake()->randomElement(['1.0', '1.1', '2.0']),
        ];
    }
}
