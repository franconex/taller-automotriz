<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PerfilRequest;
use App\Models\Perfil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PerfilController extends AdminController
{
    public function index(): View
    {
        $user = auth()->user();
        $user->load(['empleado.sucursal', 'empleado.rol', 'rol']);

        Perfil::firstOrCreate(['user_id' => $user->id]);

        $user->load('perfil');

        return view('admin.perfil.index', [
            'usuario' => $user,
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
            ], [
                'required' => 'La contraseña es obligatoria.',
                'string' => 'La contraseña debe ser texto.',
                'min' => 'La contraseña debe tener al menos :min caracteres.',
                'confirmed' => 'La confirmación de la contraseña no coincide.',
            ]);
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        $perfil = Perfil::firstOrCreate(['user_id' => $user->id]);

        if ($request->boolean('eliminar_foto')) {
            $perfil->foto = null;
        }

        $perfil->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function guardarFoto(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $foto = $request->input('foto');

        if (! $foto) {
            return response()->json(['ok' => false, 'message' => 'No se recibió la foto.'], 400);
        }

        $error = $this->validarFotoBase64($foto);
        if ($error) {
            return response()->json(['ok' => false, 'message' => $error], 422);
        }

        $perfil = Perfil::firstOrCreate(['user_id' => $user->id]);

        $perfil->foto = $foto;
        $perfil->save();

        return response()->json(['ok' => true, 'message' => 'Foto guardada.']);
    }

    private function validarFotoBase64(string $dataUri): ?string
    {
        if (! str_starts_with($dataUri, 'data:image/')) {
            return 'La foto debe ser una imagen válida.';
        }

        $partes = explode(',', $dataUri, 2);
        if (count($partes) !== 2) {
            return 'La foto debe ser una imagen válida.';
        }

        $decoded = base64_decode($partes[1], true);
        if ($decoded === false) {
            return 'La foto contiene datos corruptos.';
        }

        if (strlen($decoded) > 500 * 1024) {
            return 'La foto no debe superar los 500 KB después de redimensionar.';
        }

        if (! str_contains($dataUri, 'image/')) {
            return 'La foto debe ser una imagen válida.';
        }

        return null;
    }
}
