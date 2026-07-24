<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PagoRequest;
use App\Models\MetodoPago;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagoController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Pago::query()
            ->with(['ordenTrabajo.cliente', 'metodoPago', 'usuario']);

        if ($sucursalId = $this->usuarioSucursalId()) {
            $query->whereHas('ordenTrabajo', fn ($q) => $q->where('sucursal_id', $sucursalId));
        }

        $this->aplicarFiltros($request, $query, ['estado', 'metodo_pago_id']);
        $this->aplicarBusqueda($query, $request, [
            'numero_comprobante',
            'referencia',
            'ordenTrabajo.numero_orden',
        ]);

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_pago', $request->input('fecha'));
        }

        $pagos = $query->orderByDesc('fecha_pago')->paginate(15)->withQueryString();

        $metodos = MetodoPago::orderBy('nombre')->get();

        return view('admin.pagos.index', [
            'pagos' => $pagos,
            'metodos' => $metodos,
        ]);
    }

    public function create(Request $request): View
    {
        $ordenId = $request->input('orden_id');
        $ordenes = OrdenTrabajo::with(['cliente', 'vehiculo'])
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->whereIn('estado', ['recibida', 'diagnostico', 'en_proceso', 'finalizada'])
            ->orderByDesc('fecha_emision')
            ->limit(50)
            ->get();
        $metodos = MetodoPago::where('estado', true)->orderBy('nombre')->get();

        return view('admin.pagos.create', [
            'ordenes' => $ordenes,
            'metodos' => $metodos,
            'ordenId' => $ordenId,
            'pago' => new \App\Models\Pago(),
        ]);
    }

    public function store(PagoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = 'confirmado';
        $datos['usuario_id'] = auth()->id();

        Pago::create($datos);

        return $this->redirigirConExito('pagos', 'registrado');
    }

    public function show(Pago $pago): View
    {
        $pago->load(['ordenTrabajo.cliente', 'metodoPago', 'usuario', 'comprobante']);

        return view('admin.pagos.show', [
            'pago' => $pago,
        ]);
    }

    public function edit(Pago $pago): View
    {
        $ordenes = OrdenTrabajo::with(['cliente'])
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->orderByDesc('fecha_emision')
            ->limit(50)
            ->get();
        $metodos = MetodoPago::orderBy('nombre')->get();

        return view('admin.pagos.edit', [
            'pago' => $pago,
            'ordenes' => $ordenes,
            'metodos' => $metodos,
        ]);
    }

    public function update(PagoRequest $request, Pago $pago): RedirectResponse
    {
        $pago->update($request->validated());

        return $this->redirigirConExito('pagos', 'actualizado');
    }

    public function destroy(Pago $pago): RedirectResponse
    {
        if ($pago->comprobante) {
            return back()->with('error', 'No se puede eliminar el pago porque tiene un comprobante asociado.');
        }

        $pago->delete();

        return $this->redirigirConExito('pagos', 'eliminado');
    }

    public function toggle(Request $request, Pago $pago): RedirectResponse
    {
        $nuevo = $pago->estado === 'confirmado' ? 'anulado' : 'confirmado';
        $pago->estado = $nuevo;
        $pago->save();

        return back()->with('success', "El pago fue {$nuevo}.");
    }

    public function anular(Request $request, Pago $pago): RedirectResponse
    {
        $pago->estado = 'anulado';
        $pago->save();

        return back()->with('success', 'El pago fue anulado correctamente.');
    }
}
