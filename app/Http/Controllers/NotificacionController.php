<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    public function index(): View
    {
        $notificaciones = Auth::user()->notifications()->paginate(20);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function noLeidas(): JsonResponse
    {
        $user = Auth::user();

        // Auto-limpiar notificaciones viejas (>7 días) marcándolas como leídas
        $user->unreadNotifications()->where('created_at', '<', now()->subDays(7))->update(['read_at' => now()]);

        // Solo notificaciones de los últimos 3 días
        $query = $user->unreadNotifications()->where('created_at', '>=', now()->subDays(3));

        $notificaciones = (clone $query)->latest()->limit(5)->get();
        $total = (clone $query)->count();

        return response()->json([
            'total' => $total,
            'items' => $notificaciones->map(fn ($n) => [
                'id' => $n->id,
                'titulo' => $n->data['titulo'] ?? 'Notificación',
                'mensaje' => $n->data['mensaje'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'icono' => $n->data['icono'] ?? 'bi-bell',
                'tiempo' => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    public function marcarLeida(string $id): JsonResponse
    {
        $notificacion = Auth::user()->notifications()->where('id', $id)->first();

        if ($notificacion) {
            $notificacion->markAsRead();
        }

        return response()->json(['ok' => true]);
    }

    public function marcarTodasLeidas(): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['ok' => true]);
    }
}
