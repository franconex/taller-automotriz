<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            // Dashboard
            ['nombre' => 'Ver Dashboard', 'codigo' => 'dashboard.ver', 'modulo' => 'dashboard'],

            // Sucursales
            ['nombre' => 'Ver Sucursales', 'codigo' => 'sucursales.ver', 'modulo' => 'sucursales'],
            ['nombre' => 'Crear Sucursales', 'codigo' => 'sucursales.crear', 'modulo' => 'sucursales'],
            ['nombre' => 'Editar Sucursales', 'codigo' => 'sucursales.editar', 'modulo' => 'sucursales'],

            // Usuarios
            ['nombre' => 'Ver Usuarios', 'codigo' => 'usuarios.ver', 'modulo' => 'usuarios'],
            ['nombre' => 'Crear Usuarios', 'codigo' => 'usuarios.crear', 'modulo' => 'usuarios'],
            ['nombre' => 'Editar Usuarios', 'codigo' => 'usuarios.editar', 'modulo' => 'usuarios'],
            ['nombre' => 'Desactivar Usuarios', 'codigo' => 'usuarios.desactivar', 'modulo' => 'usuarios'],

            // Roles y Permisos
            ['nombre' => 'Ver Roles', 'codigo' => 'roles.ver', 'modulo' => 'roles'],
            ['nombre' => 'Editar Roles', 'codigo' => 'roles.editar', 'modulo' => 'roles'],
            ['nombre' => 'Asignar Permisos', 'codigo' => 'permisos.asignar', 'modulo' => 'roles'],

            // Clientes
            ['nombre' => 'Ver Clientes', 'codigo' => 'clientes.ver', 'modulo' => 'clientes'],
            ['nombre' => 'Crear Clientes', 'codigo' => 'clientes.crear', 'modulo' => 'clientes'],
            ['nombre' => 'Editar Clientes', 'codigo' => 'clientes.editar', 'modulo' => 'clientes'],

            // Vehículos
            ['nombre' => 'Ver Vehículos', 'codigo' => 'vehiculos.ver', 'modulo' => 'vehiculos'],
            ['nombre' => 'Crear Vehículos', 'codigo' => 'vehiculos.crear', 'modulo' => 'vehiculos'],
            ['nombre' => 'Editar Vehículos', 'codigo' => 'vehiculos.editar', 'modulo' => 'vehiculos'],

            // Citas
            ['nombre' => 'Ver Citas', 'codigo' => 'citas.ver', 'modulo' => 'citas'],
            ['nombre' => 'Crear Citas', 'codigo' => 'citas.crear', 'modulo' => 'citas'],
            ['nombre' => 'Editar Citas', 'codigo' => 'citas.editar', 'modulo' => 'citas'],
            ['nombre' => 'Cancelar Citas', 'codigo' => 'citas.cancelar', 'modulo' => 'citas'],

            // Órdenes de Trabajo
            ['nombre' => 'Ver Órdenes', 'codigo' => 'ordenes.ver', 'modulo' => 'ordenes'],
            ['nombre' => 'Crear Órdenes', 'codigo' => 'ordenes.crear', 'modulo' => 'ordenes'],
            ['nombre' => 'Editar Órdenes', 'codigo' => 'ordenes.editar', 'modulo' => 'ordenes'],
            ['nombre' => 'Asignar Órdenes', 'codigo' => 'ordenes.asignar', 'modulo' => 'ordenes'],
            ['nombre' => 'Actualizar Estado', 'codigo' => 'ordenes.actualizar_estado', 'modulo' => 'ordenes'],
            ['nombre' => 'Cancelar Órdenes', 'codigo' => 'ordenes.cancelar', 'modulo' => 'ordenes'],
            ['nombre' => 'Reabrir Órdenes', 'codigo' => 'ordenes.reabrir', 'modulo' => 'ordenes'],

            // Inventario
            ['nombre' => 'Ver Inventario', 'codigo' => 'inventario.ver', 'modulo' => 'inventario'],
            ['nombre' => 'Entrada Inventario', 'codigo' => 'inventario.entrada', 'modulo' => 'inventario'],
            ['nombre' => 'Ajustar Inventario', 'codigo' => 'inventario.ajustar', 'modulo' => 'inventario'],

            // Pagos
            ['nombre' => 'Ver Pagos', 'codigo' => 'pagos.ver', 'modulo' => 'pagos'],
            ['nombre' => 'Registrar Pagos', 'codigo' => 'pagos.registrar', 'modulo' => 'pagos'],
            ['nombre' => 'Anular Pagos', 'codigo' => 'pagos.anular', 'modulo' => 'pagos'],

            // Reportes
            ['nombre' => 'Ver Reportes', 'codigo' => 'reportes.ver', 'modulo' => 'reportes'],

            // Auditoría
            ['nombre' => 'Ver Auditoría', 'codigo' => 'auditoria.ver', 'modulo' => 'auditoria'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::firstOrCreate(
                ['codigo' => $permiso['codigo']],
                $permiso
            );
        }
    }
}
