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
        Rol::firstOrCreate([
            'nombre' => 'Administrador',
        ], [
            'descripcion' => 'Gestiona todo el sistema'
        ]);

        Rol::firstOrCreate([
            'nombre' => 'Gerente',
        ], [
            'descripcion' => 'Gestiona reportes'
        ]);

        Rol::firstOrCreate([
            'nombre' => 'Recepcionista',
        ], [
            'descripcion' => 'Gestiona clientes y citas'
        ]);

        Rol::firstOrCreate([
            'nombre' => 'Mecanico',
        ], [
            'descripcion' => 'Gestiona órdenes asignadas'
        ]);
    }
}
