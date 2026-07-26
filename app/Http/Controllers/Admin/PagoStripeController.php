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
    const MONEDA = 'usd';

    public function cobrar(Request $request): JsonResponse
    {
        $request->validate([
            'orden_id' => ['required', 'exists:ordenes_trabajo,id'],
        ], [
            'orden_id.required' => 'La orden es obligatoria.',
            'orden_id.exists' => 'La orden no existe.',
        ]);

        $orden = OrdenTrabajo::findOrFail($request->input('orden_id'));
        $monto = (float) $orden->total_general;

        if ($monto <= 0) {
            return response()->json(['ok' => false, 'message' => 'El total de la orden debe ser mayor a cero.'], 422);
        }

        try {
            $stripe = new StripeService();
            $montoCentavos = (int) round($monto * 100);
            $resultado = $stripe->cobrar($montoCentavos, self::MONEDA);

            $metodoPago = MetodoPago::where('nombre', 'Tarjeta de Crédito')->first()
                ?? MetodoPago::where('nombre', 'like', '%Tarjeta%')->first();

            Pago::create([
                'orden_trabajo_id' => $orden->id,
                'metodo_pago_id' => $metodoPago?->id ?? 1,
                'usuario_id' => auth()->id(),
                'fecha_pago' => now(),
                'monto' => $monto,
                'referencia' => $resultado['id'],
                'stripe_payment_intent_id' => $resultado['id'],
                'estado' => 'confirmado',
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Pago exitoso.',
                'referencia' => $resultado['id'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }
}
