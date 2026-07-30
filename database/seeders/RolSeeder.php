<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Administrador', 'descripcion' => 'Acceso completo al sistema', 'estado' => true],
            ['nombre' => 'Gerente', 'descripcion' => 'Supervisión, reportes y autorizaciones', 'estado' => true],
            ['nombre' => 'Recepcionista', 'descripcion' => 'Atención al cliente, citas y órdenes', 'estado' => true],
            ['nombre' => 'Mecánico', 'descripcion' => 'Asignaciones, diagnóstico y servicios', 'estado' => true],

        ];

        foreach ($roles as $rol) {
            Rol::firstOrCreate(['nombre' => $rol['nombre']], $rol);
        }
    }
}
