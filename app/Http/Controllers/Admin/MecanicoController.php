<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MecanicoRequest;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\Mecanico;
use App\Models\OrdenTrabajo;
use App\Models\Repuesto;
use App\Models\DetalleOrdenTrabajo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MecanicoController extends AdminController
{
    //administracion code

    public function index(Request $request): View
    {
        $query = Mecanico::query()
            ->with(['empleado.sucursal', 'empleado.rol', 'especialidad']);

        if ($sucursalId = $this->usuarioSucursalId()) {
            $query->whereHas('empleado', fn ($q) => $q->where('sucursal_id', $sucursalId));
        }

        $this->aplicarFiltros($request, $query, ['disponibilidad']);
        $this->aplicarBusqueda($query, $request, [
            'empleado.nombre_completo',
            'especialidad.nombre',
        ]);

        $mecanicos = $query->orderBy('disponibilidad')->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.mecanicos.index', [
            'mecanicos' => $mecanicos,
        ]);
    }

    public function create(): View
    {
        $empleados = Empleado::query()
            ->whereDoesntHave('mecanico')
            ->whereHas('rol', fn ($q) => $q->whereRaw('LOWER(nombre) = ?', ['mecánico']))
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->orderBy('nombre_completo')
            ->get();
        $especialidades = Especialidad::orderBy('nombre')->get();

        return view('admin.mecanicos.create', [
            'empleados' => $empleados,
            'especialidades' => $especialidades,
            'mecanico' => new \App\Models\Mecanico(),
        ]);
    }

    public function store(MecanicoRequest $request): RedirectResponse
    {
        Mecanico::create($request->validated());

        return $this->redirigirConExito('mecánicos', 'registrado');
    }

    public function show(Mecanico $mecanico): View
    {
        $mecanico->load(['empleado.sucursal', 'empleado.rol', 'especialidad', 'asignaciones.ordenTrabajo']);

        return view('admin.mecanicos.show', [
            'mecanico' => $mecanico,
        ]);
    }

    public function edit(Mecanico $mecanico): View
    {
        $empleados = Empleado::query()
            ->where(function ($q) use ($mecanico) {
                $q->whereDoesntHave('mecanico')
                  ->orWhere('id', $mecanico->empleado_id);
            })
            ->whereHas('rol', fn ($q) => $q->whereRaw('LOWER(nombre) = ?', ['mecánico']))
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->orderBy('nombre_completo')
            ->get();
        $especialidades = Especialidad::orderBy('nombre')->get();

        return view('admin.mecanicos.edit', [
            'mecanico' => $mecanico,
            'empleados' => $empleados,
            'especialidades' => $especialidades,
        ]);
    }

    public function update(MecanicoRequest $request, Mecanico $mecanico): RedirectResponse
    {
        $mecanico->update($request->validated());

        return $this->redirigirConExito('mecánicos', 'actualizado');
    }

    public function destroy(Mecanico $mecanico): RedirectResponse
    {
        if ($mecanico->asignaciones()->exists()) {
            return back()->with('error', 'No se puede eliminar el mecánico porque tiene asignaciones registradas.');
        }

        $mecanico->delete();

        return $this->redirigirConExito('mecánicos', 'eliminado');
    }

    public function toggle(Request $request, Mecanico $mecanico): RedirectResponse
    {
        $estados = ['disponible', 'ocupado', 'ausente'];
        $actual = array_search($mecanico->disponibilidad, $estados);
        $nuevo = $estados[($actual + 1) % count($estados)];
        $mecanico->disponibilidad = $nuevo;
        $mecanico->save();

        return back()->with('success', "El mecánico fue marcado como {$nuevo}.");
    }

    // operacion tecnica -diana

    // 1. ordenes asignadas
    public function misOrdenes(): View
    {
        $ordenes = OrdenTrabajo::with(['vehiculo', 'cliente'])->latest()->paginate(10);

        return view('mecanico.index', compact('ordenes'));
    }

    // detalle de la orden para trabajar
    public function atenderOrden($id): View
    {
        $orden = OrdenTrabajo::with(['vehiculo', 'detalles.repuesto'])->findOrFail($id);
        $repuestos = Repuesto::where('stock', '>', 0)->get();

        return view('mecanico.show', compact('orden', 'repuestos'));
    }

    // 2, 4 y 5. registrad diagnotico ,observavionesy estado 
    public function guardarDiagnostico(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'diagnostico'   => 'required|string',
            'observaciones' => 'nullable|string',
            'estado'        => 'required|string',
        ]);

        $orden = OrdenTrabajo::findOrFail($id);
        $orden->update([
            'diagnostico'   => $request->diagnostico,
            'observaciones' => $request->observaciones,
            'estado'        => $request->estado,
        ]);

        return back()->with('success', 'Diagnóstico y estado de la orden actualizados correctamente.');
    }

    // 3 y 6. rgistrar repuestos utilizados con VALIDACIÓN DE STOCK
    public function registrarRepuesto(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'repuesto_id' => 'required|exists:repuestos,id',
            'cantidad'    => 'required|integer|min:1',
        ]);

        $orden = OrdenTrabajo::findOrFail($id);
        $repuesto = Repuesto::findOrFail($request->repuesto_id);

        // Validar Stock
        if ($repuesto->stock < $request->cantidad) {
            return back()->with('error', "Stock insuficiente. Solo quedan {$repuesto->stock} unidades de {$repuesto->nombre}.");
        }

        // Descontar inventario
        $repuesto->decrement('stock', $request->cantidad);

        // Asociar a la orden
        DetalleOrdenTrabajo::create([
            'orden_trabajo_id' => $orden->id,
            'repuesto_id'      => $repuesto->id,
            'cantidad'         => $request->cantidad,
            'precio_unitario'  => $repuesto->precio ?? 0,
        ]);

        return back()->with('success', 'Repuesto agregado a la orden y descontado del stock.');
    }
}