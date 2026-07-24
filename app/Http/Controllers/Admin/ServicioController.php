<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ServicioRequest;
use App\Models\Servicio;
use App\Models\TipoServicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServicioController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Servicio::query()->with('tipoServicio');

        $this->aplicarFiltros($request, $query, ['estado', 'tipo_servicio_id']);
        $this->aplicarBusqueda($query, $request, ['nombre', 'descripcion']);

        $servicios = $query->orderBy('nombre')->paginate(15)->withQueryString();

        $tipos = TipoServicio::orderBy('nombre')->get();

        return view('admin.servicios.index', [
            'servicios' => $servicios,
            'tipos' => $tipos,
        ]);
    }

    public function create(): View
    {
        $tipos = TipoServicio::orderBy('nombre')->get();

        return view('admin.servicios.create', [
            'tipos' => $tipos,
            'servicio' => new \App\Models\Servicio(),
        ]);
    }

    public function store(ServicioRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);

        Servicio::create($datos);

        return $this->redirigirConExito('servicios', 'registrado');
    }

    public function show(Servicio $servicio): View
    {
        $servicio->load('tipoServicio');

        return view('admin.servicios.show', [
            'servicio' => $servicio,
        ]);
    }

    public function edit(Servicio $servicio): View
    {
        $tipos = TipoServicio::orderBy('nombre')->get();

        return view('admin.servicios.edit', [
            'servicio' => $servicio,
            'tipos' => $tipos,
        ]);
    }

    public function update(ServicioRequest $request, Servicio $servicio): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $servicio->update($datos);

        return $this->redirigirConExito('servicios', 'actualizado');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        $servicio->delete();

        return $this->redirigirConExito('servicios', 'eliminado');
    }

    public function toggle(Request $request, Servicio $servicio): RedirectResponse
    {
        return $this->cambiarEstado($request, $servicio, 'servicios');
    }
}
