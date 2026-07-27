<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ProveedorController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:proveedores.ver', only: ['index', 'show']),
            new Middleware('permiso:proveedores.crear', only: ['create', 'store']),
            new Middleware('permiso:proveedores.editar', only: ['edit', 'update', 'destroy', 'toggle']),
        ];
    }
    public function index(Request $request): View
    {
        $query = Proveedor::query()->withCount('repuestos');

        $this->aplicarFiltros($request, $query, ['estado']);
        $this->aplicarBusqueda($query, $request, [
            'nombre_empresa',
            'contacto',
            'telefono',
            'email',
            'nit',
        ]);

        $proveedores = $query->orderBy('nombre_empresa')->paginate(15)->withQueryString();

        return view('admin.proveedores.index', [
            'proveedores' => $proveedores,
        ]);
    }

    public function create(): View
    {
        return view('admin.proveedores.create', [
            'proveedor' => new \App\Models\Proveedor(),
        ]);
    }

    public function store(ProveedorRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);

        Proveedor::create($datos);

        return $this->redirigirALista('admin.proveedores.index', 'Proveedor creado con éxito.');
    }

    public function show(Proveedor $proveedore): View
    {
        $proveedore->load('repuestos');

        return view('admin.proveedores.show', [
            'proveedor' => $proveedore,
        ]);
    }

    public function edit(Proveedor $proveedore): View
    {
        return view('admin.proveedores.edit', [
            'proveedor' => $proveedore,
        ]);
    }

    public function update(ProveedorRequest $request, Proveedor $proveedore): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $proveedore->update($datos);

        return $this->redirigirConExito('proveedores', 'actualizado');
    }

    public function destroy(Proveedor $proveedore): RedirectResponse
    {
        if ($proveedore->repuestos()->exists()) {
            return back()->with('error', 'No se puede eliminar el proveedor porque tiene repuestos asociados.');
        }

        $proveedore->delete();

        return $this->redirigirConExito('proveedores', 'eliminado');
    }

    public function toggle(Request $request, Proveedor $proveedore): RedirectResponse
    {
        return $this->cambiarEstado($request, $proveedore, 'proveedores');
    }
}
