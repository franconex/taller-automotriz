<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comprobante;
use App\Models\MetodoPago;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Models\Setting;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PagoStripeController extends AdminController
{
    public function cobrar(Request $request): JsonResponse
    {
        $request->validate([
            'orden_id' => ['required', 'exists:ordenes_trabajo,id'],
            'nit' => ['nullable', 'string', 'max:30'],
            'razon_social' => ['nullable', 'string', 'max:200'],
        ]);

        $orden = OrdenTrabajo::with(['serviciosMecanico', 'repuestosMecanico', 'cliente', 'autorizaciones'])->findOrFail($request->input('orden_id'));

        $nit = $request->input('nit');
        $razonSocial = $request->input('razon_social') ?? $orden->cliente->nombre_completo;

        $serv = $orden->serviciosMecanico->sum('precio_base');
        $rep = $orden->repuestosMecanico->sum(fn($r) => $r->cantidad * $r->precio_unitario_snapshot);
        $manoObra = (float) $orden->autorizaciones->sum('mano_de_obra');
        $totalReal = $serv + $rep + $manoObra;

        $pagado = (float) $orden->pagos()->where('estado', 'confirmado')->sum('monto');
        $saldoPendiente = max(0, $totalReal - $pagado);

        if ($saldoPendiente <= 0) {
            return response()->json(['ok' => false, 'message' => 'La orden ya está totalmente pagada.'], 422);
        }

        $montoCentavos = (int) round($saldoPendiente * 100);
        $monedaRaw = Setting::obtener('moneda', 'BOB');
        $moneda = strtolower(trim(explode(' —', $monedaRaw)[0]));

        session()->put('stripe_pago', [
            'orden_id' => $orden->id,
            'monto' => $saldoPendiente,
            'nit' => $nit,
            'razon_social' => $razonSocial,
            'metodo_pago_id' => MetodoPago::where('nombre', 'like', '%Tarjeta%')->first()?->id ?? 1,
        ]);

        try {
            $stripe = new StripeService();

            $descripcion = 'Orden ' . $orden->numero_orden . ' - ' . ($orden->cliente->nombre_completo ?? '');

            $checkout = $stripe->checkout([
                'line_item' => [
                    'price_data' => [
                        'currency' => $moneda,
                        'product_data' => ['name' => $descripcion],
                        'unit_amount' => $montoCentavos,
                    ],
                    'quantity' => 1,
                ],
                'success_url' => route('admin.pagos.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('admin.pagos.stripe.cancel'),
                'metadata' => [
                    'orden_id' => (string) $orden->id,
                ],
            ]);

            return response()->json([
                'ok' => true,
                'url' => $checkout->url,
            ]);
        } catch (\Throwable $e) {
            Log::error('Stripe checkout error: ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'message' => 'Error al conectar con Stripe: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function success(Request $request): RedirectResponse
    {
        $sessionId = $request->input('session_id');
        if (! $sessionId) {
            return redirect()->route('admin.pagos.index')->with('error', 'No se recibió el ID de sesión de Stripe.');
        }

        $pagoData = session()->pull('stripe_pago');
        if (! $pagoData) {
            return redirect()->route('admin.pagos.index')->with('error', 'No se encontraron datos de pago. Intenta de nuevo.');
        }

        try {
            $stripe = new StripeService();
            $session = $stripe->retrieveSession($sessionId);

            if ($session->payment_status !== 'paid') {
                return redirect()->route('admin.pagos.index')->with('error', 'El pago no fue completado en Stripe.');
            }

            $orden = OrdenTrabajo::with(['cliente', 'serviciosMecanico', 'repuestosMecanico'])->findOrFail($pagoData['orden_id']);

            $comprobante = null;

            DB::transaction(function () use ($orden, $pagoData, $session, &$comprobante) {
                $pago = Pago::create([
                    'orden_trabajo_id' => $orden->id,
                    'metodo_pago_id' => $pagoData['metodo_pago_id'],
                    'usuario_id' => auth()->id(),
                    'fecha_pago' => now(),
                    'monto' => $pagoData['monto'],
                    'referencia' => $session->id,
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'estado' => 'confirmado',
                ]);

                $ultimoId = Comprobante::withTrashed()->max('id') ?? 0;
                $comprobante = Comprobante::create([
                    'pago_id' => $pago->id,
                    'cliente_id' => $orden->cliente_id,
                    'numero' => 'FACT-' . now()->format('Ymd') . '-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT),
                    'fecha_emision' => now(),
                    'nit_ci' => $pagoData['nit'] ?? null,
                    'razon_social' => $pagoData['razon_social'] ?? $orden->cliente->nombre_completo,
                    'monto_total' => $pagoData['monto'],
                    'estado' => 'emitido',
                ]);

                if (! in_array($orden->estado, ['entregada', 'anulada'])) {
                    $orden->update(['estado' => 'entregada', 'fecha_entrega' => now()]);
                }

                if ($pagoData['nit'] && ! $orden->cliente->nit) {
                    $orden->cliente->update([
                        'nit' => $pagoData['nit'],
                        'razon_social' => $pagoData['razon_social'],
                    ]);
                }
            });

            $url = $comprobante
                ? route('admin.factura.show', $comprobante)
                : route('admin.pagos.index');

            return redirect()->to($url)->with('success', 'Pago de Bs ' . number_format($pagoData['monto'], 2) . ' confirmado. Factura generada.');
        } catch (\Throwable $e) {
            Log::error('Stripe success error: ' . $e->getMessage());
            return redirect()->route('admin.pagos.index')->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request): RedirectResponse
    {
        session()->forget('stripe_pago');
        return redirect()->route('admin.pagos.index')->with('error', 'Pago con tarjeta cancelado.');
    }
}
