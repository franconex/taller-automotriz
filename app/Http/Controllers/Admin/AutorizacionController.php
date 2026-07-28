<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auditoria;
use App\Models\Autorizacion;
use App\Models\OrdenTrabajo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AutorizacionController extends AdminController
{
    public function index(): View
    {
        $query = Autorizacion::with([
            'ordenTrabajo.cliente',
            'ordenTrabajo.vehiculo',
            'usuarioSolicitante',
        ])->latest('fecha_solicitud');

        $this->scopeSucursal($query, 'orden_trabajo_id', 'App\Models\OrdenTrabajo', 'sucursal_id');

        return view('admin.autorizaciones.index', [
            'autorizaciones' => $query->paginate(20),
        ]);
    }

    public function create(OrdenTrabajo $ordenTrabajo): View
    {
        Gate::authorize('create', Autorizacion::class);

        $ordenTrabajo->load('cliente', 'vehiculo');

        return view('admin.autorizaciones.create', ['ordene' => $ordenTrabajo]);
    }

    public function store(Request $request, OrdenTrabajo $ordenTrabajo): RedirectResponse
    {
        Gate::authorize('create', Autorizacion::class);

        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:200'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'importe' => ['required', 'numeric', 'min:0', 'max:999999'],
        ]);

        DB::transaction(function () use ($ordenTrabajo, $data) {
            $auth = Autorizacion::create([
                'orden_trabajo_id' => $ordenTrabajo->id,
                'usuario_solicitante_id' => Auth::id(),
                'titulo' => $data['titulo'],
                'descripcion' => $data['descripcion'],
                'importe' => $data['importe'],
                'estado' => 'pendiente',
                'fecha_solicitud' => now(),
            ]);

            Auditoria::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear',
                'modulo' => 'autorizaciones',
                'entidad_tipo' => 'Autorizacion',
                'entidad_id' => $auth->id,
                'datos_nuevos' => json_encode([
                    'orden_trabajo_id' => $ordenTrabajo->id,
                    'titulo' => $data['titulo'],
                    'importe' => $data['importe'],
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'fecha_accion' => now(),
            ]);
        });

        return redirect()->route('admin.autorizaciones.index')
            ->with('success', 'Solicitud de autorización enviada al cliente.');
    }

    public function show(Autorizacion $autorizacione): View
    {
        Gate::authorize('view', $autorizacione);

        $autorizacione->load([
            'ordenTrabajo.cliente',
            'ordenTrabajo.vehiculo',
            'usuarioSolicitante',
            'respondidoPor',
        ]);

        return view('admin.autorizaciones.show', compact('autorizacione'));
    }

    public function cancelar(Autorizacion $autorizacione): RedirectResponse
    {
        Gate::authorize('create', Autorizacion::class);

        if ($autorizacione->esFinal()) {
            return back()->with('error', 'La solicitud ya tiene una respuesta final.');
        }

        DB::transaction(function () use ($autorizacione) {
            $autorizacione->update([
                'estado' => 'cancelada',
                'fecha_respuesta' => now(),
                'respondido_por_id' => Auth::id(),
            ]);

            Auditoria::create([
                'usuario_id' => Auth::id(),
                'accion' => 'cancelar',
                'modulo' => 'autorizaciones',
                'entidad_tipo' => 'Autorizacion',
                'entidad_id' => $autorizacione->id,
                'datos_anteriores' => json_encode(['estado' => $autorizacione->estado]),
                'datos_nuevos' => json_encode(['estado' => 'cancelada']),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'fecha_accion' => now(),
            ]);
        });

        return redirect()->route('admin.autorizaciones.index')
            ->with('success', 'Solicitud cancelada.');
    }
}
