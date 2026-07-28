<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permiso;
use App\Models\SolicitudPermiso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SolicitudPermisoController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:solicitudes-permiso.ver', only: ['index', 'show']),
            new Middleware('permiso:solicitudes-permiso.crear', only: ['create', 'store']),
            new Middleware('permiso:solicitudes-permiso.aprobar', only: ['aprobar', 'rechazar']),
        ];
    }

    public function index(): View
    {
        $user = Auth::user();
        $esAdmin = $user->tienePermiso('solicitudes-permiso.aprobar');

        if ($esAdmin) {
            $solicitudes = SolicitudPermiso::with(['solicitante', 'permiso'])
                ->orderByRaw("FIELD(estado, 'pendiente', 'aprobada', 'rechazada')")
                ->latest()
                ->paginate(20);
        } else {
            $solicitudes = SolicitudPermiso::with(['solicitante', 'permiso'])
                ->where('usuario_solicitante_id', $user->id)
                ->latest()
                ->paginate(20);
        }

        return view('admin.solicitudes-permiso.index', [
            'solicitudes' => $solicitudes,
            'esAdmin' => $esAdmin,
        ]);
    }

    protected function noPuedeSolicitar(): bool
    {
        $user = Auth::user();
        return $user->tieneRol('Administrador') || $user->tieneRol('Mecánico');
    }

    public function create(): View
    {
        if ($this->noPuedeSolicitar()) {
            return redirect()->route('admin.solicitudes-permiso.index')
                ->with('error', 'No tienes permiso para realizar solicitudes.');
        }

        $user = Auth::user();

        $idsYaAsignados = $user->rol->permisos()->pluck('permisos.id');

        $permisosDisponibles = Permiso::whereNotIn('id', $idsYaAsignados)
            ->where('modulo', '!=', 'configuracion')
            ->orderBy('modulo')
            ->orderBy('nombre')
            ->get()
            ->groupBy('modulo');

        return view('admin.solicitudes-permiso.create', [
            'permisosAgrupados' => $permisosDisponibles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->noPuedeSolicitar()) {
            return redirect()->route('admin.solicitudes-permiso.index')
                ->with('error', 'No tienes permiso para realizar solicitudes.');
        }

        $validados = $request->validate([
            'permiso_id' => ['required', 'exists:permisos,id'],
            'motivo' => ['required', 'string', 'max:500'],
        ]);

        $user = Auth::user();

        $yaTiene = $user->rol->permisos()->where('permiso_id', $validados['permiso_id'])->exists();

        if ($yaTiene) {
            return redirect()->route('admin.solicitudes-permiso.index')
                ->with('error', 'Ya tienes este permiso asignado.');
        }

        SolicitudPermiso::create([
            'usuario_solicitante_id' => $user->id,
            'permiso_id' => $validados['permiso_id'],
            'motivo' => $validados['motivo'],
        ]);

        return redirect()->route('admin.solicitudes-permiso.index')
            ->with('success', 'Solicitud de permiso enviada correctamente.');
    }

    public function show(SolicitudPermiso $solicitudPermiso): View
    {
        $solicitudPermiso->load(['solicitante', 'admin', 'permiso']);

        $yaTienePermiso = $solicitudPermiso->solicitante->rol->permisos()
            ->where('permiso_id', $solicitudPermiso->permiso_id)
            ->exists();

        return view('admin.solicitudes-permiso.show', [
            'solicitud' => $solicitudPermiso,
            'yaTienePermiso' => $yaTienePermiso,
        ]);
    }

    public function aprobar(Request $request, SolicitudPermiso $solicitudPermiso): RedirectResponse
    {
        $validados = $request->validate([
            'respuesta_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $solicitudPermiso->load('solicitante.rol');

        $role = $solicitudPermiso->solicitante->rol;

        if (!$role->permisos()->where('permiso_id', $solicitudPermiso->permiso_id)->exists()) {
            $role->permisos()->attach($solicitudPermiso->permiso_id);
        }

        $solicitudPermiso->update([
            'estado' => 'aprobada',
            'respuesta_admin' => $validados['respuesta_admin'],
            'usuario_admin_id' => Auth::id(),
            'fecha_respuesta' => now(),
        ]);

        return redirect()->route('admin.solicitudes-permiso.index')
            ->with('success', 'Solicitud aprobada. El permiso se ha asignado al rol del usuario.');
    }

    public function rechazar(Request $request, SolicitudPermiso $solicitudPermiso): RedirectResponse
    {
        $validados = $request->validate([
            'respuesta_admin' => ['required', 'string', 'max:500'],
        ]);

        $solicitudPermiso->update([
            'estado' => 'rechazada',
            'respuesta_admin' => $validados['respuesta_admin'],
            'usuario_admin_id' => Auth::id(),
            'fecha_respuesta' => now(),
        ]);

        return redirect()->route('admin.solicitudes-permiso.index')
            ->with('success', 'Solicitud rechazada.');
    }
}
