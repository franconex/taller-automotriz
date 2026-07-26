<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\VehiculoRequest;
use App\Models\Cliente;
use App\Models\ModeloVehiculo;
use App\Models\Vehiculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehiculoController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Vehiculo::query()->with(['cliente', 'modelo.marca']);

        $this->aplicarFiltros($request, $query, ['estado', 'cliente_id']);
        $this->aplicarBusqueda($query, $request, [
            'placa',
            'numero_chasis',
            'cliente.nombre_completo',
        ]);

        $vehiculos = $query->orderBy('placa')->paginate(15)->withQueryString();

        return view('admin.vehiculos.index', [
            'vehiculos' => $vehiculos,
        ]);
    }

    public function create(): View
    {
        $clientes = Cliente::orderBy('nombre_completo')->get();
        $modelos = ModeloVehiculo::with('marcaVehiculo')
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('admin.vehiculos.create', [
            'clientes' => $clientes,
            'modelos' => $modelos,
            'vehiculo' => new \App\Models\Vehiculo(),
        ]);
    }

    public function store(VehiculoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);
        $datos['kilometraje_actual'] = $datos['kilometraje_actual'] ?? 0;

        Vehiculo::create($datos);

        return $this->redirigirALista('admin.vehiculos.index', 'Vehículo creado con éxito.');
    }

    public function show(Vehiculo $vehiculo): View
    {
        $vehiculo->load(['cliente', 'modelo.marca', 'citas', 'ordenesTrabajo']);

        return view('admin.vehiculos.show', [
            'vehiculo' => $vehiculo,
        ]);
    }

    public function edit(Vehiculo $vehiculo): View
    {
        $clientes = Cliente::orderBy('nombre_completo')->get();
        $modelos = ModeloVehiculo::with('marcaVehiculo')
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('admin.vehiculos.edit', [
            'vehiculo' => $vehiculo,
            'clientes' => $clientes,
            'modelos' => $modelos,
        ]);
    }

    public function update(VehiculoRequest $request, Vehiculo $vehiculo): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $vehiculo->update($datos);

        return $this->redirigirConExito('vehículos', 'actualizado');
    }

    public function destroy(Vehiculo $vehiculo): RedirectResponse
    {
        if ($vehiculo->ordenesTrabajo()->exists()) {
            return back()->with('error', 'No se puede eliminar el vehículo porque tiene órdenes de trabajo.');
        }

        $vehiculo->delete();

        return $this->redirigirConExito('vehículos', 'eliminado');
    }

    public function toggle(Request $request, Vehiculo $vehiculo): RedirectResponse
    {
        return $this->cambiarEstado($request, $vehiculo, 'vehículos');
    }
}
