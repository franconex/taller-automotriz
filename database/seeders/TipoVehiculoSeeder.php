<?php

namespace Database\Seeders;

use App\Models\TipoVehiculo;
use Illuminate\Database\Seeder;

class TipoVehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'Automovil', 'Hatchback', 'Sedan', 'Coupe', 'Convertible',
            'Vagoneta', 'SUV', 'Crossover', 'Jeep',
            'Camioneta Cabina Sencilla', 'Camioneta Cabina Doble',
            'Furgoneta', 'Minivan', 'Minibus', 'Microbus',
            'Autobus Urbano', 'Autobus Interprovincial', 'Autobus de Turismo',
            'Camion Liviano', 'Camion Mediano', 'Camion Pesado',
            'Tractocamion', 'Volqueta', 'Cisterna', 'Camion Frigorifico',
            'Camion Grua', 'Ambulancia', 'Motocicleta', 'Cuadratrack',
            'Motocarro', 'Maquinaria Especial',
        ];

        foreach ($tipos as $nombre) {
            TipoVehiculo::create(['nombre' => $nombre, 'estado' => true]);
        }
    }
}
