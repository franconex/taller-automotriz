<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MetodoPagoRequest;
use App\Models\MetodoPago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetodoPagoController extends AdminController
{
    public function index(Request $request): View
    {
        $query = MetodoPago::query()->withCount('pagos');

        $this->aplicarFiltros($request, $query, ['estado']);
        $this->aplicarBusqueda($query, $request, ['nombre', 'descripcion']);

        $metodos = $query->orderBy('nombre')->paginate(15)->withQueryString();

        return view('admin.metodos-pago.index', [
            'metodos' => $metodos,
        ]);
    }

    public function create(): View
    {
        return view('admin.metodos-pago.create', [
            'metodo' => new \App\Models\MetodoPago(),
        ]);
    }

    public function store(MetodoPagoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);

        MetodoPago::create($datos);

        return $this->redirigirConExito('métodos de pago', 'registrado');
    }

    public function show(MetodoPago $metodoPago): View
    {
        $metodoPago->loadCount('pagos');

        return view('admin.metodos-pago.show', [
            'metodo' => $metodoPago,
        ]);
    }

    public function edit(MetodoPago $metodoPago): View
    {
        return view('admin.metodos-pago.edit', [
            'metodo' => $metodoPago,
        ]);
    }

    public function update(MetodoPagoRequest $request, MetodoPago $metodoPago): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $metodoPago->update($datos);

        return $this->redirigirConExito('métodos de pago', 'actualizado');
    }

    public function destroy(MetodoPago $metodoPago): RedirectResponse
    {
        if ($metodoPago->pagos()->exists()) {
            return back()->with('error', 'No se puede eliminar el método de pago porque tiene pagos registrados.');
        }

        $metodoPago->delete();

        return $this->redirigirConExito('métodos de pago', 'eliminado');
    }

    public function toggle(Request $request, MetodoPago $metodoPago): RedirectResponse
    {
        return $this->cambiarEstado($request, $metodoPago, 'métodos de pago');
    }
}
