<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        collect([
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
        ])->each(function (string $name): void {
            Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        });
    }
}
