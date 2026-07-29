<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ClienteRequest;
use App\Models\Cliente;
use App\Models\ModeloVehiculo;
use App\Models\TipoVehiculo;
use App\Models\TipoUso;
use App\Models\Vehiculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClienteController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:clientes.ver', only: ['index', 'show']),
            new Middleware('permiso:clientes.crear', only: ['create', 'store']),
            new Middleware('permiso:clientes.editar', only: ['edit', 'update', 'destroy', 'toggle']),
        ];
    }
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

        $cliente = DB::transaction(function () use ($request, $datos) {
            $cliente = Cliente::create($datos);

            $vehiculos = $request->input('vehiculos', []);
            foreach ($vehiculos as $v) {
                Vehiculo::create([
                    'cliente_id'         => $cliente->id,
                    'marca'              => $v['marca'],
                    'modelo'             => $v['modelo'],
                    'placa'              => $v['placa'],
                    'anio'               => $v['anio'] ?? null,
                    'color'              => $v['color'] ?? null,
                    'kilometraje_actual' => 0,
                    'estado'             => true,
                ]);
            }

            return $cliente;
        });

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




