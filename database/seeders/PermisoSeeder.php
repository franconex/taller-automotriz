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

            // Servicios
            ['nombre' => 'Ver Servicios', 'codigo' => 'servicios.ver', 'modulo' => 'servicios'],
            ['nombre' => 'Crear Servicios', 'codigo' => 'servicios.crear', 'modulo' => 'servicios'],
            ['nombre' => 'Editar Servicios', 'codigo' => 'servicios.editar', 'modulo' => 'servicios'],

            // Tipos de Servicio
            ['nombre' => 'Ver Tipos de Servicio', 'codigo' => 'tipos-servicio.ver', 'modulo' => 'tipos-servicio'],
            ['nombre' => 'Crear Tipos de Servicio', 'codigo' => 'tipos-servicio.crear', 'modulo' => 'tipos-servicio'],
            ['nombre' => 'Editar Tipos de Servicio', 'codigo' => 'tipos-servicio.editar', 'modulo' => 'tipos-servicio'],

            // Proveedores
            ['nombre' => 'Ver Proveedores', 'codigo' => 'proveedores.ver', 'modulo' => 'proveedores'],
            ['nombre' => 'Crear Proveedores', 'codigo' => 'proveedores.crear', 'modulo' => 'proveedores'],
            ['nombre' => 'Editar Proveedores', 'codigo' => 'proveedores.editar', 'modulo' => 'proveedores'],

            // Repuestos
            ['nombre' => 'Ver Repuestos', 'codigo' => 'repuestos.ver', 'modulo' => 'repuestos'],
            ['nombre' => 'Crear Repuestos', 'codigo' => 'repuestos.crear', 'modulo' => 'repuestos'],
            ['nombre' => 'Editar Repuestos', 'codigo' => 'repuestos.editar', 'modulo' => 'repuestos'],

            // Empleados
            ['nombre' => 'Ver Empleados', 'codigo' => 'empleados.ver', 'modulo' => 'empleados'],
            ['nombre' => 'Crear Empleados', 'codigo' => 'empleados.crear', 'modulo' => 'empleados'],
            ['nombre' => 'Editar Empleados', 'codigo' => 'empleados.editar', 'modulo' => 'empleados'],

            // Mecánicos
            ['nombre' => 'Ver Mecánicos', 'codigo' => 'mecanicos.ver', 'modulo' => 'mecanicos'],
            ['nombre' => 'Crear Mecánicos', 'codigo' => 'mecanicos.crear', 'modulo' => 'mecanicos'],
            ['nombre' => 'Editar Mecánicos', 'codigo' => 'mecanicos.editar', 'modulo' => 'mecanicos'],

            // Configuración
            ['nombre' => 'Ver Configuración', 'codigo' => 'configuracion.ver', 'modulo' => 'configuracion'],
            ['nombre' => 'Editar Configuración', 'codigo' => 'configuracion.editar', 'modulo' => 'configuracion'],

            // Métodos de Pago
            ['nombre' => 'Ver Métodos de Pago', 'codigo' => 'metodos-pago.ver', 'modulo' => 'metodos-pago'],
            ['nombre' => 'Editar Métodos de Pago', 'codigo' => 'metodos-pago.editar', 'modulo' => 'metodos-pago'],

            // Comprobantes
            ['nombre' => 'Ver Comprobantes', 'codigo' => 'comprobantes.ver', 'modulo' => 'comprobantes'],
            ['nombre' => 'Editar Comprobantes', 'codigo' => 'comprobantes.editar', 'modulo' => 'comprobantes'],

            // Precios
            ['nombre' => 'Ver Precios de Compra', 'codigo' => 'precios.ver', 'modulo' => 'repuestos'],

            // Solicitudes de Permiso
            ['nombre' => 'Ver Solicitudes de Permiso', 'codigo' => 'solicitudes-permiso.ver', 'modulo' => 'solicitudes-permiso'],
            ['nombre' => 'Crear Solicitudes de Permiso', 'codigo' => 'solicitudes-permiso.crear', 'modulo' => 'solicitudes-permiso'],
            ['nombre' => 'Aprobar Solicitudes de Permiso', 'codigo' => 'solicitudes-permiso.aprobar', 'modulo' => 'solicitudes-permiso'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::firstOrCreate(
                ['codigo' => $permiso['codigo']],
                $permiso
            );
        }
    }
}
