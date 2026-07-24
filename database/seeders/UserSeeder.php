<?php

namespace Database\Seeders;

use App\Models\Rol;
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

        $adminRole = Rol::where('nombre', 'Administrador')->first();
        $gerenteRole = Rol::where('nombre', 'Gerente')->first();
        $recepcionistaRole = Rol::where('nombre', 'Recepcionista')->first();
        $mecanicoRole = Rol::where('nombre', 'Mecánico')->first();

        $usuarios = [
            [
                'rol' => $adminRole,
                'nombre' => 'Administrador Principal',
                'username' => 'admin',
                'email' => 'admin@tallerpro.com',
            ],
            [
                'rol' => $gerenteRole,
                'nombre' => 'Gerente General',
                'username' => 'gerente',
                'email' => 'gerente@tallerpro.com',
            ],
            [
                'rol' => $recepcionistaRole,
                'nombre' => 'Recepcionista Principal',
                'username' => 'recepcion',
                'email' => 'recepcion@tallerpro.com',
            ],
            [
                'rol' => $mecanicoRole,
                'nombre' => 'Mecánico Principal',
                'username' => 'mecanico',
                'email' => 'mecanico@tallerpro.com',
            ],
        ];

        $passwordPlano = 'TallerPro2026!';

        foreach ($usuarios as $datos) {
            if (! $datos['rol']) {
                continue;
            }

            if (! User::where('username', $datos['username'])->exists()) {
                User::create([
                    'nombre' => $datos['nombre'],
                    'username' => $datos['username'],
                    'email' => $datos['email'],
                    'password' => Hash::make($passwordPlano),
                    'estado' => 'activo',
                    'rol_id' => $datos['rol']->id,
                    'sucursal_id' => $sucursal->id,
                ]);

                $this->command->info("Usuario {$datos['username']} creado ({$datos['rol']->nombre}).");
            }
        }

        $this->command->warn('Contraseña común para pruebas: ' . $passwordPlano . ' — CAMBIAR EN PRODUCCIÓN.');
    }
}
