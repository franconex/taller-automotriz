<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    public function cobrar(int $montoCentavos, string $moneda = 'usd'): array
    {
        $intent = PaymentIntent::create([
            'amount' => $montoCentavos,
            'currency' => $moneda,
            'payment_method_types' => ['card'],
            'description' => 'Pago taller automotriz',
        ]);

        return [
            'id' => $intent->id,
            'client_secret' => $intent->client_secret,
            'status' => $intent->status,
        ];
    }

    public function confirmar(string $paymentIntentId): array
    {
        $intent = PaymentIntent::retrieve($paymentIntentId);

        return [
            'id' => $intent->id,
            'status' => $intent->status,
            'amount' => $intent->amount,
        ];
    }
}
