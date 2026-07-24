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

        if (! $user || ! $user->isActivo()) {
            abort(403, 'No autenticado o usuario inactivo.');
        }

        if (! $user->rol || ! $user->rol->estado) {
            abort(403, 'Rol inactivo o no asignado.');
        }

        foreach ($roles as $rol) {
            if ($user->tieneRol($rol)) {
                return $next($request);
            }
        }

        abort(403, 'No autorizado para este panel.');
    }
}
