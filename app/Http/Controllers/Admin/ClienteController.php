<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Cliente::query();

        $this->aplicarFiltros($request, $query, ['estado']);
        $this->aplicarBusqueda($query, $request, [
            'nombre_completo',
            'ci',
            'telefono',
            'email',
        ]);

        $clientes = $query->orderBy('nombre_completo')->paginate(15)->withQueryString();

        return view('admin.clientes.index', [
            'clientes' => $clientes,
        ]);
    }

    public function create(): View
    {
        return view('admin.clientes.create', [
            'cliente' => new \App\Models\Cliente(),
        ]);
    }

    public function store(ClienteRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);
        $datos['fecha_registro'] = now();

        Cliente::create($datos);

        return $this->redirigirConExito('clientes', 'registrado');
    }

    public function show(Cliente $cliente): View
    {
        $cliente->load(['vehiculos.modelo.marcaVehiculo', 'citas', 'ordenesTrabajo']);

        return view('admin.clientes.show', [
            'cliente' => $cliente,
        ]);
    }

    public function edit(Cliente $cliente): View
    {
        return view('admin.clientes.edit', [
            'cliente' => $cliente,
        ]);
    }

    public function update(ClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $cliente->update($datos);

        return $this->redirigirConExito('clientes', 'actualizado');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        if ($cliente->vehiculos()->exists()) {
            return back()->with('error', 'No se puede eliminar el cliente porque tiene vehículos registrados.');
        }

        if ($cliente->ordenesTrabajo()->exists()) {
            return back()->with('error', 'No se puede eliminar el cliente porque tiene órdenes de trabajo.');
        }

        $cliente->delete();

        return $this->redirigirConExito('clientes', 'eliminado');
    }

    public function toggle(Request $request, Cliente $cliente): RedirectResponse
    {
        return $this->cambiarEstado($request, $cliente, 'clientes');
    }
}
