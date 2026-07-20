<?php

namespace Database\Factories;

use App\Models\sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

class SucursalFactory extends Factory
{
    protected $model = sucursal::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->company(),
            'direccion' => fake()->address(),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'horario_atencion' => 'Lun–Sáb 08:00–18:00',
            'latitud' => fake()->latitude(),
            'longitud' => fake()->longitude(),
            'estado' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => false,
        ]);
    }
}
