<?php

namespace Database\Seeders;

use App\Models\permisos;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Rol::where('nombre', 'Administrador')->first();
        $gerente = Rol::where('nombre', 'Gerente')->first();
        $recepcionista = Rol::where('nombre', 'Recepcionista')->first();
        $mecanico = Rol::where('nombre', 'Mecanico')->first();

        if ($admin) {
            $admin->permisos()->sync(
                permisos::pluck('id')->toArray()
            );
        }

        if ($gerente) {
            $gerente->permisos()->sync(
                permisos::whereIn('codigo', [
                    'clientes.ver',
                    'empleados.ver',
                    'ordenes.ver',
                    'pagos.ver',
                    'reportes.ver',
                    'sucursales.ver',
                    'auditoria.ver',
                ])->pluck('id')->toArray()
            );
        }

        if ($recepcionista) {
            $recepcionista->permisos()->sync(
                permisos::whereIn('codigo', [
                    'clientes.ver',
                    'clientes.crear',
                    'clientes.editar',
                    'vehiculos.ver',
                    'vehiculos.crear',
                    'vehiculos.editar',
                    'citas.ver',
                    'citas.crear',
                    'citas.editar',
                    'citas.confirmar',
                    'citas.reprogramar',
                    'citas.cancelar',
                    'ordenes.ver',
                    'ordenes.crear',
                    'pagos.ver',
                    'pagos.registrar',
                ])->pluck('id')->toArray()
            );
        }

        if ($mecanico) {
            $mecanico->permisos()->sync(
                permisos::whereIn('codigo', [
                    'ordenes.ver',
                    'ordenes.actualizar_estado',
                    'ordenes.registrar_diagnostico',
                ])->pluck('id')->toArray()
            );
        }
    }
}
