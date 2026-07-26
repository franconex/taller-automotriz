<?php

namespace App\Http\Controllers\Admin;

use App\Models\Inventario;
use App\Models\Repuesto;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventarioController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Inventario::query()->with(['repuesto', 'sucursal']);

        $this->scopeSucursal($query, 'sucursal_id');
        $this->aplicarFiltros($request, $query, ['sucursal_id']);
        $this->aplicarBusqueda($query, $request, [
            'repuesto.nombre',
            'repuesto.codigo',
        ]);

        $items = $query->orderBy('repuesto.nombre')->paginate(15)->withQueryString();

        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.inventario.index', [
            'items' => $items,
            'sucursales' => $sucursales,
        ]);
    }

    public function show(Inventario $inventario): View
    {
        $inventario->load(['repuesto.proveedor', 'sucursal', 'movimientos' => fn ($q) => $q->orderByDesc('fecha_movimiento')->limit(20)]);

        return view('admin.inventario.show', [
            'inventario' => $inventario,
        ]);
    }

    public function edit(Inventario $inventario): View
    {
        $inventario->load(['repuesto', 'sucursal']);

        return view('admin.inventario.edit', [
            'inventario' => $inventario,
        ]);
    }

    public function update(Request $request, Inventario $inventario): RedirectResponse
    {
        $request->validate([
            'cantidad_reservada' => ['nullable', 'integer', 'min:0'],
        ], [
            'integer' => 'El campo :attribute debe ser un número entero.',
            'min' => 'El campo :attribute debe ser al menos :min.',
        ], [
            'cantidad_reservada' => 'cantidad reservada',
        ]);

        $inventario->cantidad_reservada = (int) $request->input('cantidad_reservada', 0);
        $inventario->fecha_actualizacion = now();
        $inventario->save();

        return $this->redirigirConExito('inventario', 'actualizado');
    }

    public function destroy(Inventario $inventario): RedirectResponse
    {
        if ($inventario->cantidad_actual > 0) {
            return back()->with('error', 'No se puede eliminar el registro porque tiene stock.');
        }

        $inventario->delete();

        return $this->redirigirConExito('inventario', 'eliminado');
    }

    public function toggle(Request $request, Inventario $inventario): RedirectResponse
    {
        return $this->cambiarEstado($request, $inventario, 'inventario');
    }
}
