<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comprobante;
use App\Models\MetodoPago;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PagoStripeController extends AdminController
{
    public function cobrar(Request $request): JsonResponse
    {
        $request->validate([
            'orden_id' => ['required', 'exists:ordenes_trabajo,id'],
        ], [
            'orden_id.required' => 'La orden es obligatoria.',
            'orden_id.exists' => 'La orden no existe.',
        ]);

        $orden = OrdenTrabajo::with(['serviciosMecanico', 'repuestosMecanico', 'cliente'])->findOrFail($request->input('orden_id'));

        // Calcular total real desde servicios y repuestos
        $totalReal = (float) $orden->total_general;
        if ($totalReal <= 0) {
            $serv = $orden->serviciosMecanico->sum('precio_base');
            $rep = $orden->repuestosMecanico->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot);
            $totalReal = $serv + $rep;
        }
        $pagado = (float) $orden->pagos()->where('estado', 'confirmado')->sum('monto');
        $saldoPendiente = max(0, $totalReal - $pagado);

        if ($saldoPendiente <= 0) {
            return response()->json(['ok' => false, 'message' => 'La orden ya está totalmente pagada.'], 422);
        }

        $existePendiente = Pago::where('orden_trabajo_id', $orden->id)
            ->where('estado', 'pendiente')
            ->whereNotNull('stripe_payment_intent_id')
            ->exists();
        if ($existePendiente) {
            return response()->json(['ok' => false, 'message' => 'Ya existe un intento de pago pendiente para esta orden.'], 422);
        }

        $moneda = config('app.moneda', 'usd');

        try {
            $stripe = new StripeService();
            $montoCentavos = (int) round($saldoPendiente * 100);
            $resultado = $stripe->cobrar($montoCentavos, $moneda);

            $metodoPago = MetodoPago::where('nombre', 'like', '%Tarjeta%')->first();

            $pago = Pago::create([
                'orden_trabajo_id'       => $orden->id,
                'metodo_pago_id'         => $metodoPago?->id ?? 1,
                'usuario_id'             => auth()->id(),
                'fecha_pago'             => now(),
                'monto'                  => $saldoPendiente,
                'referencia'             => $resultado['id'],
                'stripe_payment_intent_id' => $resultado['id'],
                'estado'                 => 'confirmado',
            ]);

            // Confirmar PaymentIntent
            $stripe->confirmar($resultado['id']);

            // Generar comprobante con detalle
            $items = [];
            foreach ($orden->serviciosMecanico as $s) {
                $items[] = $s->nombre_servicio . ' Bs ' . number_format($s->precio_base, 2);
            }
            foreach ($orden->repuestosMecanico as $r) {
                $items[] = ($r->repuesto?->nombre ?? 'Repuesto') . ' x' . $r->cantidad . ' Bs ' . number_format($r->cantidad * $r->precio_unitario_snapshot, 2);
            }

            $ultimoId = Comprobante::withTrashed()->max('id') ?? 0;
            Comprobante::create([
                'pago_id'      => $pago->id,
                'cliente_id'   => $orden->cliente_id,
                'numero'       => 'REC-' . now()->format('Ymd') . '-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT),
                'fecha_emision' => now(),
                'monto_total'  => $saldoPendiente,
                'estado'       => 'emitido',
                'observaciones' => implode("\n", $items),
            ]);

            // Entregar orden
            if (! in_array($orden->estado, ['entregada', 'anulada'])) {
                $orden->update(['estado' => 'entregada', 'fecha_entrega' => now()]);
            }

            return response()->json([
                'ok' => true,
                'message' => 'Pago de Bs ' . number_format($saldoPendiente, 2) . ' confirmado. Comprobante REC-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT) . ' generado.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }
}
