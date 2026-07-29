<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PagoRequest;
use App\Models\Comprobante;
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
        $ordenes = OrdenTrabajo::with(['cliente', 'vehiculo.modelo.marcaVehiculo'])
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->where('estado', 'finalizada')
            ->orderByDesc('fecha_emision')
            ->limit(50)
            ->get();
        $metodosPago = MetodoPago::where('estado', true)->orderBy('nombre')->get();

        return view('admin.pagos.index', [
            'pagos' => $pagos,
            'metodos' => $metodos,
            'ordenes' => $ordenes,
            'metodosPago' => $metodosPago,
        ]);
    }

    public function create(Request $request): View
    {
        $ordenId = $request->input('orden_id');
        $ordenes = OrdenTrabajo::with(['cliente', 'vehiculo'])
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->where('estado', 'finalizada')
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

        $pago = Pago::create($datos);
        $this->generarComprobante($pago);

        return $this->redirigirALista('admin.pagos.index', 'Pago registrado con éxito.');
    }

    public function modalData(Request $request, OrdenTrabajo $orden): JsonResponse
    {
        $orden->load([
            'cliente',
            'vehiculo',
            'serviciosMecanico',
            'repuestosMecanico.repuesto',
            'autorizaciones',
        ]);

        $totalServicios = $orden->serviciosMecanico->sum('precio_base');
        $totalRepuestos = $orden->repuestosMecanico->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot);
        $manoDeObra = (float) $orden->autorizaciones->sum('mano_de_obra');
        $totalGeneral = $totalServicios + $totalRepuestos + $manoDeObra;

        $metodosPago = MetodoPago::where('estado', true)->orderBy('nombre')->get();

        return response()->json([
            'ok' => true,
            'orden' => ['id' => $orden->id, 'numero_orden' => $orden->numero_orden],
            'cliente' => $orden->cliente,
            'vehiculo' => $orden->vehiculo,
            'servicios' => $orden->serviciosMecanico,
            'repuestos' => $orden->repuestosMecanico,
            'total_servicios' => $totalServicios,
            'total_repuestos' => $totalRepuestos,
            'mano_obra' => $manoDeObra,
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
            'con_nit' => ['nullable', 'boolean'],
            'nit' => ['nullable', 'string', 'max:30'],
            'razon_social' => ['nullable', 'string', 'max:200'],
        ]);

        $orden = OrdenTrabajo::with(['serviciosMecanico', 'repuestosMecanico.repuesto', 'cliente', 'autorizaciones'])->findOrFail($data['orden_id']);

        if (! in_array($orden->estado, ['finalizada_mecanico', 'lista_entrega', 'finalizada', 'recibida', 'diagnostico', 'en_proceso'])) {
            return response()->json(['ok' => false, 'mensaje' => 'La orden no está en un estado válido para cobrar.'], 422);
        }

        $conNit = $data['con_nit'] ?? false;
        $nit = $conNit ? ($data['nit'] ?? null) : null;
        $razonSocial = $conNit ? ($data['razon_social'] ?? $orden->cliente->nombre_completo) : 'Consumidor Final';

        $yaPagado = $orden->pagos()->where('estado', 'confirmado')->sum('monto');
        $totalServicios = $orden->serviciosMecanico->sum('precio_base');
        $totalRepuestos = $orden->repuestosMecanico->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot);
        $manoDeObra = (float) $orden->autorizaciones->sum('mano_de_obra');
        $totalGeneral = $totalServicios + $totalRepuestos + $manoDeObra;
        $pendiente = max(0, $totalGeneral - $yaPagado);

        if ($pendiente <= 0) {
            return response()->json(['ok' => false, 'mensaje' => 'La orden ya está totalmente pagada.'], 422);
        }

        try {
            $comprobante = null;

            DB::transaction(function () use ($orden, $data, $pendiente, $nit, $razonSocial, &$comprobante) {
                $pago = Pago::create([
                    'orden_trabajo_id' => $orden->id,
                    'metodo_pago_id' => $data['metodo_pago_id'],
                    'usuario_id' => auth()->id(),
                    'fecha_pago' => now(),
                    'monto' => $pendiente,
                    'estado' => 'confirmado',
                ]);

                $ultimoId = Comprobante::withTrashed()->max('id') ?? 0;
                $comprobante = Comprobante::create([
                    'pago_id'      => $pago->id,
                    'cliente_id'   => $orden->cliente_id,
                    'numero'       => 'FACT-' . now()->format('Ymd') . '-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT),
                    'fecha_emision' => now(),
                    'nit_ci'       => $nit,
                    'razon_social' => $razonSocial,
                    'monto_total'  => $pendiente,
                    'estado'       => 'emitido',
                ]);

                $comprobante->load('pago.metodoPago');

                if ($orden->estado === 'finalizada_mecanico' || $orden->estado === 'lista_entrega' || $orden->estado === 'finalizada') {
                    $orden->update(['estado' => 'entregada', 'fecha_entrega' => now()]);
                }

                if ($conNit && $nit && ! $orden->cliente->nit) {
                    $orden->cliente->update(['nit' => $nit, 'razon_social' => $razonSocial]);
                }
            });

            return response()->json([
                'ok' => true,
                'mensaje' => 'Pago de Bs ' . number_format($pendiente, 2) . ' registrado. Factura generada.',
                'comprobante_id' => $comprobante?->id,
                'comprobante_numero' => $comprobante?->numero,
                'factura_url' => $comprobante ? route('admin.factura.show', $comprobante) : null,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'mensaje' => 'Error al procesar pago: ' . $e->getMessage()], 500);
        }
    }

    public function show(Pago $pago): View
    {
        $pago->load([
            'ordenTrabajo.cliente',
            'ordenTrabajo.serviciosMecanico.mecanico.empleado',
            'ordenTrabajo.repuestosMecanico.repuesto',
            'ordenTrabajo.autorizaciones',
            'metodoPago',
            'usuario',
            'comprobante',
        ]);

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

    public function detallesOrden(OrdenTrabajo $orden): JsonResponse
    {
        $orden->load([
            'cliente',
            'vehiculo.modelo.marcaVehiculo',
            'detalles.servicio',
            'detalles.repuesto',
            'detalles.asignacionTrabajo.mecanico.empleado',
        ]);

        $servicios = $orden->detalles->where('tipo', 'servicio')->values()->map(function ($d) {
            return [
                'id' => $d->id,
                'descripcion' => $d->descripcion ?: ($d->servicio->nombre ?? ''),
                'cantidad' => (float) $d->cantidad,
                'precio_unitario' => (float) $d->precio_unitario,
                'subtotal' => (float) $d->subtotal,
                'mecanico' => $d->asignacionTrabajo?->mecanico?->empleado?->nombre_completo ?? '—',
            ];
        });

        $repuestos = $orden->detalles->where('tipo', 'repuesto')->values()->map(function ($d) {
            return [
                'id' => $d->id,
                'descripcion' => $d->descripcion ?: ($d->repuesto->nombre ?? ''),
                'cantidad' => (float) $d->cantidad,
                'precio_unitario' => (float) $d->precio_unitario,
                'subtotal' => (float) $d->subtotal,
            ];
        });

        $totalPagado = (float) $orden->pagos()->where('estado', 'confirmado')->sum('monto');
        $saldoPendiente = max(0, (float) $orden->total_general - $totalPagado);

        return response()->json([
            'ok' => true,
            'orden' => [
                'id' => $orden->id,
                'numero_orden' => $orden->numero_orden,
                'cliente' => $orden->cliente?->nombre_completo ?? '—',
                'vehiculo' => $orden->vehiculo?->placa ?? '—',
                'marca' => $orden->vehiculo?->modelo?->marcaVehiculo?->nombre ?? '',
                'modelo' => $orden->vehiculo?->modelo?->nombre ?? '',
                'descripcion_problema' => $orden->descripcion_problema,
                'servicios' => $servicios,
                'repuestos' => $repuestos,
                'subtotal_servicios' => (float) $orden->subtotal_servicios,
                'subtotal_repuestos' => (float) $orden->subtotal_repuestos,
                'descuento' => (float) $orden->descuento,
                'total_general' => (float) $orden->total_general,
                'total_pagado' => $totalPagado,
                'saldo_pendiente' => $saldoPendiente,
            ],
        ]);
    }

    public function storeAjax(PagoRequest $request): JsonResponse
    {
        $datos = $request->validated();
        $datos['estado'] = 'confirmado';
        $datos['usuario_id'] = auth()->id();

        try {
            $pago = DB::transaction(function () use ($datos) {
                $pago = Pago::create($datos);
                $comprobante = $this->generarComprobante($pago);
                $pago->load('comprobante');
                return $pago;
            });

            $pago->load(['metodoPago', 'ordenTrabajo.cliente', 'comprobante']);

            return response()->json([
                'ok' => true,
                'message' => 'Pago registrado con éxito.',
                'pago' => [
                    'id' => $pago->id,
                    'monto' => (float) $pago->monto,
                    'metodo' => $pago->metodoPago->nombre ?? '—',
                    'referencia' => $pago->referencia,
                    'fecha' => $pago->fecha_pago?->format('d/m/Y H:i'),
                ],
                'comprobante' => $pago->comprobante ? [
                    'id' => $pago->comprobante->id,
                    'numero' => $pago->comprobante->numero,
                    'url' => route('admin.comprobantes.show', $pago->comprobante),
                ] : null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al registrar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function pagoQr(Request $request): RedirectResponse
    {
        $request->validate(['orden_id' => 'required|exists:ordenes_trabajo,id']);

        $orden = OrdenTrabajo::with('cliente')->findOrFail($request->orden_id);

        $qrMetodo = MetodoPago::where('nombre', 'QR')->first();
        if (!$qrMetodo) {
            return back()->with('error', 'No hay un método de pago QR configurado.');
        }

        $comprobante = DB::transaction(function () use ($orden, $qrMetodo) {
            $pago = Pago::create([
                'orden_trabajo_id' => $orden->id,
                'metodo_pago_id' => $qrMetodo->id,
                'usuario_id' => auth()->id(),
                'fecha_pago' => now(),
                'monto' => $orden->total_general,
                'estado' => 'confirmado',
            ]);

            $anio = now()->format('Y');
            $ultimo = Comprobante::where('numero', 'like', "COMP-{$anio}-%")
                ->orderByDesc('numero')->first();
            $correlativo = $ultimo ? ((int) explode('-', $ultimo->numero)[2]) + 1 : 1;
            $numero = sprintf('COMP-%s-%04d', $anio, $correlativo);

            $comp = Comprobante::create([
                'pago_id'     => $pago->id,
                'cliente_id'  => $orden->cliente_id,
                'numero'      => $numero,
                'fecha_emision' => now(),
                'nit_ci'      => $orden->cliente->nit ?? null,
                'razon_social' => $orden->cliente->nit
                    ? ($orden->cliente->razon_social ?? $orden->cliente->nombre_completo)
                    : 'Consumidor Final',
                'monto_total' => $orden->total_general,
                'estado'      => 'emitido',
            ]);

            if ($orden->estado === 'finalizada') {
                $orden->update(['estado' => 'entregada', 'fecha_entrega' => now()]);
            }

            return $comp;
        });

        return redirect()->route('admin.factura.show', $comprobante);
    }

    private function generarComprobante(Pago $pago): Comprobante
    {
        $anio = now()->format('Y');
        $ultimo = Comprobante::where('numero', 'like', "COMP-{$anio}-%")
            ->orderByDesc('numero')
            ->first();

        $correlativo = 1;
        if ($ultimo) {
            $partes = explode('-', $ultimo->numero);
            $correlativo = ((int) end($partes)) + 1;
        }

        $numero = sprintf('COMP-%s-%04d', $anio, $correlativo);

        return Comprobante::create([
            'pago_id' => $pago->id,
            'cliente_id' => $pago->ordenTrabajo->cliente_id,
            'numero' => $numero,
            'fecha_emision' => now(),
            'monto_total' => $pago->monto,
            'estado' => 'emitido',
        ]);
    }
}
