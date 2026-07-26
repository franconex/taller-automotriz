<?php

namespace Database\Seeders;

use App\Models\MarcaVehiculo;
use Illuminate\Database\Seeder;

class MarcaVehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            ['nombre' => 'Toyota', 'pais_origen' => 'Japon'],
            ['nombre' => 'Nissan', 'pais_origen' => 'Japon'],
            ['nombre' => 'Suzuki', 'pais_origen' => 'Japon'],
            ['nombre' => 'Honda', 'pais_origen' => 'Japon'],
            ['nombre' => 'Mitsubishi', 'pais_origen' => 'Japon'],
            ['nombre' => 'Mazda', 'pais_origen' => 'Japon'],
            ['nombre' => 'Subaru', 'pais_origen' => 'Japon'],
            ['nombre' => 'Isuzu', 'pais_origen' => 'Japon'],
            ['nombre' => 'Hino', 'pais_origen' => 'Japon'],
            ['nombre' => 'Hyundai', 'pais_origen' => 'Corea del Sur'],
            ['nombre' => 'Kia', 'pais_origen' => 'Corea del Sur'],
            ['nombre' => 'Chevrolet', 'pais_origen' => 'Estados Unidos'],
            ['nombre' => 'Ford', 'pais_origen' => 'Estados Unidos'],
            ['nombre' => 'Jeep', 'pais_origen' => 'Estados Unidos'],
            ['nombre' => 'Dodge', 'pais_origen' => 'Estados Unidos'],
            ['nombre' => 'RAM', 'pais_origen' => 'Estados Unidos'],
            ['nombre' => 'Freightliner', 'pais_origen' => 'Estados Unidos'],
            ['nombre' => 'International', 'pais_origen' => 'Estados Unidos'],
            ['nombre' => 'Mack', 'pais_origen' => 'Estados Unidos'],
            ['nombre' => 'Kenworth', 'pais_origen' => 'Estados Unidos'],
            ['nombre' => 'Volkswagen', 'pais_origen' => 'Alemania'],
            ['nombre' => 'Mercedes-Benz', 'pais_origen' => 'Alemania'],
            ['nombre' => 'BMW', 'pais_origen' => 'Alemania'],
            ['nombre' => 'Audi', 'pais_origen' => 'Alemania'],
            ['nombre' => 'MAN', 'pais_origen' => 'Alemania'],
            ['nombre' => 'Renault', 'pais_origen' => 'Francia'],
            ['nombre' => 'Peugeot', 'pais_origen' => 'Francia'],
            ['nombre' => 'Citroen', 'pais_origen' => 'Francia'],
            ['nombre' => 'Fiat', 'pais_origen' => 'Italia'],
            ['nombre' => 'Iveco', 'pais_origen' => 'Italia'],
            ['nombre' => 'Volvo', 'pais_origen' => 'Suecia'],
            ['nombre' => 'Scania', 'pais_origen' => 'Suecia'],
            ['nombre' => 'Land Rover', 'pais_origen' => 'Reino Unido'],
            ['nombre' => 'Changan', 'pais_origen' => 'China'],
            ['nombre' => 'Great Wall', 'pais_origen' => 'China'],
            ['nombre' => 'Haval', 'pais_origen' => 'China'],
            ['nombre' => 'JAC', 'pais_origen' => 'China'],
            ['nombre' => 'Geely', 'pais_origen' => 'China'],
            ['nombre' => 'BYD', 'pais_origen' => 'China'],
            ['nombre' => 'Chery', 'pais_origen' => 'China'],
            ['nombre' => 'DFSK', 'pais_origen' => 'China'],
            ['nombre' => 'Dongfeng', 'pais_origen' => 'China'],
            ['nombre' => 'Foton', 'pais_origen' => 'China'],
            ['nombre' => 'BAIC', 'pais_origen' => 'China'],
            ['nombre' => 'GAC', 'pais_origen' => 'China'],
            ['nombre' => 'MG', 'pais_origen' => 'China'],
            ['nombre' => 'Jetour', 'pais_origen' => 'China'],
            ['nombre' => 'Maxus', 'pais_origen' => 'China'],
            ['nombre' => 'Yutong', 'pais_origen' => 'China'],
            ['nombre' => 'King Long', 'pais_origen' => 'China'],
            ['nombre' => 'Higer', 'pais_origen' => 'China'],
            ['nombre' => 'Golden Dragon', 'pais_origen' => 'China'],
            ['nombre' => 'Zhongtong', 'pais_origen' => 'China'],
            ['nombre' => 'Marcopolo', 'pais_origen' => 'Brasil'],
            ['nombre' => 'Busscar', 'pais_origen' => 'Brasil'],
            ['nombre' => 'Bajaj', 'pais_origen' => 'India'],
            ['nombre' => 'TVS', 'pais_origen' => 'India'],
            ['nombre' => 'Haojue', 'pais_origen' => 'China'],
            ['nombre' => 'Kawasaki', 'pais_origen' => 'Japon'],
            ['nombre' => 'Yamaha', 'pais_origen' => 'Japon'],
        ];

        foreach ($marcas as $m) {
            MarcaVehiculo::create([
                'nombre' => $m['nombre'],
                'pais_origen' => $m['pais_origen'],
                'estado' => true,
            ]);
        }
    }
}
