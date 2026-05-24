<?php

namespace Database\Seeders;

use App\Models\StudioSession;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = StudioSession::query()
            ->with('musicians')
            ->get();

        $musicians = User::query()
            ->where('role', 'musician')
            ->get();

        $sessions->each(function (StudioSession $session) use ($musicians): void {
            Track::factory()
                ->count(fake()->numberBetween(1, 3))
                ->state(fn () => [
                    'studio_session_id' => $session->id,
                    'uploaded_by' => $session->musicians->isNotEmpty()
                        ? $session->musicians->random()->id
                        : $musicians->random()->id,
                ])
                ->create();
        });
    }
}
