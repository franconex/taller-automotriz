<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditoriaController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Auditoria::query()->with('usuario');

        $this->aplicarFiltros($request, $query, ['modulo', 'accion']);
        $this->aplicarBusqueda($query, $request, [
            'usuario.nombre',
            'entidad_tipo',
            'accion',
            'modulo',
        ]);

        if ($request->filled('desde')) {
            $query->whereDate('fecha_accion', '>=', $request->input('desde'));
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_accion', '<=', $request->input('hasta'));
        }

        $registros = $query->orderByDesc('fecha_accion')->paginate(20)->withQueryString();

        $modulos = Auditoria::query()
            ->select('modulo')
            ->whereNotNull('modulo')
            ->distinct()
            ->orderBy('modulo')
            ->pluck('modulo');

        return view('admin.auditoria.index', [
            'registros' => $registros,
            'modulos' => $modulos,
        ]);
    }

    public function show(Auditoria $auditoria): View
    {
        $auditoria->load('usuario');

        return view('admin.auditoria.show', [
            'registro' => $auditoria,
        ]);
    }
}
