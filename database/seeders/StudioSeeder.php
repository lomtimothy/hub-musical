<?php

namespace Database\Seeders;

use App\Models\Studio;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudioSeeder extends Seeder
{
    public function run(): void
    {
        $producers = User::query()
            ->where('role', 'producer')
            ->get();

        $tags = Tag::query()->get();

        $studios = collect([
            [
                'name' => 'Estudio Centro',
                'description' => 'Estudio profesional para grabación, mezcla y producción musical en el centro de la ciudad.',
                'address' => 'Av. Demo 123',
                'city' => 'Guadalajara',
                'state' => 'Jalisco',
                'hourly_rate' => 500,
                'capacity' => 6,
            ],
            [
                'name' => 'Sala Norte Records',
                'description' => 'Espacio equipado para bandas, productores independientes y sesiones vocales.',
                'address' => 'Calle Norte 456',
                'city' => 'Zapopan',
                'state' => 'Jalisco',
                'hourly_rate' => 750,
                'capacity' => 8,
            ],
            [
                'name' => 'Analog House Studio',
                'description' => 'Estudio con equipo analógico, consola vintage y cabina tratada acústicamente.',
                'address' => 'Av. Sonido 789',
                'city' => 'Tlaquepaque',
                'state' => 'Jalisco',
                'hourly_rate' => 950,
                'capacity' => 5,
            ],
        ])->map(function (array $data, int $index) use ($producers, $tags): Studio {
            $studio = Studio::create([
                ...$data,
                'owner_id' => $producers[$index % $producers->count()]->id,
                'slug' => Str::slug($data['name']),
                'is_active' => true,
            ]);

            $studio->tags()->sync(
                $tags->random(min(3, $tags->count()))->pluck('id')->all()
            );

            return $studio;
        });

        Studio::factory()
            ->count(7)
            ->state(fn () => [
                'owner_id' => $producers->random()->id,
            ])
            ->create()
            ->each(function (Studio $studio) use ($tags): void {
                $studio->tags()->sync(
                    $tags->random(min(3, $tags->count()))->pluck('id')->all()
                );
            });
    }
}
