<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UsuarioRequest;
use App\Models\Empleado;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioController extends AdminController
{
    public function __construct()
    {
        $this->middleware('permiso:usuarios.crear')->only(['create', 'store']);
        $this->middleware('permiso:usuarios.editar')->only(['edit', 'update', 'restablecerPassword']);
        $this->middleware('permiso:usuarios.desactivar')->only(['destroy', 'toggle']);
    }
    public function index(Request $request): View
    {
        $query = User::query()->with(['rol', 'sucursal']);

        $this->scopeSucursal($query, 'sucursal_id');
        $this->aplicarFiltros($request, $query, ['estado', 'rol_id']);
        $this->aplicarBusqueda($query, $request, [
            'nombre',
            'username',
            'email',
        ]);

        $usuarios = $query->orderBy('nombre')->paginate(15)->withQueryString();

        $roles = Rol::orderBy('nombre')->get();

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
        ]);
    }

    public function create(): View
    {
        $empleados = Empleado::query()
            ->with('rol', 'sucursal')
            ->whereDoesntHave('user')
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->orderBy('nombre_completo')
            ->get();

        $empleadosData = $empleados->map(fn ($e) => [
            'id' => $e->id,
            'nombre_completo' => $e->nombre_completo,
            'email' => $e->email,
            'rol_id' => $e->rol_id,
            'rol_nombre' => $e->rol?->nombre ?? '—',
            'sucursal_id' => $e->sucursal_id,
            'sucursal_nombre' => $e->sucursal?->nombre ?? '—',
        ]);

        return view('admin.usuarios.create', [
            'empleados' => $empleados,
            'empleadosData' => $empleadosData,
            'usuario' => new \App\Models\User(),
        ]);
    }

    public function store(UsuarioRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $empleado = Empleado::findOrFail($datos['empleado_id']);

        $datos['nombre'] = $empleado->nombre_completo;
        $datos['email'] = $empleado->email ?? $empleado->nombre_completo.'@tallerpro.com';
        $datos['rol_id'] = $empleado->rol_id;
        $datos['sucursal_id'] = $empleado->sucursal_id;
        $datos['password'] = Hash::make($datos['password']);
        $datos['estado'] = $datos['estado'] ?? 'activo';
        unset($datos['password_confirmation']);

        User::create($datos);

        return $this->redirigirALista('admin.usuarios.index', 'Usuario creado con éxito.');
    }

    public function show(User $usuario): View
    {
        $usuario->load(['rol', 'sucursal', 'empleado']);

        return view('admin.usuarios.show', [
            'usuario' => $usuario,
        ]);
    }

    public function edit(User $usuario): View
    {
        $empleados = Empleado::query()
            ->with('rol', 'sucursal')
            ->where(function ($q) use ($usuario) {
                $q->whereDoesntHave('user')
                  ->orWhere('id', $usuario->empleado_id);
            })
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->orderBy('nombre_completo')
            ->get();

        $empleadosData = $empleados->map(fn ($e) => [
            'id' => $e->id,
            'nombre_completo' => $e->nombre_completo,
            'email' => $e->email,
            'rol_id' => $e->rol_id,
            'rol_nombre' => $e->rol?->nombre ?? '—',
            'sucursal_id' => $e->sucursal_id,
            'sucursal_nombre' => $e->sucursal?->nombre ?? '—',
        ]);

        return view('admin.usuarios.edit', [
            'usuario' => $usuario,
            'empleados' => $empleados,
            'empleadosData' => $empleadosData,
        ]);
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        $datos = $request->validated();
        $empleado = Empleado::findOrFail($datos['empleado_id']);

        $datos['nombre'] = $empleado->nombre_completo;
        $datos['email'] = $empleado->email ?? $usuario->email;
        $datos['rol_id'] = $empleado->rol_id;
        $datos['sucursal_id'] = $empleado->sucursal_id;

        if (! empty($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        } else {
            unset($datos['password']);
        }

        unset($datos['password_confirmation']);
        $datos['estado'] = $datos['estado'] ?? $usuario->estado;

        if ($datos['estado'] === 'activo' && $empleado && !$empleado->estado) {
            return back()->with('error', 'No se puede activar el usuario porque el empleado asociado está dado de baja.')->withInput();
        }

        $usuario->update($datos);

        return $this->redirigirConExito('usuarios', 'actualizado');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return $this->redirigirConExito('usuarios', 'eliminado');
    }

    public function toggle(Request $request, User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes cambiar tu propio estado.');
        }

        $nuevoEstado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';

        if ($nuevoEstado === 'activo' && $usuario->empleado && !$usuario->empleado->estado) {
            return back()->with('error', 'No se puede activar el usuario porque el empleado asociado está dado de baja.');
        }

        $usuario->estado = $nuevoEstado;
        $usuario->save();

        return back()->with('success', "El usuario fue {$usuario->estado} correctamente.");
    }

    public function restablecerPassword(Request $request, User $usuario): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'required' => 'La contraseña es obligatoria.',
            'string' => 'La contraseña debe ser texto.',
            'min' => 'La contraseña debe tener al menos :min caracteres.',
            'confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $usuario->password = Hash::make($request->input('password'));
        $usuario->save();

        return back()->with('success', 'La contraseña fue restablecida correctamente.');
    }
}
