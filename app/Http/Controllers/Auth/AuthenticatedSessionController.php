<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->user()->update([
            'ultimo_acceso' => now(),
        ]);

        return redirect()->intended($this->panelRouteFor($request->user()));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function panelRouteFor(User $user): string
    {
        $map = [
            'Administrador' => 'admin.dashboard',
            'Gerente'       => 'gerente.dashboard',
            'Recepcionista'  => 'recepcionista.dashboard',
            'Mecanico'      => 'mecanico.dashboard',
        ];

        $roleName = $user->rol?->nombre;

        if (! $roleName || ! array_key_exists($roleName, $map)) {
            abort(403, 'Rol desconocido.');
        }

        return route($map[$roleName]);
    }
}
