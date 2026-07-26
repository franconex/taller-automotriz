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

    Route::get('/gerente/dashboard', function () {
        return view('gerente.dashboard');
    })->middleware('rol:Gerente')->name('gerente.dashboard');

    Route::get('/recepcion/dashboard', function () {
        return view('recepcion.dashboard');
    })->middleware('rol:Recepcionista')->name('recepcion.dashboard');

    Route::get('/mecanico/dashboard', function () {
        return view('mecanico.dashboard');
    })->middleware('rol:Mecánico')->name('mecanico.dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('perfil', [\App\Http\Controllers\Admin\PerfilController::class, 'index'])->name('perfil.index');
        Route::put('perfil', [\App\Http\Controllers\Admin\PerfilController::class, 'update'])->name('perfil.update');
        Route::post('perfil/foto', [\App\Http\Controllers\Admin\PerfilController::class, 'guardarFoto'])->name('perfil.foto');
        Route::get('modelos-json', function () {
            return \App\Models\ModeloVehiculo::with('marca', 'tipoVehiculo')
                ->where('estado', true)
                ->orderBy('nombre')
                ->get()
                ->map(fn ($m) => [
                    'id' => (string) $m->id,
                    'marca' => $m->marca->nombre ?? '',
                    'nombre' => $m->nombre,
                    'tipo_vehiculo_id' => $m->tipo_vehiculo_id ? (string) $m->tipo_vehiculo_id : null,
                ]);
        })->name('modelos.json');
    });

    Route::prefix('admin')
        ->middleware('rol:Administrador')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');

            Route::resource('sucursales', SucursalController::class);
            Route::patch('sucursales/{sucursale}/toggle', [SucursalController::class, 'toggle'])
                ->name('sucursales.toggle');

            Route::resource('empleados', EmpleadoController::class);
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

            // (citas movido fuera del grupo admin, ver bloque más abajo)

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

            Route::resource('metodos-pago', MetodoPagoController::class)
                ->parameters(['metodos-pago' => 'metodoPago'])
                ->except(['create', 'store', 'destroy']);
            Route::patch('metodos-pago/{metodoPago}/toggle', [MetodoPagoController::class, 'toggle'])
                ->name('metodos-pago.toggle');

            Route::resource('pagos', PagoController::class);
            Route::patch('pagos/{pago}/toggle', [PagoController::class, 'toggle'])
                ->name('pagos.toggle');
            Route::post('pagos/stripe/cobrar', [\App\Http\Controllers\Admin\PagoStripeController::class, 'cobrar'])->middleware('permiso:pagos.registrar')->name('pagos.stripe.cobrar');
            Route::patch('pagos/{pago}/anular', [PagoController::class, 'anular'])
                ->name('pagos.anular');

            Route::resource('comprobantes', ComprobanteController::class)
                ->except(['create', 'store']);
            Route::patch('comprobantes/{comprobante}/anular', [ComprobanteController::class, 'anular'])
                ->name('comprobantes.anular');

            Route::get('configuracion', [ConfiguracionController::class, 'index'])
                ->name('configuracion.index');
            Route::put('configuracion', [ConfiguracionController::class, 'update'])
                ->name('configuracion.update');

            Route::get('reportes', [ReporteController::class, 'index'])
                ->name('reportes.index');
            Route::get('reportes/{tipo}', [ReporteController::class, 'mostrar'])
                ->name('reportes.mostrar');

            Route::get('auditoria', [AuditoriaController::class, 'index'])
                ->name('auditoria.index');
            Route::get('auditoria/{auditoria}', [AuditoriaController::class, 'show'])
                ->name('auditoria.show');
        });

        // Citas: módulo accesible para cualquier usuario con permiso citas.ver
        // (Recepcionista, Administrador, Gerente). El control fino por acción
        // se hace con permisos (citas.crear, citas.editar, citas.cancelar, ordenes.crear).
        Route::middleware('permiso:citas.ver')
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {
            // Rutas específicas ANTES del resource para evitar conflictos con {cita}
            Route::get('citas/eventos', [CitaController::class, 'eventos'])->name('citas.eventos');
            Route::get('citas/tabla-dia', [CitaController::class, 'tablaDia'])->name('citas.tabla-dia');
            Route::get('citas/proximas', [CitaController::class, 'proximas'])->name('citas.proximas');
            Route::post('citas/quick-cliente', [CitaController::class, 'quickCliente'])
                ->middleware('permiso:citas.crear')
                ->name('citas.quick-cliente');
            Route::post('citas/quick-vehiculo', [CitaController::class, 'quickVehiculo'])
                ->middleware('permiso:citas.crear')
                ->name('citas.quick-vehiculo');
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

