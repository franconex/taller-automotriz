<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermisoMiddleware
{
    public function handle(Request $request, Closure $next, ...$permisos): Response
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'No autenticado.');
        }

        if (! $user->estado) {
            abort(403, 'Usuario inactivo.');
        }

        if (! $user->rol || ! $user->rol->estado) {
            abort(403, 'Rol inactivo o no asignado.');
        }

        foreach ($permisos as $permiso) {
            if ($user->tienePermiso($permiso)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permiso para realizar esta acción.');
    }
}
