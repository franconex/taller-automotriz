<?php

namespace Database\Factories;

use App\Models\Rol;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $sucursal = Sucursal::inRandomOrder()->first() ?? Sucursal::factory()->create();
        $rol = Rol::inRandomOrder()->first() ?? Rol::factory()->create();

        return [
            'nombre' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'sucursal_id' => $sucursal->id,
            'rol_id' => $rol->id,
            'estado' => 'activo',
        ];
    }
}
