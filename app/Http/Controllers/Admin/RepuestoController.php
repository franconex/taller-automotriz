<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RepuestoRequest;
use App\Models\Proveedor;
use App\Models\Repuesto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class RepuestoController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permiso:roles.editar')];
    }
    public function index(Request $request): View
    {
        $query = Repuesto::query()->with('proveedor');

        $this->aplicarFiltros($request, $query, ['estado', 'proveedor_id']);
        $this->aplicarBusqueda($query, $request, [
            'codigo',
            'nombre',
            'descripcion',
        ]);

        $repuestos = $query->orderBy('nombre')->paginate(15)->withQueryString();

        $proveedores = Proveedor::orderBy('nombre_empresa')->get();

        return view('admin.repuestos.index', [
            'repuestos' => $repuestos,
            'proveedores' => $proveedores,
        ]);
    }

    public function create(): View
    {
        $proveedores = Proveedor::orderBy('nombre_empresa')->get();

        return view('admin.repuestos.create', [
            'proveedores' => $proveedores,
            'repuesto' => new \App\Models\Repuesto(),
        ]);
    }

    public function store(RepuestoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);
        $datos['stock_minimo'] = $datos['stock_minimo'] ?? 0;

        Repuesto::create($datos);

        return $this->redirigirALista('admin.repuestos.index', 'Repuesto creado con éxito.');
    }

    public function show(Repuesto $repuesto): View
    {
        $repuesto->load('proveedor', 'inventarios.sucursal');

        return view('admin.repuestos.show', [
            'repuesto' => $repuesto,
        ]);
    }

    public function edit(Repuesto $repuesto): View
    {
        $proveedores = Proveedor::orderBy('nombre_empresa')->get();

        return view('admin.repuestos.edit', [
            'repuesto' => $repuesto,
            'proveedores' => $proveedores,
        ]);
    }

    public function update(RepuestoRequest $request, Repuesto $repuesto): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $repuesto->update($datos);

        return $this->redirigirConExito('repuestos', 'actualizado');
    }

    public function destroy(Repuesto $repuesto): RedirectResponse
    {
        if ($repuesto->inventarios()->where('cantidad_actual', '>', 0)->exists()) {
            return back()->with('error', 'No se puede eliminar el repuesto porque tiene stock registrado.');
        }

        $repuesto->delete();

        return $this->redirigirConExito('repuestos', 'eliminado');
    }

    public function toggle(Request $request, Repuesto $repuesto): RedirectResponse
    {
        return $this->cambiarEstado($request, $repuesto, 'repuestos');
    }
}
