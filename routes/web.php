<?php

use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\CitaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\ComprobanteController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmpleadoController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\MecanicoController;
use App\Http\Controllers\Admin\MetodoPagoController;
use App\Http\Controllers\Admin\MovimientoInventarioController;
use App\Http\Controllers\Admin\OrdenTrabajoController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\RepuestoController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\ServicioController;
use App\Http\Controllers\Admin\SucursalController;
use App\Http\Controllers\Admin\TipoServicioController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\VehiculoController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Dashboards por Roles
    Route::get('/gerente/dashboard', function () {
        return view('gerente.dashboard');
    })->middleware('rol:Gerente')->name('gerente.dashboard');

    Route::get('/recepcion/dashboard', function () {
        return view('recepcion.dashboard');
    })->middleware('rol:Recepcionista')->name('recepcion.dashboard');

    Route::get('/mecanico/dashboard', function () {
        return view('mecanico.dashboard');
    })->middleware('rol:Mecánico')->name('mecanico.dashboard');

    // Rutas operativas del mecanico
    Route::middleware('rol:Mecánico')->group(function () {
        Route::get('/mis-ordenes', [MecanicoController::class, 'misOrdenes'])->name('mecanico.mis_ordenes');
        Route::get('/mis-ordenes/{id}', [MecanicoController::class, 'atenderOrden'])->name('mecanico.atender');
        Route::put('/mis-ordenes/{id}/diagnostico', [MecanicoController::class, 'guardarDiagnostico'])->name('mecanico.diagnostico');
        Route::post('/mis-ordenes/{id}/repuestos', [MecanicoController::class, 'registrarRepuesto'])->name('mecanico.repuestos');
    });

    // Perfil general
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('perfil', [\App\Http\Controllers\Admin\PerfilController::class, 'index'])->name('perfil.index');
        Route::put('perfil', [\App\Http\Controllers\Admin\PerfilController::class, 'update'])->name('perfil.update');
    });


    // RUTAS EXCLUSIVAS DEL ADMINISTRADOR

    Route::prefix('admin')
        ->middleware('rol:Administrador')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');

            Route::resource('sucursales', SucursalController::class);
            Route::patch('sucursales/{sucursale}/toggle', [SucursalController::class, 'toggle'])
                ->name('sucursales.toggle');

            Route::resource('empleados', EmpleadoController::class)
                ->except(['destroy']);
            Route::patch('empleados/{empleado}/toggle', [EmpleadoController::class, 'toggle'])
                ->name('empleados.toggle');

            Route::resource('usuarios', UsuarioController::class);
            Route::patch('usuarios/{usuario}/toggle', [UsuarioController::class, 'toggle'])
                ->name('usuarios.toggle');
            Route::post('usuarios/{usuario}/restablecer-password', [UsuarioController::class, 'restablecerPassword'])
                ->name('usuarios.restablecer-password');

            Route::resource('roles', RolController::class);
            Route::patch('roles/{role}/toggle', [RolController::class, 'toggle'])
                ->name('roles.toggle');
            Route::get('roles/{role}/permisos', [RolController::class, 'permisos'])
                ->name('roles.permisos');
            Route::put('roles/{role}/permisos', [RolController::class, 'actualizarPermisos'])
                ->name('roles.actualizar-permisos');

            Route::resource('clientes', ClienteController::class);
            Route::patch('clientes/{cliente}/toggle', [ClienteController::class, 'toggle'])
                ->name('clientes.toggle');

            Route::resource('vehiculos', VehiculoController::class);
            Route::patch('vehiculos/{vehiculo}/toggle', [VehiculoController::class, 'toggle'])
                ->name('vehiculos.toggle');

            Route::resource('ordenes', OrdenTrabajoController::class);
            Route::patch('ordenes/{orden}/toggle', [OrdenTrabajoController::class, 'toggle'])
                ->name('ordenes.toggle');
            Route::patch('ordenes/{orden}/estado', [OrdenTrabajoController::class, 'cambiarEstadoOrden'])
                ->name('ordenes.cambiar-estado');
            Route::patch('ordenes/{orden}/cancelar', [OrdenTrabajoController::class, 'cancelar'])
                ->name('ordenes.cancelar');

            Route::resource('mecanicos', MecanicoController::class);
            Route::patch('mecanicos/{mecanico}/toggle', [MecanicoController::class, 'toggle'])
                ->name('mecanicos.toggle');

            Route::resource('tipos-servicio', TipoServicioController::class);
            Route::patch('tipos-servicio/{tipo_servicio}/toggle', [TipoServicioController::class, 'toggle'])
                ->name('tipos-servicio.toggle');

            Route::resource('servicios', ServicioController::class);
            Route::patch('servicios/{servicio}/toggle', [ServicioController::class, 'toggle'])
                ->name('servicios.toggle');

            Route::resource('proveedores', ProveedorController::class);
            Route::patch('proveedores/{proveedore}/toggle', [ProveedorController::class, 'toggle'])
                ->name('proveedores.toggle');

            Route::resource('repuestos', RepuestoController::class);
            Route::patch('repuestos/{repuesto}/toggle', [RepuestoController::class, 'toggle'])
                ->name('repuestos.toggle');

            Route::resource('inventario', InventarioController::class);
            Route::patch('inventario/{inventario}/toggle', [InventarioController::class, 'toggle'])
                ->name('inventario.toggle');

            Route::resource('movimientos-inventario', MovimientoInventarioController::class)
                ->parameters(['movimientos-inventario' => 'movimiento'])
                ->except(['edit', 'update']);

            Route::get('configuracion', [ConfiguracionController::class, 'index'])
                ->name('configuracion.index');
            Route::put('configuracion', [ConfiguracionController::class, 'update'])
                ->name('configuracion.update');
        });


    // RUTAS COMPARTIDAS (ADMIN Y GERENTE) BASADAS EN PERMISOS


    // Módulo de Pagos y Autorizaciones
    Route::middleware('permiso:pagos.ver')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('metodos-pago', MetodoPagoController::class)
                ->parameters(['metodos-pago' => 'metodoPago'])
                ->except(['create', 'store', 'destroy']);
            Route::patch('metodos-pago/{metodoPago}/toggle', [MetodoPagoController::class, 'toggle'])
                ->name('metodos-pago.toggle');

            Route::resource('pagos', PagoController::class);
            Route::patch('pagos/{pago}/toggle', [PagoController::class, 'toggle'])
                ->name('pagos.toggle');
            Route::patch('pagos/{pago}/anular', [PagoController::class, 'anular'])
                ->name('pagos.anular');

            Route::resource('comprobantes', ComprobanteController::class)
                ->except(['create', 'store']);
            Route::patch('comprobantes/{comprobante}/anular', [ComprobanteController::class, 'anular'])
                ->name('comprobantes.anular');
        });

    // Módulo de Reportes
    Route::middleware('permiso:reportes.ver')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('reportes', [ReporteController::class, 'index'])
                ->name('reportes.index');
            Route::get('reportes/{tipo}', [ReporteController::class, 'mostrar'])
                ->name('reportes.mostrar');
        });

    // Módulo de Auditoría
    Route::middleware('permiso:auditoria.ver')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('auditoria', [AuditoriaController::class, 'index'])
                ->name('auditoria.index');
            Route::get('auditoria/{auditoria}', [AuditoriaController::class, 'show'])
                ->name('auditoria.show');
        });

    // RUTAS COMPARTIDAS PARA CITAS

    Route::middleware('permiso:citas.ver')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('citas/eventos', [CitaController::class, 'eventos'])->name('citas.eventos');
            Route::get('citas/tabla-dia', [CitaController::class, 'tablaDia'])->name('citas.tabla-dia');
            Route::get('citas/proximas', [CitaController::class, 'proximas'])->name('citas.proximas');
            Route::post('citas', [CitaController::class, 'store'])
                ->middleware('permiso:citas.crear')
                ->name('citas.store');
            Route::resource('citas', CitaController::class)->except(['create', 'store', 'destroy']);
            Route::put('citas/{cita}/reprogramar', [CitaController::class, 'reprogramar'])
                ->middleware('permiso:citas.editar')
                ->name('citas.reprogramar');
            Route::patch('citas/{cita}/confirmar', [CitaController::class, 'confirmar'])
                ->middleware('permiso:citas.editar')
                ->name('citas.confirmar');
            Route::patch('citas/{cita}/cancelar', [CitaController::class, 'cancelar'])
                ->middleware('permiso:citas.cancelar')
                ->name('citas.cancelar');
            Route::patch('citas/{cita}/no-asistio', [CitaController::class, 'marcarNoAsistio'])
                ->middleware('permiso:citas.editar')
                ->name('citas.no-asistio');
            Route::post('citas/{cita}/convertir-orden', [CitaController::class, 'convertirEnOrden'])
                ->middleware('permiso:ordenes.crear')
                ->name('citas.convertir-orden');
        });
});