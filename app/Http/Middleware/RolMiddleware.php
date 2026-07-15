<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'No autenticado.');
        }

        if (! $user->estado) {
            abort(403, 'Usuario inactivo.');
        }

        if (! $user->rol || ! in_array($user->rol->nombre, $roles)) {
            abort(403, 'No autorizado para este panel.');
        }

        return $next($request);
    }
}
