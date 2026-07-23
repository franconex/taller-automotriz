<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('nombre', 'Administrador')->first();
        $gerente = Role::where('nombre', 'Gerente')->first();
        $recepcionista = Role::where('nombre', 'Recepcionista')->first();
        $mecanico = Role::where('nombre', 'Mecánico')->first();

        // Administrador: todos los permisos
        $admin->permisos()->sync(Permiso::pluck('id'));

        // Gerente: supervisión, reportes, órdenes, inventario, pagos, clientes
        $gerente->permisos()->sync(
            Permiso::whereIn('codigo', [
                'dashboard.ver',
                'sucursales.ver',
                'usuarios.ver',
                'roles.ver',
                'clientes.ver',
                'clientes.crear',
                'clientes.editar',
                'vehiculos.ver',
                'vehiculos.crear',
                'vehiculos.editar',
                'citas.ver',
                'citas.crear',
                'citas.editar',
                'citas.cancelar',
                'ordenes.ver',
                'ordenes.crear',
                'ordenes.editar',
                'ordenes.asignar',
                'ordenes.actualizar_estado',
                'ordenes.cancelar',
                'ordenes.reabrir',
                'inventario.ver',
                'inventario.entrada',
                'inventario.ajustar',
                'pagos.ver',
                'pagos.registrar',
                'pagos.anular',
                'reportes.ver',
                'auditoria.ver',
            ])->pluck('id')
        );

        // Recepcionista: clientes, vehículos, citas, órdenes (crear), pagos (registrar)
        $recepcionista->permisos()->sync(
            Permiso::whereIn('codigo', [
                'dashboard.ver',
                'clientes.ver',
                'clientes.crear',
                'clientes.editar',
                'vehiculos.ver',
                'vehiculos.crear',
                'vehiculos.editar',
                'citas.ver',
                'citas.crear',
                'citas.editar',
                'citas.cancelar',
                'ordenes.ver',
                'ordenes.crear',
                'ordenes.editar',
                'inventario.ver',
                'pagos.ver',
                'pagos.registrar',
            ])->pluck('id')
        );

        // Mecánico: solo asignaciones, diagnóstico, servicios y repuestos
        $mecanico->permisos()->sync(
            Permiso::whereIn('codigo', [
                'dashboard.ver',
                'ordenes.ver',
                'ordenes.actualizar_estado',
                'inventario.ver',
            ])->pluck('id')
        );
    }
}
