<?php

namespace Database\Factories;

use App\Models\empleado;
use App\Models\sucursal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpleadoFactory extends Factory
{
    protected $model = empleado::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'sucursal_id' => sucursal::factory(),
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'ci' => fake()->unique()->numerify('########'),
            'telefono' => fake()->phoneNumber(),
            'direccion' => fake()->address(),
            'fecha_nacimiento' => fake()->date('Y-m-d', '-20 years'),
            'fecha_ingreso' => fake()->date('Y-m-d', '-1 years'),
            'cargo' => fake()->jobTitle(),
            'salario' => fake()->randomFloat(2, 2000, 8000),
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
