<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SucursalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->company() . ' Sucursal',
            'direccion' => fake()->address(),
            'telefono' => fake()->phoneNumber(),
            'estado' => true,
        ];
    }
}
