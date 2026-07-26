<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SucursalRequest;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SucursalController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Sucursal::query();

        $this->aplicarFiltros($request, $query, ['estado']);
        $this->aplicarBusqueda($query, $request, [
            'nombre',
            'direccion',
            'telefono',
        ]);

        $sucursales = $query->orderBy('nombre')->paginate(15)->withQueryString();

        return view('admin.sucursales.index', [
            'sucursales' => $sucursales,
        ]);
    }

    public function create(): View
    {
        return view('admin.sucursales.create', [
            'sucursal' => new \App\Models\Sucursal(),
        ]);
    }

    public function store(SucursalRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);

        Sucursal::create($datos);

        return $this->redirigirALista('admin.sucursales.index', 'Sucursal creada con éxito.');
    }

    public function show(Sucursal $sucursale): View
    {
        $sucursale->load(['empleados', 'inventarios.repuesto']);

        return view('admin.sucursales.show', [
            'sucursal' => $sucursale,
        ]);
    }

    public function edit(Sucursal $sucursale): View
    {
        return view('admin.sucursales.edit', [
            'sucursal' => $sucursale,
        ]);
    }

    public function update(SucursalRequest $request, Sucursal $sucursale): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $sucursale->update($datos);

        return $this->redirigirConExito('sucursales', 'actualizada');
    }

    public function destroy(Sucursal $sucursale): RedirectResponse
    {
        if ($sucursale->empleados()->exists()) {
            return back()->with('error', 'No se puede eliminar la sucursal porque tiene empleados asociados.');
        }

        $sucursale->delete();

        return $this->redirigirConExito('sucursales', 'eliminada');
    }

    public function toggle(Request $request, Sucursal $sucursale): RedirectResponse
    {
        return $this->cambiarEstado($request, $sucursale, 'sucursales');
    }
}
