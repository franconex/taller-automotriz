<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Auditoria::with('usuario:id,nombre,email');

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('entidad_afectada')) {
            $query->where('entidad_afectada', $request->entidad_afectada);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $registros = $query->orderByDesc('id')->paginate(20);

        $acciones = Auditoria::select('accion')->distinct()->orderBy('accion')->pluck('accion');
        $entidades = Auditoria::select('entidad_afectada')->distinct()->orderBy('entidad_afectada')->pluck('entidad_afectada');

        return view('admin.auditoria.index', compact('registros', 'acciones', 'entidades'));
    }
}
