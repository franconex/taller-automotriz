<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SucursalFactory extends Factory
{
    public function definition(): array
    {
        $nombres = ['Sucursal Central', 'Sucursal Norte', 'Sucursal Sur', 'Sucursal Este', 'Sucursal Oeste'];

        return [
            'nombre' => fake()->randomElement($nombres) . ' ' . fake()->city(),
            'direccion' => fake()->streetAddress() . ', ' . fake()->city(),
            'telefono' => '+591 ' . fake()->numerify('7########'),
            'horario_atencion' => json_encode([
                'weekday' => ['open' => '08:00', 'close' => '18:00'],
                'saturday' => ['open' => '09:00', 'close' => '13:00'],
            ]),
            'estado' => true,
        ];
    }
}
