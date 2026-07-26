<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comprobante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComprobanteController extends AdminController
{
    public function __construct()
    {
        $this->middleware('permiso:roles.editar');
    }
    public function index(Request $request): View
    {
        $query = Comprobante::query()->with(['pago.ordenTrabajo', 'cliente']);

        $this->aplicarFiltros($request, $query, ['estado']);
        $this->aplicarBusqueda($query, $request, [
            'numero',
            'nit_ci',
            'razon_social',
            'cliente.nombre_completo',
        ]);

        $comprobantes = $query->orderByDesc('fecha_emision')->paginate(15)->withQueryString();

        return view('admin.comprobantes.index', [
            'comprobantes' => $comprobantes,
        ]);
    }

    public function show(Comprobante $comprobante): View
    {
        $comprobante->load(['pago.ordenTrabajo', 'cliente']);

        return view('admin.comprobantes.show', [
            'comprobante' => $comprobante,
        ]);
    }

    public function edit(Comprobante $comprobante): View
    {
        return view('admin.comprobantes.edit', [
            'comprobante' => $comprobante,
        ]);
    }

    public function update(Request $request, Comprobante $comprobante): RedirectResponse
    {
        $request->validate([
            'nit_ci' => ['nullable', 'string', 'max:20'],
            'razon_social' => ['nullable', 'string', 'max:150'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ], [
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
        ], [
            'nit_ci' => 'NIT/CI',
            'razon_social' => 'razón social',
        ]);

        $comprobante->update($request->only(['nit_ci', 'razon_social', 'observaciones']));

        return $this->redirigirConExito('comprobantes', 'actualizado');
    }

    public function destroy(Comprobante $comprobante): RedirectResponse
    {
        $comprobante->delete();

        return $this->redirigirConExito('comprobantes', 'eliminado');
    }

    public function anular(Request $request, Comprobante $comprobante): RedirectResponse
    {
        $comprobante->estado = 'anulado';
        $comprobante->save();

        return back()->with('success', 'El comprobante fue anulado correctamente.');
    }
}
