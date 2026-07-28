<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SucursalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'direccion' => fake()->address(),
            'telefono' => fake()->phoneNumber(),
            'horario_atencion' => 'Lun-Vie 8-18',
            'estado' => true,
        ];
    }
}
