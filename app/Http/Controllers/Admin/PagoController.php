<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PagoRequest;
use App\Models\MetodoPago;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PagoController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:pagos.ver', only: ['index', 'show']),
            new Middleware('permiso:pagos.registrar', only: ['create', 'store']),
            new Middleware('permiso:pagos.anular', only: ['anular']),
        ];
    }
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

        return $this->redirigirALista('admin.pagos.index', 'Pago registrado con éxito.');
    }

    public function modalData(Request $request, OrdenTrabajo $orden): JsonResponse
    {
        $orden->load([
            'cliente',
            'vehiculo',
            'serviciosMecanico',
            'repuestosMecanico.repuesto',
        ]);

        $totalServicios = $orden->serviciosMecanico->sum('precio_base');
        $totalRepuestos = $orden->repuestosMecanico->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot);
        $totalGeneral = $totalServicios + $totalRepuestos;

        $metodosPago = MetodoPago::where('estado', true)->orderBy('nombre')->get();

        return response()->json([
            'ok' => true,
            'orden' => ['id' => $orden->id, 'numero_orden' => $orden->numero_orden],
            'cliente' => $orden->cliente,
            'vehiculo' => $orden->vehiculo,
            'servicios' => $orden->serviciosMecanico,
            'repuestos' => $orden->repuestosMecanico,
            'total_general' => $totalGeneral,
            'metodos_pago' => $metodosPago,
            'metodo_default' => $metodosPago->first()?->id,
        ]);
    }

    public function cobrarDesdeModal(Request $request): JsonResponse
    {
        $data = $request->validate([
            'orden_id' => ['required', 'exists:ordenes_trabajo,id'],
            'metodo_pago_id' => ['required', 'exists:metodos_pago,id'],
        ]);

        $orden = OrdenTrabajo::with(['serviciosMecanico', 'repuestosMecanico'])->findOrFail($data['orden_id']);

        if (! in_array($orden->estado, ['finalizada_mecanico', 'lista_entrega', 'finalizada', 'recibida', 'diagnostico', 'en_proceso'])) {
            return response()->json(['ok' => false, 'mensaje' => 'La orden no está en un estado válido para cobrar.'], 422);
        }

        $yaPagado = $orden->pagos()->where('estado', 'confirmado')->sum('monto');
        $totalServicios = $orden->serviciosMecanico->sum('precio_base');
        $totalRepuestos = $orden->repuestosMecanico->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot);
        $totalGeneral = $totalServicios + $totalRepuestos;
        $pendiente = max(0, $totalGeneral - $yaPagado);

        if ($pendiente <= 0) {
            return response()->json(['ok' => false, 'mensaje' => 'La orden ya está totalmente pagada.'], 422);
        }

        try {
            DB::transaction(function () use ($orden, $data, $pendiente) {
                Pago::create([
                    'orden_trabajo_id' => $orden->id,
                    'metodo_pago_id' => $data['metodo_pago_id'],
                    'usuario_id' => auth()->id(),
                    'fecha_pago' => now(),
                    'monto' => $pendiente,
                    'estado' => 'confirmado',
                ]);

                if ($orden->estado === 'finalizada_mecanico' || $orden->estado === 'lista_entrega' || $orden->estado === 'finalizada') {
                    $orden->update(['estado' => 'entregada', 'fecha_entrega' => now()]);
                }
            });

            return response()->json([
                'ok' => true,
                'mensaje' => 'Pago de Bs ' . number_format($pendiente, 2) . ' registrado. Orden entregada.',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'mensaje' => 'Error al procesar pago: ' . $e->getMessage()], 500);
        }
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
