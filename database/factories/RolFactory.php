<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RolFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->randomElement(['Administrador', 'Mecánico', 'Recepcionista', 'Gerente']),
            'estado' => true,
        ];
    }
}
