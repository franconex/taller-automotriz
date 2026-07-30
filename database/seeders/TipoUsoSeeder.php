<?php

namespace Database\Seeders;

use App\Models\TipoUso;
use Illuminate\Database\Seeder;

class TipoUsoSeeder extends Seeder
{
    public function run(): void
    {
        $usos = [
            'Particular', 'Taxi', 'Radiotaxi', 'Trufi',
            'Minibus Urbano', 'Micro Urbano',
            'Transporte Escolar', 'Transporte Empresarial',
            'Transporte Interprovincial', 'Transporte Interdepartamental',
            'Turismo', 'Carga', 'Reparto', 'Delivery',
            'Ambulancia', 'Policia', 'Bomberos',
            'Servicio Municipal', 'Maquinaria', 'Otro',
        ];

        foreach ($usos as $nombre) {
            TipoUso::create(['nombre' => $nombre, 'estado' => true]);
        }
    }
}
