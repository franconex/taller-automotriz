<?php

namespace Database\Seeders;

use App\Models\permisos;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Usuarios', 'usuarios.ver', 'Ver usuarios', 'Ver listado de usuarios'],
            ['Usuarios', 'usuarios.crear', 'Crear usuarios', 'Crear nuevos usuarios'],
            ['Usuarios', 'usuarios.editar', 'Editar usuarios', 'Editar usuarios existentes'],
            ['Usuarios', 'usuarios.desactivar', 'Desactivar usuarios', 'Activar/desactivar usuarios'],
            ['Usuarios', 'usuarios.restablecer_password', 'Restablecer contraseña', 'Restablecer contraseña de usuarios'],

            ['Empleados', 'empleados.ver', 'Ver empleados', 'Ver listado de empleados'],
            ['Empleados', 'empleados.crear', 'Crear empleados', 'Crear nuevos empleados'],
            ['Empleados', 'empleados.editar', 'Editar empleados', 'Editar empleados existentes'],
            ['Empleados', 'empleados.desactivar', 'Desactivar empleados', 'Activar/desactivar empleados'],

            ['Clientes', 'clientes.ver', 'Ver clientes', 'Ver listado de clientes'],
            ['Clientes', 'clientes.crear', 'Crear clientes', 'Crear nuevos clientes'],
            ['Clientes', 'clientes.editar', 'Editar clientes', 'Editar clientes existentes'],
            ['Clientes', 'clientes.desactivar', 'Desactivar clientes', 'Activar/desactivar clientes'],

            ['Vehículos', 'vehiculos.ver', 'Ver vehículos', 'Ver listado de vehículos'],
            ['Vehículos', 'vehiculos.crear', 'Crear vehículos', 'Crear nuevos vehículos'],
            ['Vehículos', 'vehiculos.editar', 'Editar vehículos', 'Editar vehículos existentes'],
            ['Vehículos', 'vehiculos.desactivar', 'Desactivar vehículos', 'Activar/desactivar vehículos'],

            ['Citas', 'citas.ver', 'Ver citas', 'Ver listado de citas'],
            ['Citas', 'citas.crear', 'Crear citas', 'Crear nuevas citas'],
            ['Citas', 'citas.editar', 'Editar citas', 'Editar citas existentes'],
            ['Citas', 'citas.confirmar', 'Confirmar citas', 'Confirmar citas agendadas'],
            ['Citas', 'citas.reprogramar', 'Reprogramar citas', 'Cambiar fecha/hora de citas'],
            ['Citas', 'citas.cancelar', 'Cancelar citas', 'Cancelar citas existentes'],

            ['Órdenes', 'ordenes.ver', 'Ver órdenes', 'Ver listado de órdenes de trabajo'],
            ['Órdenes', 'ordenes.crear', 'Crear órdenes', 'Crear nuevas órdenes de trabajo'],
            ['Órdenes', 'ordenes.asignar', 'Asignar órdenes', 'Asignar órdenes a mecánicos'],
            ['Órdenes', 'ordenes.actualizar_estado', 'Actualizar estado', 'Actualizar el estado de órdenes'],
            ['Órdenes', 'ordenes.registrar_diagnostico', 'Registrar diagnóstico', 'Registrar diagnóstico en órdenes'],

            ['Pagos', 'pagos.ver', 'Ver pagos', 'Ver listado de pagos'],
            ['Pagos', 'pagos.registrar', 'Registrar pagos', 'Registrar nuevos pagos'],
            ['Pagos', 'pagos.anular', 'Anular pagos', 'Anular pagos existentes'],

            ['Roles', 'roles.ver', 'Ver roles', 'Ver listado de roles'],
            ['Roles', 'roles.crear', 'Crear roles', 'Crear nuevos roles'],
            ['Roles', 'roles.editar', 'Editar roles', 'Editar roles existentes'],
            ['Roles', 'roles.asignar_permisos', 'Asignar permisos', 'Asignar permisos a roles'],

            ['Permisos', 'permisos.ver', 'Ver permisos', 'Ver listado de permisos'],
            ['Permisos', 'permisos.crear', 'Crear permisos', 'Crear nuevos permisos'],
            ['Permisos', 'permisos.editar', 'Editar permisos', 'Editar permisos existentes'],

            ['Sucursales', 'sucursales.ver', 'Ver sucursales', 'Ver listado de sucursales'],
            ['Sucursales', 'sucursales.crear', 'Crear sucursales', 'Crear nuevas sucursales'],
            ['Sucursales', 'sucursales.editar', 'Editar sucursales', 'Editar sucursales existentes'],
            ['Sucursales', 'sucursales.desactivar', 'Desactivar sucursales', 'Activar/desactivar sucursales'],

            ['Auditoría', 'auditoria.ver', 'Ver auditoría', 'Ver registro de auditoría'],
            ['Reportes', 'reportes.ver', 'Ver reportes', 'Ver reportes y estadísticas'],

            ['Especialidades', 'especialidades.ver', 'Ver especialidades', 'Ver listado de especialidades'],
            ['Especialidades', 'especialidades.crear', 'Crear especialidades', 'Crear nuevas especialidades'],
            ['Especialidades', 'especialidades.editar', 'Editar especialidades', 'Editar especialidades existentes'],

            ['Tipos Servicio', 'tipo_servicios.ver', 'Ver tipos de servicio', 'Ver listado de tipos de servicio'],
            ['Tipos Servicio', 'tipo_servicios.crear', 'Crear tipos de servicio', 'Crear nuevos tipos de servicio'],
            ['Tipos Servicio', 'tipo_servicios.editar', 'Editar tipos de servicio', 'Editar tipos de servicio'],

            ['Métodos Pago', 'metodos_pago.ver', 'Ver métodos de pago', 'Ver listado de métodos de pago'],
            ['Métodos Pago', 'metodos_pago.crear', 'Crear métodos de pago', 'Crear nuevos métodos de pago'],
            ['Métodos Pago', 'metodos_pago.editar', 'Editar métodos de pago', 'Editar métodos de pago'],
        ];

        foreach ($items as [$modulo, $codigo, $nombre, $descripcion]) {
            permisos::firstOrCreate(
                ['codigo' => $codigo],
                ['nombre' => $nombre, 'modulo' => $modulo, 'descripcion' => $descripcion],
            );
        }
    }
}
