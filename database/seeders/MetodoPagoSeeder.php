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
            ['nombre' => 'Transferencia Bancaria', 'descripcion' => 'Transferencia o depósito bancario', 'estado' => true],
            ['nombre' => 'Tarjeta de Débito', 'descripcion' => 'Pago con tarjeta de débito', 'estado' => true],
            ['nombre' => 'Tarjeta de Crédito', 'descripcion' => 'Pago con tarjeta de crédito', 'estado' => true],
            ['nombre' => 'QR', 'descripcion' => 'Pago mediante código QR', 'estado' => true],
        ];

        foreach ($metodos as $metodo) {
            MetodoPago::firstOrCreate(['nombre' => $metodo['nombre']], $metodo);
        }
    }
}
