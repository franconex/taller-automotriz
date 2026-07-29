<?php

namespace App\Http\Controllers\Admin;

use App\Models\Vacacion;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VacacionController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    public function index(): View
    {
        $user = Auth::user();
        $esAdmin = $user->tieneRol('Administrador') || $user->tieneRol('Gerente');

        $vacaciones = $esAdmin
            ? Vacacion::with('solicitante.empleado')->latest()->paginate(20)
            : Vacacion::with('solicitante.empleado')->where('usuario_solicitante_id', $user->id)->latest()->paginate(20);

        return view('admin.vacaciones.index', compact('vacaciones', 'esAdmin'));
    }

    public function create(): View
    {
        return view('admin.vacaciones.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->tieneRol('Administrador')) {
            return redirect()->route('admin.vacaciones.index')
                ->with('error', 'Los administradores no pueden solicitar vacaciones.');
        }

        $validados = $request->validate([
            'fecha_inicio' => ['required', 'date', 'after_or_equal:today'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'motivo' => ['required', 'string', 'max:500'],
        ]);

        $dias = Carbon::parse($validados['fecha_inicio'])->diffInDays(Carbon::parse($validados['fecha_fin'])) + 1;

        Vacacion::create([
            'usuario_solicitante_id' => $user->id,
            'fecha_inicio' => $validados['fecha_inicio'],
            'fecha_fin' => $validados['fecha_fin'],
            'motivo' => $validados['motivo'],
        ]);

        return redirect()->route('admin.vacaciones.index')
            ->with('success', "Solicitud de vacaciones enviada correctamente ({$dias} día(s)).");
    }

    public function aprobar(Vacacion $vacacion): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->tieneRol('Administrador') && ! $user->tieneRol('Gerente')) {
            abort(403, 'No autorizado para aprobar vacaciones.');
        }

        $vacacion->load('solicitante');

        $vacacion->update([
            'estado' => 'aprobada',
            'usuario_admin_id' => $user->id,
            'fecha_respuesta' => now(),
        ]);

        $vacacion->solicitante()->update(['estado' => 'vacaciones']);

        return redirect()->route('admin.vacaciones.index')
            ->with('success', 'Vacaciones aprobadas. El usuario ahora está en estado "Vacaciones".');
    }

    public function rechazar(Request $request, Vacacion $vacacion): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->tieneRol('Administrador') && ! $user->tieneRol('Gerente')) {
            abort(403, 'No autorizado para rechazar vacaciones.');
        }

        $validados = $request->validate([
            'respuesta_admin' => ['required', 'string', 'max:500'],
        ]);

        $vacacion->update([
            'estado' => 'rechazada',
            'respuesta_admin' => $validados['respuesta_admin'],
            'usuario_admin_id' => $user->id,
            'fecha_respuesta' => now(),
        ]);

        return redirect()->route('admin.vacaciones.index')
            ->with('success', 'Solicitud de vacaciones rechazada.');
    }

    public function finalizar(Vacacion $vacacion): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->tieneRol('Administrador') && ! $user->tieneRol('Gerente')) {
            abort(403, 'No autorizado.');
        }

        if ($vacacion->estado !== 'aprobada') {
            return redirect()->route('admin.vacaciones.index')
                ->with('error', 'Solo se pueden finalizar vacaciones aprobadas.');
        }

        $vacacion->solicitante()->update(['estado' => 'activo']);

        return redirect()->route('admin.vacaciones.index')
            ->with('success', 'Vacaciones finalizadas. El usuario volvió a estado "Activo".');
    }
}
