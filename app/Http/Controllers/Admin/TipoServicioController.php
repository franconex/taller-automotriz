<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\TipoServicioRequest;
use App\Models\TipoServicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipoServicioController extends AdminController
{
    public function index(Request $request): View
    {
        $query = TipoServicio::query()->withCount('servicios');

        $this->aplicarFiltros($request, $query, ['estado']);
        $this->aplicarBusqueda($query, $request, ['nombre', 'descripcion']);

        $tipos = $query->orderBy('nombre')->paginate(15)->withQueryString();

        return view('admin.tipos-servicio.index', [
            'tipos' => $tipos,
        ]);
    }

    public function create(): View
    {
        return view('admin.tipos-servicio.create', [
            'tipo' => new \App\Models\TipoServicio(),
        ]);
    }

    public function store(TipoServicioRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);

        TipoServicio::create($datos);

        return $this->redirigirALista('admin.tipos-servicio.index', 'Tipo de servicio creado con éxito.');
    }

    public function show(TipoServicio $tipo_servicio): View
    {
        $tipo_servicio->load('servicios');

        return view('admin.tipos-servicio.show', [
            'tipo' => $tipo_servicio,
        ]);
    }

    public function edit(TipoServicio $tipo_servicio): View
    {
        return view('admin.tipos-servicio.edit', [
            'tipo' => $tipo_servicio,
        ]);
    }

    public function update(TipoServicioRequest $request, TipoServicio $tipo_servicio): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $tipo_servicio->update($datos);

        return $this->redirigirConExito('tipos de servicio', 'actualizado');
    }

    public function destroy(TipoServicio $tipo_servicio): RedirectResponse
    {
        if ($tipo_servicio->servicios()->exists()) {
            return back()->with('error', 'No se puede eliminar el tipo de servicio porque tiene servicios asociados.');
        }

        $tipo_servicio->delete();

        return $this->redirigirConExito('tipos de servicio', 'eliminado');
    }

    public function toggle(Request $request, TipoServicio $tipo_servicio): RedirectResponse
    {
        return $this->cambiarEstado($request, $tipo_servicio, 'tipos de servicio');
    }
}
