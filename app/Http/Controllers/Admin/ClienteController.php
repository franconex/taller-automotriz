<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ClienteRequest;
use App\Models\Cliente;
use App\Models\ModeloVehiculo;
use App\Models\Vehiculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $modelos = ModeloVehiculo::with('marcaVehiculo')
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('admin.clientes.create', [
            'cliente' => new \App\Models\Cliente(),
            'modelos' => $modelos,
        ]);
    }

    public function store(ClienteRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);
        $datos['fecha_registro'] = now();

        $cliente = DB::transaction(function () use ($request, $datos) {
            $cliente = Cliente::create($datos);

            if ($request->filled('vehiculo_placa')) {
                $errores = [];
                if (! $request->filled('vehiculo_modelo_id')) {
                    $errores['vehiculo_modelo_id'] = 'El modelo del vehículo es obligatorio.';
                }
                if (Vehiculo::where('placa', $request->input('vehiculo_placa'))->exists()) {
                    $errores['vehiculo_placa'] = 'La placa ya está registrada.';
                }
                if ($errores) {
                    return back()->withInput()->withErrors($errores);
                }

                $vehiculo = Vehiculo::create([
                    'cliente_id'        => $cliente->id,
                    'modelo_vehiculo_id' => $request->input('vehiculo_modelo_id'),
                    'placa'             => $request->input('vehiculo_placa'),
                    'anio'              => $request->input('vehiculo_anio'),
                    'color'             => $request->input('vehiculo_color'),
                    'foto'              => $request->input('vehiculo_foto_base64'),
                    'kilometraje_actual' => 0,
                    'estado'            => true,
                ]);
            }

            return $cliente;
        });

        if ($cliente instanceof \Illuminate\Http\RedirectResponse) {
            return $cliente;
        }

        return $this->redirigirALista('admin.clientes.index', 'Cliente creado con éxito.');
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
        $modelos = ModeloVehiculo::with('marcaVehiculo')
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('admin.clientes.edit', [
            'cliente' => $cliente,
            'modelos' => $modelos,
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
