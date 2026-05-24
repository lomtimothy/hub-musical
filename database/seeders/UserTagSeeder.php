<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::query()->get();

        User::query()
            ->where('role', 'musician')
            ->orWhere('role', 'producer')
            ->get()
            ->each(function (User $user) use ($tags): void {
                $user->tags()->sync(
                    $tags->random(min(2, $tags->count()))->pluck('id')->all()
                );
            });
    }
}
