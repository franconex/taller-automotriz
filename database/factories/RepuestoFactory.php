<?php

namespace Database\Factories;

use App\Models\Repuesto;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepuestoFactory extends Factory
{
    protected $model = Repuesto::class;

    public function definition(): array
    {
        return [
            'codigo' => 'REP-' . str_pad((string) fake()->unique()->randomNumber(5), 5, '0', STR_PAD_LEFT),
            'codigo_barras' => fake()->unique()->ean13(),
            'codigo_fabricante' => null,
            'tipo' => 'repuesto',
            'nombre' => fake()->words(3, true),
            'categoria' => fake()->randomElement(['Lubricantes', 'Filtros', 'Frenos', 'Suspensión', 'Eléctrico', 'Motor']),
            'marca' => fake()->randomElement(['Castrol', 'Bosch', 'Mann', 'SKF', 'Valeo', 'NGK']),
            'descripcion' => fake()->sentence(),
            'costo_compra' => fake()->randomFloat(2, 10, 500),
            'precio_venta' => fake()->randomFloat(2, 20, 800),
            'stock_minimo' => 3,
            'stock_maximo' => 50,
            'estado' => true,
        ];
    }
}
