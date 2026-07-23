<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $sucursal = Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Principal'],
            [
                'direccion' => 'Av. Principal #100, Santa Cruz',
                'telefono' => '3-1234567',
                'horario_atencion' => 'Lun-Vie 8:00-18:00, Sáb 8:00-13:00',
            ]
        );

        $adminRole = Role::where('nombre', 'Administrador')->first();

        if ($adminRole && ! User::where('username', 'admin')->exists()) {
            User::create([
                'nombre' => 'Administrador Principal',
                'username' => 'admin',
                'email' => 'admin@tallerpro.com',
                'password' => Hash::make('Cambiar123!'),
                'estado' => 'activo',
                'rol_id' => $adminRole->id,
                'sucursal_id' => $sucursal->id,
            ]);

            $this->command->info('Usuario administrador creado: admin / Cambiar123!');
            $this->command->warn('CAMBIA ESTA CONTRASEÑA EN PRODUCCIÓN.');
        }
    }
}
