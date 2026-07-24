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

        if (! $user || ! $user->isActivo()) {
            abort(403, 'No autenticado o usuario inactivo.');
        }

        if (! $user->rol || ! $user->rol->estado) {
            abort(403, 'Rol inactivo o no asignado.');
        }

        $permisosUsuario = $user->rol->permisos()->pluck('codigo')->toArray();

        if (array_intersect($permisos, $permisosUsuario)) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para realizar esta acción.');
    }
}
