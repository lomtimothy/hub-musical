<?php

namespace Database\Seeders;

use App\Models\Studio;
use App\Models\StudioSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudioSessionSeeder extends Seeder
{
    public function run(): void
    {
        $studios = Studio::query()->get();

        $musicians = User::query()
            ->where('role', 'musician')
            ->get();

        $instruments = [
            'Voz',
            'Guitarra',
            'Bajo',
            'Batería',
            'Teclado',
            'Producción',
            'Mezcla',
        ];

        $studios->each(function (Studio $studio) use ($musicians, $instruments): void {
            StudioSession::factory()
                ->count(2)
                ->state(fn () => [
                    'studio_id' => $studio->id,
                    'booked_by' => $musicians->random()->id,
                ])
                ->create()
                ->each(function (StudioSession $studioSession) use ($musicians, $instruments): void {
                    $selectedMusicians = $musicians->random(fake()->numberBetween(1, min(4, $musicians->count())));

                    $selectedMusicians->each(function (User $musician) use ($studioSession, $instruments): void {
                        $studioSession->musicians()->syncWithoutDetaching([
                            $musician->id => [
                                'instrument' => fake()->randomElement($instruments),
                                'payment_split' => fake()->randomElement([10, 15, 20, 25, 30, 50]),
                            ],
                        ]);
                    });
                });
        });
    }
}
