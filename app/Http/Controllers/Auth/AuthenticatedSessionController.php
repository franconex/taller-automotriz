<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->rol || ! $user->rol->estado) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'login' => 'No tienes un rol activo asignado. Contacta al administrador.',
            ]);
        }

        if ($user->empleado && ! $user->empleado->estado) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'login' => 'No puedes iniciar sesión porque el empleado asociado a tu cuenta fue dado de baja. Contacta al administrador.',
            ]);
        }

        if ($user->esCliente() && ! $user->cliente) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'login' => 'No puedes iniciar sesión porque tu cuenta de cliente no está activa. Contacta al administrador.',
            ]);
        }

        $user->update(['ultimo_acceso' => now()]);

        return $this->redirectAfterLogin($user);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectBasedOnRole(User $user): string
    {
        return match ($user->rol->nombre) {
            'Recepcionista' => route('admin.citas.index'),
            'Mecánico' => route('mecanico.dashboard'),
            'Cliente' => route('cliente.dashboard'),
            default => route('admin.dashboard'),
        };
    }

    private function redirectAfterLogin(User $user): RedirectResponse
    {
        $intended = session()->get('url.intended');

        if ($intended && ! $this->intendedUrlMatchesRole($intended, $user)) {
            session()->forget('url.intended');
        }

        return redirect()->intended($this->redirectBasedOnRole($user));
    }

    private function intendedUrlMatchesRole(string $url, User $user): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';

        if ($user->esCliente()) {
            return str_contains($path, '/cliente/');
        }

        if ($user->tieneRol('Mecánico')) {
            return str_contains($path, '/mecanico/');
        }

        return str_contains($path, '/admin/');
    }
}
