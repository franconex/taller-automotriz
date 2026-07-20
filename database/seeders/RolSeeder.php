<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rol::create([
            'nombre' => 'Administrador',
            'descripcion' => 'Gestiona todo el sistema'
        ]);

        Rol::create([
            'nombre' => 'Gerente',
            'descripcion' => 'Gestiona reportes'
        ]);

        Rol::create([
            'nombre' => 'Recepcionista',
            'descripcion' => 'Gestiona clientes y citas'
        ]);

        Rol::create([
            'nombre' => 'Mecanico',
            'descripcion' => 'Gestiona órdenes asignadas'
        ]);
    }
}
