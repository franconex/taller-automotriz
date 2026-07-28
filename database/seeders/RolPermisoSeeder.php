<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Rol::where('nombre', 'Administrador')->first();
        $gerente = Rol::where('nombre', 'Gerente')->first();
        $recepcionista = Rol::where('nombre', 'Recepcionista')->first();
        $mecanico = Rol::where('nombre', 'Mecánico')->first();

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
                'servicios.ver',
                'servicios.crear',
                'servicios.editar',
                'tipos-servicio.ver',
                'tipos-servicio.crear',
                'tipos-servicio.editar',
                'proveedores.ver',
                'proveedores.crear',
                'proveedores.editar',
                'repuestos.ver',
                'repuestos.crear',
                'repuestos.editar',
                'empleados.ver',
                'empleados.crear',
                'empleados.editar',
                'mecanicos.ver',
                'mecanicos.crear',
                'mecanicos.editar',
                'configuracion.ver',
                'configuracion.editar',
                'metodos-pago.ver',
                'metodos-pago.editar',
                'comprobantes.ver',
                'comprobantes.editar',
                'precios.ver',
                'citas.confirmar',
                'citas.asignar_mecanico',
                'citas.registrar_llegada',
                'ordenes.atencion_directa',
                'ordenes.servicios_asignar',
                'ordenes.repuestos_asignar',
                'ordenes.estimar_tiempo',
                'ordenes.ver_estimacion',
                'subservicios.ver',
                'subservicios.crear',
                'subservicios.editar',
                'estimaciones.supervisar',
            ])->pluck('id')
        );

        // Recepcionista: clientes, vehículos, citas, órdenes (crear), pagos (registrar)
        $recepcionista->permisos()->sync(
            Permiso::whereIn('codigo', [
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
                'servicios.ver',
                'tipos-servicio.ver',
                'proveedores.ver',
                'repuestos.ver',
                'mecanicos.ver',
                'metodos-pago.ver',
                'comprobantes.ver',
                'citas.confirmar',
                'citas.asignar_mecanico',
                'citas.registrar_llegada',
                'ordenes.atencion_directa',
                'ordenes.ver_estimacion',
                'subservicios.ver',
            ])->pluck('id')
        );

        // Mecánico: solo asignaciones, diagnóstico, servicios y repuestos (solo lectura)
        $mecanico->permisos()->sync(
            Permiso::whereIn('codigo', [
                'dashboard.ver',
                'ordenes.ver',
                'ordenes.actualizar_estado',
                'inventario.ver',
                'servicios.ver',
                'tipos-servicio.ver',
                'proveedores.ver',
                'repuestos.ver',
                'empleados.ver',
                'mecanicos.ver',
                'metodos-pago.ver',
                'comprobantes.ver',
                'ordenes.estimar_tiempo',
                'ordenes.ver_estimacion',
                'subservicios.ver',
                'ordenes.servicios_asignar',
                'ordenes.repuestos_asignar',
            ])->pluck('id')
        );
    }
}
