<?php

use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmpleadoController;
use App\Http\Controllers\Admin\EspecialidadController;
use App\Http\Controllers\Admin\MetodoPagoController;
use App\Http\Controllers\Admin\PerfilController;
use App\Http\Controllers\Admin\PermisoController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\SucursalController;
use App\Http\Controllers\Admin\TipoServicioController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'inicio')->name('inicio');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $roleRoutes = [
            'Administrador' => 'admin.dashboard',
            'Gerente' => 'gerente.dashboard',
            'Recepcionista' => 'recepcionista.dashboard',
            'Mecanico' => 'mecanico.dashboard',
        ];

        $roleName = auth('web')->user()?->rol?->nombre;

        if (! $roleName || ! array_key_exists($roleName, $roleRoutes)) {
            abort(403, 'Rol desconocido.');
        }

        return redirect()->route($roleRoutes[$roleName]);
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->middleware(['rol:Administrador'])
            ->name('dashboard');

        Route::middleware(['rol:Administrador'])->group(function () {
            Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
            Route::get('usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
            Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
            Route::get('usuarios/{usuario}', [UsuarioController::class, 'show'])->name('usuarios.show');
            Route::get('usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
            Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
            Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'toggleEstado'])->name('usuarios.estado');
            Route::put('usuarios/{usuario}/password', [UsuarioController::class, 'updatePassword'])->name('usuarios.password');
            Route::patch('usuarios/{usuario}/rol', [UsuarioController::class, 'cambiarRol'])->name('usuarios.rol');

            Route::get('roles', [RolController::class, 'index'])->name('roles.index');
            Route::get('roles/crear', [RolController::class, 'create'])->name('roles.create');
            Route::post('roles', [RolController::class, 'store'])->name('roles.store');
            Route::get('roles/{rol}', [RolController::class, 'show'])->name('roles.show');
            Route::get('roles/{rol}/editar', [RolController::class, 'edit'])->name('roles.edit');
            Route::put('roles/{rol}', [RolController::class, 'update'])->name('roles.update');
            Route::patch('roles/{rol}/estado', [RolController::class, 'toggleEstado'])->name('roles.estado');
            Route::put('roles/{rol}/permisos', [RolController::class, 'asignarPermisos'])->name('roles.permisos');

            Route::get('permisos', [PermisoController::class, 'index'])->name('permisos.index');

            Route::get('auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');

            Route::get('sucursales', [SucursalController::class, 'index'])->name('sucursales.index');
            Route::get('sucursales/crear', [SucursalController::class, 'create'])->name('sucursales.create');
            Route::post('sucursales', [SucursalController::class, 'store'])->name('sucursales.store');
            Route::get('sucursales/{sucursale}', [SucursalController::class, 'show'])->name('sucursales.show');
            Route::get('sucursales/{sucursale}/editar', [SucursalController::class, 'edit'])->name('sucursales.edit');
            Route::put('sucursales/{sucursale}', [SucursalController::class, 'update'])->name('sucursales.update');
            Route::patch('sucursales/{sucursale}/estado', [SucursalController::class, 'toggleEstado'])->name('sucursales.estado');

            Route::get('especialidades', [EspecialidadController::class, 'index'])->name('especialidades.index');
            Route::get('especialidades/crear', [EspecialidadController::class, 'create'])->name('especialidades.create');
            Route::post('especialidades', [EspecialidadController::class, 'store'])->name('especialidades.store');
            Route::get('especialidades/{especialidade}/editar', [EspecialidadController::class, 'edit'])->name('especialidades.edit');
            Route::put('especialidades/{especialidade}', [EspecialidadController::class, 'update'])->name('especialidades.update');
            Route::patch('especialidades/{especialidade}/estado', [EspecialidadController::class, 'toggleEstado'])->name('especialidades.estado');

            Route::get('tipo-servicios', [TipoServicioController::class, 'index'])->name('tipo-servicios.index');
            Route::get('tipo-servicios/crear', [TipoServicioController::class, 'create'])->name('tipo-servicios.create');
            Route::post('tipo-servicios', [TipoServicioController::class, 'store'])->name('tipo-servicios.store');
            Route::get('tipo-servicios/{tipoServicio}/editar', [TipoServicioController::class, 'edit'])->name('tipo-servicios.edit');
            Route::put('tipo-servicios/{tipoServicio}', [TipoServicioController::class, 'update'])->name('tipo-servicios.update');
            Route::patch('tipo-servicios/{tipoServicio}/estado', [TipoServicioController::class, 'toggleEstado'])->name('tipo-servicios.estado');

            Route::get('metodos-pago', [MetodoPagoController::class, 'index'])->name('metodos-pago.index');
            Route::get('metodos-pago/crear', [MetodoPagoController::class, 'create'])->name('metodos-pago.create');
            Route::post('metodos-pago', [MetodoPagoController::class, 'store'])->name('metodos-pago.store');
            Route::get('metodos-pago/{metodoPago}/editar', [MetodoPagoController::class, 'edit'])->name('metodos-pago.edit');
            Route::put('metodos-pago/{metodoPago}', [MetodoPagoController::class, 'update'])->name('metodos-pago.update');
            Route::patch('metodos-pago/{metodoPago}/estado', [MetodoPagoController::class, 'toggleEstado'])->name('metodos-pago.estado');

            Route::get('perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
            Route::put('perfil', [PerfilController::class, 'update'])->name('perfil.update');
        });

        Route::middleware(['permiso:empleados.ver'])->group(function () {
            Route::get('empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
            Route::get('empleados/crear', [EmpleadoController::class, 'create'])->name('empleados.create');
            Route::post('empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
            Route::get('empleados/{empleado}', [EmpleadoController::class, 'show'])->name('empleados.show');
            Route::get('empleados/{empleado}/editar', [EmpleadoController::class, 'edit'])->name('empleados.edit');
            Route::put('empleados/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update');
            Route::patch('empleados/{empleado}/estado', [EmpleadoController::class, 'toggleEstado'])->name('empleados.estado');
            Route::patch('empleados/{empleado}/cambiar-rol', [EmpleadoController::class, 'cambiarRol'])->name('empleados.cambiar-rol');
        });

        Route::middleware(['permiso:clientes.ver'])->group(function () {
            Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
            Route::get('clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
            Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
            Route::get('clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
            Route::get('clientes/{cliente}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
            Route::put('clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
            Route::patch('clientes/{cliente}/estado', [ClienteController::class, 'toggleEstado'])->name('clientes.estado');
        });
    });

    Route::get('/gerente', function () {
        return view('gerente.dashboard');
    })->middleware(['rol:Gerente'])->name('gerente.dashboard');

    Route::get('/recepcionista', function () {
        return view('recepcionista.dashboard');
    })->middleware(['rol:Recepcionista'])->name('recepcionista.dashboard');

    Route::get('/mecanico', function () {
        return view('mecanico.dashboard');
    })->middleware(['rol:Mecanico'])->name('mecanico.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
