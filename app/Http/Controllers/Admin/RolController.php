<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RolRequest;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Rol::query();

        $this->aplicarFiltros($request, $query, ['estado']);
        $this->aplicarBusqueda($query, $request, [
            'nombre',
            'descripcion',
        ]);

        $roles = $query->withCount('users')->orderBy('nombre')->paginate(15)->withQueryString();

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.create', [
            'rol' => new \App\Models\Rol(),
        ]);
    }

    public function store(RolRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);

        Rol::create($datos);

        return $this->redirigirALista('admin.roles.index', 'Rol creado con éxito.');
    }

    public function edit(Rol $role): View
    {
        return view('admin.roles.edit', [
            'rol' => $role,
        ]);
    }

    public function update(RolRequest $request, Rol $role): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $role->update($datos);

        return $this->redirigirConExito('roles', 'actualizado');
    }

    public function destroy(Rol $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return back()->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
        }

        $role->delete();

        return $this->redirigirConExito('roles', 'eliminado');
    }

    public function toggle(Request $request, Rol $role): RedirectResponse
    {
        return $this->cambiarEstado($request, $role, 'roles');
    }

    public function permisos(Rol $role): View
    {
        $permisos = Permiso::orderBy('modulo')->orderBy('nombre')->get()->groupBy('modulo');
        $asignados = $role->permisos()->pluck('permisos.id')->toArray();

        return view('admin.roles.permisos', [
            'rol' => $role,
            'permisos' => $permisos,
            'asignados' => $asignados,
        ]);
    }

    public function actualizarPermisos(Request $request, Rol $role): RedirectResponse
    {
        $permisos = $request->input('permisos', []);
        $role->permisos()->sync($permisos);

        return back()->with('success', 'Los permisos del rol fueron actualizados correctamente.');
    }
}
