<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'musician',
            'remember_token' => Str::random(10),
        ];
    }

    public function musician(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'musician',
        ]);
    }

    public function producer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'producer',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
            'two_factor_recovery_codes' => encrypt(json_encode([
                'recovery-code-one',
                'recovery-code-two',
                'recovery-code-three',
                'recovery-code-four',
                'recovery-code-five',
                'recovery-code-six',
                'recovery-code-seven',
                'recovery-code-eight',
            ])),
            'two_factor_confirmed_at' => now(),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
