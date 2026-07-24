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
        $roles = Rol::orderBy('nombre')->get();
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();
        $empleados = Empleado::query()
            ->whereDoesntHave('user')
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->orderBy('nombre_completo')
            ->get();

        return view('admin.usuarios.create', [
            'roles' => $roles,
            'sucursales' => $sucursales,
            'empleados' => $empleados,
            'usuario' => new \App\Models\User(),
        ]);
    }

    public function store(UsuarioRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['password'] = Hash::make($datos['password']);
        $datos['estado'] = $datos['estado'] ?? 'activo';
        unset($datos['password_confirmation']);

        User::create($datos);

        return $this->redirigirConExito('usuarios', 'registrado');
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
        $roles = Rol::orderBy('nombre')->get();
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();
        $empleados = Empleado::query()
            ->where(function ($q) use ($usuario) {
                $q->whereDoesntHave('user')
                  ->orWhere('id', $usuario->empleado_id);
            })
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->orderBy('nombre_completo')
            ->get();

        return view('admin.usuarios.edit', [
            'usuario' => $usuario,
            'roles' => $roles,
            'sucursales' => $sucursales,
            'empleados' => $empleados,
        ]);
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        $datos = $request->validated();

        if (! empty($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        } else {
            unset($datos['password']);
        }

        unset($datos['password_confirmation']);
        $datos['estado'] = $datos['estado'] ?? $usuario->estado;

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

        $usuario->estado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        $usuario->save();

        return back()->with('success', "El usuario fue {$usuario->estado} correctamente.");
    }

    public function restablecerPassword(Request $request, User $usuario): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [], [
            'password' => 'contraseña',
        ]);

        $usuario->password = Hash::make($request->input('password'));
        $usuario->save();

        return back()->with('success', 'La contraseña fue restablecida correctamente.');
    }
}
