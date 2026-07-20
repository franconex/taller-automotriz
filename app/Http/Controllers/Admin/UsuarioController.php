<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserPasswordRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Rol;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function index()
    {
        $usuarios = User::with('rol:id,nombre')
            ->withCount('empleado')
            ->orderByDesc('id')
            ->paginate(15);

        $roles = Rol::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    public function create()
    {
        $roles = Rol::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $usuario = User::create($data);

        $this->auditService->register(
            'crear',
            'Usuario',
            $usuario->id,
            null,
            $request->safe()->except(['password', 'password_confirmation']),
            "Usuario {$usuario->nombre} creado",
        );

        return to_route('admin.usuarios.index')
            ->with('success', "Usuario {$usuario->nombre} creado correctamente.");
    }

    public function show(User $usuario)
    {
        $usuario->load('rol:id,nombre', 'empleado.sucursal:id,nombre');

        return view('admin.usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $roles = Rol::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $usuario)
    {
        $anterior = $usuario->only(['rol_id', 'nombre', 'username', 'email', 'estado']);
        $usuario->update($request->validated());

        $this->auditService->register(
            'editar',
            'Usuario',
            $usuario->id,
            $anterior,
            $request->safe()->toArray(),
            "Usuario {$usuario->nombre} editado",
        );

        return to_route('admin.usuarios.index')
            ->with('success', "Usuario {$usuario->nombre} actualizado correctamente.");
    }

    public function toggleEstado(User $usuario)
    {
        $this->authorize('desactivar', $usuario);

        $usuario->update(['estado' => ! $usuario->estado]);

        $accion = $usuario->estado ? 'activar' : 'desactivar';

        $this->auditService->register(
            $accion,
            'Usuario',
            $usuario->id,
            ['estado' => ! $usuario->estado],
            ['estado' => $usuario->estado],
            "Usuario {$usuario->nombre} {$accion}do",
        );

        $mensaje = $usuario->estado
            ? "Usuario {$usuario->nombre} activado correctamente."
            : "Usuario {$usuario->nombre} desactivado correctamente.";

        return to_route('admin.usuarios.index')->with('success', $mensaje);
    }

    public function updatePassword(UpdateUserPasswordRequest $request, User $usuario)
    {
        $usuario->update([
            'password' => Hash::make($request->password),
            'debe_cambiar_password' => true,
        ]);

        $this->auditService->register(
            'restablecer_password',
            'Usuario',
            $usuario->id,
            null,
            null,
            "Contraseña restablecida para {$usuario->nombre}",
        );

        return to_route('admin.usuarios.index')
            ->with('success', "Contraseña de {$usuario->nombre} restablecida correctamente.");
    }

    public function cambiarRol(UpdateUserRequest $request, User $usuario)
    {
        $anterior = $usuario->only('rol_id');
        $usuario->update(['rol_id' => $request->rol_id]);

        $rolNuevo = Rol::find($request->rol_id);

        $this->auditService->register(
            'cambiar_rol',
            'Usuario',
            $usuario->id,
            $anterior,
            ['rol_id' => $request->rol_id],
            "Rol de {$usuario->nombre} cambiado a {$rolNuevo?->nombre}",
        );

        return to_route('admin.usuarios.index')
            ->with('success', "Rol de {$usuario->nombre} actualizado correctamente.");
    }
}
