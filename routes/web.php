<?php
use App\Models\User;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'inicio')->name('inicio');

Route::middleware('auth')->group(function () {


    Route::get('/dashboard', function () {
        $roleRoutes = [
            'Administrador' => 'admin.dashboard',
            'Gerente'       => 'gerente.dashboard',
            'Recepcionista'  => 'recepcionista.dashboard',
            'Mecanico'      => 'mecanico.dashboard',
        ];

        $roleName = auth('web')->user()?->rol?->nombre;

        if (! $roleName || ! array_key_exists($roleName, $roleRoutes)) {
            abort(403, 'Rol desconocido.');
        }

        return redirect()->route($roleRoutes[$roleName]);
    })->name('dashboard');

    Route::get('/admin', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware(['rol:Administrador'])->name('admin.dashboard');

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
