<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ClienteAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleSocialiteController extends Controller
{
    public function __construct(
        private ClienteAccountService $accountService
    ) {}

    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'No se pudo autenticar con Google. Intenta de nuevo.');
        }

        if (! $googleUser->email) {
            return redirect()->route('login')
                ->with('error', 'Google no proporcionó un correo electrónico. Usa otro método.');
        }

        try {
            $user = $this->accountService->registrarConGoogle($googleUser);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('login')
                ->withErrors($e->errors());
        }

        $request->session()->regenerate();

        Auth::login($user, true);

        if (! $user->email_verified_at && ($googleUser->user['email_verified'] ?? false)) {
            $user->update(['email_verified_at' => now()]);
        }

        $redirectTo = $user->esCliente() ? route('cliente.dashboard') : route('admin.dashboard');

        $request->session()->put('url.intended', $redirectTo);
        $request->session()->save();

        return redirect()->to($redirectTo)
            ->with('success', 'Inicio de sesión con Google exitoso.');
    }
}
