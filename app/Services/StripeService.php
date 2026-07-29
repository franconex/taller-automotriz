<?php

namespace App\Services;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    public function checkout(array $params): Session
    {
        return Session::create([
            'line_items' => [$params['line_item']],
            'mode' => 'payment',
            'success_url' => $params['success_url'],
            'cancel_url' => $params['cancel_url'],
            'metadata' => $params['metadata'] ?? [],
            'locale' => 'es',
        ]);
    }

    public function retrieveSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }
}
