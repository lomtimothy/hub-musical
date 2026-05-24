<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin Hub Musical',
            'email' => 'admin@hubmusical.test',
        ]);

        User::factory()->producer()->create([
            'name' => 'Productor Demo',
            'email' => 'productor@hubmusical.test',
        ]);

        User::factory()->musician()->create([
            'name' => 'Músico Demo',
            'email' => 'musico@hubmusical.test',
        ]);

        User::factory()
            ->producer()
            ->count(5)
            ->create();

        User::factory()
            ->musician()
            ->count(15)
            ->create();
    }
}
