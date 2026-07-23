<?php

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

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/gerente/dashboard', function () {
        return view('gerente.dashboard');
    })->name('gerente.dashboard');

    Route::get('/recepcion/dashboard', function () {
        return view('recepcion.dashboard');
    })->name('recepcion.dashboard');

    Route::get('/mecanico/dashboard', function () {
        return view('mecanico.dashboard');
    })->name('mecanico.dashboard');
});
