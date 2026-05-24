<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Analógico',
            'Digital',
            'Vocalista',
            'Guitarrista',
            'Baterista',
            'Productor',
            'Mezcla',
            'Mastering',
            'Podcast',
            'Hip Hop',
            'Rock',
            'Pop',
            'Jazz',
            'Reggaetón',
            'Acústico',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
