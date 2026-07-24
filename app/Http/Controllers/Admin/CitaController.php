<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CitaRequest;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\OrdenTrabajo;
use App\Models\Sucursal;
use App\Models\Vehiculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CitaController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Cita::query()->with(['cliente', 'vehiculo', 'sucursal']);

        $this->scopeSucursal($query, 'sucursal_id');
        $this->aplicarFiltros($request, $query, ['estado', 'sucursal_id', 'fecha']);
        $this->aplicarBusqueda($query, $request, [
            'descripcion_problema',
            'cliente.nombre_completo',
            'vehiculo.placa',
        ]);

        $citas = $query->orderByDesc('fecha')->orderBy('hora')->paginate(15)->withQueryString();

        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.citas.index', [
            'citas' => $citas,
            'sucursales' => $sucursales,
        ]);
    }

    public function create(): View
    {
        $clientes = Cliente::orderBy('nombre_completo')->get();
        $vehiculos = Vehiculo::with('cliente')->orderBy('placa')->get();
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.citas.create', [
            'clientes' => $clientes,
            'vehiculos' => $vehiculos,
            'sucursales' => $sucursales,
            'cita' => new \App\Models\Cita(),
        ]);
    }

    public function store(CitaRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['usuario_id'] = auth()->id();
        $datos['estado'] = $datos['estado'] ?? 'pendiente';
        $datos['deja_vehiculo'] = (bool) ($datos['deja_vehiculo'] ?? false);
        $datos['costo_consulta'] = $datos['costo_consulta'] ?? 0;

        Cita::create($datos);

        return $this->redirigirConExito('citas', 'registrada');
    }

    public function show(Cita $cita): View
    {
        $cita->load(['cliente', 'vehiculo', 'sucursal', 'usuario', 'ordenTrabajo']);

        return view('admin.citas.show', [
            'cita' => $cita,
        ]);
    }

    public function edit(Cita $cita): View
    {
        $clientes = Cliente::orderBy('nombre_completo')->get();
        $vehiculos = Vehiculo::with('cliente')->orderBy('placa')->get();
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.citas.edit', [
            'cita' => $cita,
            'clientes' => $clientes,
            'vehiculos' => $vehiculos,
            'sucursales' => $sucursales,
        ]);
    }

    public function update(CitaRequest $request, Cita $cita): RedirectResponse
    {
        $datos = $request->validated();
        $datos['deja_vehiculo'] = (bool) ($datos['deja_vehiculo'] ?? false);

        $cita->update($datos);

        return $this->redirigirConExito('citas', 'actualizada');
    }

    public function destroy(Cita $cita): RedirectResponse
    {
        if ($cita->ordenTrabajo) {
            return back()->with('error', 'No se puede eliminar la cita porque ya tiene una orden de trabajo asociada.');
        }

        $cita->delete();

        return $this->redirigirConExito('citas', 'eliminada');
    }

    public function toggle(Request $request, Cita $cita): RedirectResponse
    {
        $estados = ['pendiente', 'confirmada', 'atendida', 'cancelada'];
        $actual = array_search($cita->estado, $estados);
        $nuevo = $estados[($actual + 1) % count($estados)];
        $cita->estado = $nuevo;
        $cita->save();

        return back()->with('success', "La cita fue marcada como {$nuevo}.");
    }

    public function cancelar(Request $request, Cita $cita): RedirectResponse
    {
        $cita->estado = 'cancelada';
        $cita->save();

        return back()->with('success', 'La cita fue cancelada correctamente.');
    }

    public function convertirOrden(Request $request, Cita $cita): RedirectResponse
    {
        if ($cita->ordenTrabajo) {
            return back()->with('error', 'La cita ya tiene una orden de trabajo asociada.');
        }

        DB::transaction(function () use ($cita) {
            $orden = OrdenTrabajo::create([
                'numero_orden' => 'OT-' . str_pad((string) (OrdenTrabajo::max('id') + 1), 6, '0', STR_PAD_LEFT),
                'cliente_id' => $cita->cliente_id,
                'vehiculo_id' => $cita->vehiculo_id,
                'sucursal_id' => $cita->sucursal_id,
                'usuario_recepcion_id' => auth()->id(),
                'cita_id' => $cita->id,
                'fecha_emision' => now(),
                'descripcion_problema' => $cita->descripcion_problema,
                'estado' => 'recibida',
            ]);

            $cita->estado = 'atendida';
            $cita->save();

            return $orden;
        });

        return back()->with('success', 'La cita fue convertida en orden de trabajo correctamente.');
    }
}
