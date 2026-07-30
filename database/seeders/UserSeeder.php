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
                'telefono' => '+591 70000001',
                'horario_atencion' => json_encode([
                    'weekday' => ['open' => '08:00', 'close' => '18:00'],
                    'saturday' => ['open' => '09:00', 'close' => '13:00'],
                ]),
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
                'sucursal_id' => null,
            ],
            [
                'rol' => $gerenteRole,
                'nombre' => 'Gerente General',
                'username' => 'gerente',
                'email' => 'gerente@tallerpro.com',
                'sucursal_id' => $sucursal->id,
            ],
            [
                'rol' => $recepcionistaRole,
                'nombre' => 'Recepcionista Principal',
                'username' => 'recepcion',
                'email' => 'recepcion@tallerpro.com',
                'sucursal_id' => $sucursal->id,
            ],
            [
                'rol' => $mecanicoRole,
                'nombre' => 'Mecánico Principal',
                'username' => 'mecanico',
                'email' => 'mecanico@tallerpro.com',
                'sucursal_id' => $sucursal->id,
            ],
        ];

        $passwordPlano = 'TallerPro2026!';

        foreach ($usuarios as $datos) {
            if (! $datos['rol']) {
                continue;
            }

            User::updateOrCreate(
                ['username' => $datos['username']],
                [
                    'nombre' => $datos['nombre'],
                    'email' => $datos['email'],
                    'password' => Hash::make($passwordPlano),
                    'estado' => 'activo',
                    'rol_id' => $datos['rol']->id,
                    'sucursal_id' => $datos['sucursal_id'],
                ]
            );

            $this->command->info("Usuario {$datos['username']} sincronizado ({$datos['rol']->nombre}).");
        }

        $this->command->warn('Contraseña común para pruebas: ' . $passwordPlano . ' — CAMBIAR EN PRODUCCIÓN.');
    }
}
