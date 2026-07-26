<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfiguracionController extends AdminController
{
    public function __construct()
    {
        $this->middleware('permiso:roles.editar');
    }
    public function index(): View
    {
        $config = [
            'razon_social' => Setting::obtener('razon_social', ''),
            'nit' => Setting::obtener('nit', ''),
            'direccion' => Setting::obtener('direccion', ''),
            'telefono' => Setting::obtener('telefono', ''),
            'email' => Setting::obtener('email', ''),
            'zona_horaria' => Setting::obtener('zona_horaria', 'America/La_Paz'),
            'moneda' => Setting::obtener('moneda', 'BOB — Boliviano'),
            'formato_fecha' => Setting::obtener('formato_fecha', 'd/m/Y'),
        ];

        return view('admin.configuracion.index', [
            'config' => $config,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'razon_social' => ['nullable', 'string', 'max:150'],
            'nit' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'zona_horaria' => ['nullable', 'string', 'max:50'],
            'moneda' => ['nullable', 'string', 'max:50'],
            'formato_fecha' => ['nullable', 'string', 'max:20'],
        ], [
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
            'email' => 'El campo :attribute debe ser un correo válido.',
        ], [
            'razon_social' => 'razón social',
        ]);

        foreach ($datos as $clave => $valor) {
            Setting::guardar($clave, $valor ?? '', 'texto', 'general');
        }

        return back()->with('success', 'La configuración fue guardada correctamente.');
    }
}
