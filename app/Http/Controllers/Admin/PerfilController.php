<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Services\AuditService;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function edit()
    {
        $usuario = auth()->user()->load('rol:id,nombre');

        return view('admin.perfil.edit', compact('usuario'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $usuario = auth()->user();

        $data = $request->safe()->only(['nombre', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($data['email'] !== $usuario->email) {
            $data['email_verified_at'] = null;
        }

        $usuario->update($data);

        $this->auditService->register(
            'editar_perfil',
            'Usuario',
            $usuario->id,
            null,
            $request->safe()->except(['password', 'password_confirmation', 'current_password']),
            'Perfil actualizado',
        );

        return to_route('admin.perfil.edit')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}
