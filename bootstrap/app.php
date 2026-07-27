<?php

use App\Http\Middleware\PermisoMiddleware;
use App\Http\Middleware\RolMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rol' => RolMiddleware::class,
            'permiso' => PermisoMiddleware::class,
        ]);

        RedirectIfAuthenticated::redirectUsing(function (Request $request) {
            $user = $request->user();

            if ($user && $user->rol) {
                return match ($user->rol->nombre) {
                    'Administrador' => route('admin.dashboard'),
                    'Gerente'      => route('admin.dashboard'),
                    'Recepcionista' => route('admin.citas.index'),
                    'Mecánico'     => route('admin.dashboard'),
                    default => route('admin.dashboard'),
                };
            }

            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Recurso no encontrado.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No autorizado.'], 403);
            }
            return response()->view('errors.403', [], 403);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No autenticado.'], 401);
            }
            return redirect()->guest(route('login'));
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Sesión expirada.'], 419);
                }
                return response()->view('errors.419', [], 419);
            }
            if ($e->getStatusCode() === 503) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Sistema en mantenimiento.'], 503);
                }
                return response()->view('errors.503', [], 503);
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error interno del servidor.'], 500);
            }
            if (app()->isDownForMaintenance()) {
                return response()->view('errors.503', [], 503);
            }
            return response()->view('errors.500', [], 500);
        });
    })->create();
