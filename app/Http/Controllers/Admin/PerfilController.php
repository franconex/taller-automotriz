<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PerfilRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PerfilController extends AdminController
{
    public function index(): View
    {
        $usuario = auth()->user()->load(['empleado.sucursal', 'empleado.rol', 'rol']);

        return view('admin.perfil.index', [
            'usuario' => $usuario,
        ]);
    }

    public function update(PerfilRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $datos = $request->validated();

        $user->username = $datos['username'];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ], [], [
                'password' => 'contraseña',
            ]);
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        if ($request->hasFile('foto') && $user->empleado) {
            $ruta = $request->file('foto')->store('fotos', 'public');
            $user->empleado->foto = $ruta;
            $user->empleado->save();
        }

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
