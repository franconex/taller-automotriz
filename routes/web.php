<?php

use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\CitaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\ComprobanteController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\CotizacionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmpleadoController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\MecanicoController;
use App\Http\Controllers\Admin\MetodoPagoController;
use App\Http\Controllers\Admin\MovimientoInventarioController;
use App\Http\Controllers\Admin\OrdenCompraController;
use App\Http\Controllers\Admin\OrdenTrabajoController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\RepuestoController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\ServicioController;
use App\Http\Controllers\Admin\SolicitudCompraController;
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
        ->middleware('rol:Administrador,Gerente,Recepcionista,Mecánico')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->middleware('permiso:dashboard.ver')
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
            Route::patch('ordenes/{ordene}/toggle', [OrdenTrabajoController::class, 'toggle'])
                ->name('ordenes.toggle');
            Route::patch('ordenes/{ordene}/estado', [OrdenTrabajoController::class, 'cambiarEstadoOrden'])
                ->name('ordenes.cambiar-estado');
            Route::patch('ordenes/{ordene}/cancelar', [OrdenTrabajoController::class, 'cancelar'])
                ->name('ordenes.cancelar');

            Route::get('ordenes/{orden}/repuestos', [OrdenTrabajoController::class, 'editRepuestos'])
                ->name('ordenes.repuestos');
            Route::get('ordenes/{orden}/repuestos-json', [OrdenTrabajoController::class, 'repuestosJson'])
                ->name('ordenes.repuestos-json');
            Route::post('ordenes/{orden}/detalles', [OrdenTrabajoController::class, 'agregarDetalle'])
                ->name('ordenes.detalles.store');
            Route::delete('ordenes/{orden}/detalles/{detalle}', [OrdenTrabajoController::class, 'eliminarDetalle'])
                ->name('ordenes.detalles.destroy');
            Route::get('ordenes/{orden}/sugerir-compra', [OrdenTrabajoController::class, 'sugerirCompra'])
                ->name('ordenes.sugerir-compra');

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

            Route::get('repuestos/escaner/buscar', [RepuestoController::class, 'buscarPorEscaner'])
                ->name('repuestos.escaner-buscar');
            Route::get('repuestos/buscar-por-codigo', [RepuestoController::class, 'buscarPorEscaner'])
                ->name('repuestos.buscar-por-codigo');
            Route::resource('repuestos', RepuestoController::class)->except(['index', 'show']);

            Route::resource('inventario', InventarioController::class)->except(['create', 'store']);
            Route::post('inventario/entrada-rapida', [InventarioController::class, 'entradaRapida'])
                ->name('inventario.entrada-rapida');
            Route::post('inventario/entrada', [InventarioController::class, 'registrarEntrada'])
                ->name('inventario.entrada');

            Route::post('inventario/crear-desde-escaner', [InventarioController::class, 'crearDesdeEscaner'])
                ->name('inventario.crear-desde-escaner');

            Route::resource('movimientos-inventario', MovimientoInventarioController::class)
                ->parameters(['movimientos-inventario' => 'movimiento'])
                ->except(['edit', 'update']);
            Route::get('movimientos-inventario/{movimiento}/route', [MovimientoInventarioController::class, 'route'])
                ->name('movimientos-inventario.route');

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
            Route::get('pagos/{pago}/qr', [\App\Http\Controllers\Admin\PagoQRController::class, 'mostrar'])
                ->name('pagos.qr');
            Route::post('pagos/qr-data', [\App\Http\Controllers\Admin\PagoQRController::class, 'qrData'])
                ->name('pagos.qr-data');

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

            Route::resource('solicitudes-compra', SolicitudCompraController::class)
                ->parameters(['solicitudes-compra' => 'solicitud']);
            Route::patch('solicitudes-compra/{solicitud}/aprobar', [SolicitudCompraController::class, 'aprobar'])
                ->name('solicitudes-compra.aprobar');
            Route::patch('solicitudes-compra/{solicitud}/rechazar', [SolicitudCompraController::class, 'rechazar'])
                ->name('solicitudes-compra.rechazar');

            Route::resource('cotizaciones', CotizacionController::class)->except(['index', 'destroy']);
            Route::patch('cotizaciones/{cotizacione}/seleccionar', [CotizacionController::class, 'seleccionar'])
                ->name('cotizaciones.seleccionar');

            Route::resource('ordenes-compra', OrdenCompraController::class)
                ->parameters(['ordenes-compra' => 'orden'])
                ->except(['create', 'store', 'edit', 'update', 'destroy']);
            Route::patch('ordenes-compra/{orden}/enviar', [OrdenCompraController::class, 'marcarEnviada'])
                ->name('ordenes-compra.enviar');
            Route::get('ordenes-compra/{orden}/recibir', [OrdenCompraController::class, 'recibir'])
                ->name('ordenes-compra.recibir');
            Route::post('ordenes-compra/{orden}/procesar-recepcion', [OrdenCompraController::class, 'procesarRecepcion'])
                ->name('ordenes-compra.procesar-recepcion');
            Route::patch('ordenes-compra/{orden}/cancelar', [OrdenCompraController::class, 'cancelar'])
                ->name('ordenes-compra.cancelar');
        });

        // Citas: módulo accesible para cualquier usuario con permiso citas.ver
        // (Recepcionista, Administrador, Gerente). El control fino por acción
        // se hace con permisos (citas.crear, citas.editar, citas.cancelar, ordenes.crear).
        Route::middleware('permiso:citas.ver')
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {
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
            Route::resource('citas', CitaController::class)->except(['create', 'store', 'destroy']);
        });
});

