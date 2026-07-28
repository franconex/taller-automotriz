<?php

namespace App\Http\Controllers\Admin;

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
            'monto'    => ['required', 'numeric', 'min:0.01'],
        ], [
            'orden_id.required' => 'La orden es obligatoria.',
            'orden_id.exists' => 'La orden no existe.',
            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser un número.',
            'monto.min' => 'El monto debe ser mayor a cero.',
        ]);

        $orden = OrdenTrabajo::findOrFail($request->input('orden_id'));
        $montoSolicitado = (float) $request->input('monto');

        $pagado = (float) $orden->pagos()->where('estado', 'confirmado')->sum('monto');
        $saldoPendiente = max(0, (float) $orden->total_general - $pagado);

        if ($montoSolicitado > $saldoPendiente + 0.01) {
            return response()->json(['ok' => false, 'message' => 'El monto excede el saldo pendiente (' . number_format($saldoPendiente, 2) . ').'], 422);
        }

        if ($montoSolicitado <= 0) {
            return response()->json(['ok' => false, 'message' => 'El monto debe ser mayor a cero.'], 422);
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
            $montoCentavos = (int) round($montoSolicitado * 100);
            $resultado = $stripe->cobrar($montoCentavos, $moneda);

            $metodoPago = MetodoPago::where('nombre', 'like', '%Tarjeta%')->first();

            $pago = Pago::create([
                'orden_trabajo_id' => $orden->id,
                'metodo_pago_id' => $metodoPago?->id ?? 1,
                'usuario_id' => auth()->id(),
                'fecha_pago' => now(),
                'monto' => $montoSolicitado,
                'referencia' => $resultado['id'],
                'stripe_payment_intent_id' => $resultado['id'],
                'estado' => 'pendiente',
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Pago iniciado. Confirma con tu banco.',
                'client_secret' => $resultado['client_secret'] ?? null,
                'pago_id' => $pago->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al procesar el pago. Intenta de nuevo o contacta recepción.',
            ], 500);
        }
    }
}
