<?php

namespace Database\Seeders;

use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            ['nombre' => 'Efectivo', 'descripcion' => 'Pago en efectivo', 'estado' => true],
            ['nombre' => 'Tarjeta', 'descripcion' => 'Pago con tarjeta de débito o crédito', 'estado' => true],
            ['nombre' => 'QR', 'descripcion' => 'Pago mediante código QR', 'estado' => true],
        ];

        foreach ($metodos as $metodo) {
            MetodoPago::firstOrCreate(['nombre' => $metodo['nombre']], $metodo);
        }
    }
}
