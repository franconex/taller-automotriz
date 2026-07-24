<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\EmpleadoRequest;
use App\Models\Empleado;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmpleadoController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Empleado::query()->with('sucursal', 'user');

        $this->scopeSucursal($query, 'sucursal_id');
        $this->aplicarFiltros($request, $query, ['estado', 'sucursal_id']);
        $this->aplicarBusqueda($query, $request, [
            'nombre_completo',
            'ci',
            'cargo',
            'telefono',
            'email',
        ]);

        $empleados = $query->orderBy('nombre_completo')->paginate(15)->withQueryString();

        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.empleados.index', [
            'empleados' => $empleados,
            'sucursales' => $sucursales,
        ]);
    }

    public function create(): View
    {
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.empleados.create', [
            'empleado' => new \App\Models\Empleado(),
        ]);
    }

    public function store(EmpleadoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);

        Empleado::create($datos);

        return $this->redirigirConExito('empleados', 'registrado');
    }

    public function show(Empleado $empleado): View
    {
        $empleado->load(['sucursal', 'user.rol', 'mecanico.especialidad']);

        return view('admin.empleados.show', [
            'empleado' => $empleado,
        ]);
    }

    public function edit(Empleado $empleado): View
    {
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.empleados.edit', [
            'empleado' => $empleado,
            'sucursales' => $sucursales,
        ]);
    }

    public function update(EmpleadoRequest $request, Empleado $empleado): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $empleado->update($datos);

        return $this->redirigirConExito('empleados', 'actualizado');
    }

    public function destroy(Empleado $empleado): RedirectResponse
    {
        if ($empleado->user()->exists()) {
            return back()->with('error', 'No se puede eliminar el empleado porque tiene una cuenta de acceso asociada.');
        }

        if ($empleado->mecanico()->exists()) {
            return back()->with('error', 'No se puede eliminar el empleado porque está registrado como mecánico.');
        }

        $empleado->delete();

        return $this->redirigirConExito('empleados', 'eliminado');
    }

    public function toggle(Request $request, Empleado $empleado): RedirectResponse
    {
        return $this->cambiarEstado($request, $empleado, 'empleados');
    }
}
