<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoServicio;
use App\Services\AuditService;
use Illuminate\Http\Request;

class TipoServicioController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function index()
    {
        $tipos = TipoServicio::orderBy('nombre')->paginate(15);

        return view('admin.tipo-servicios.index', compact('tipos'));
    }

    public function create()
    {
        return view('admin.tipo-servicios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:tipo_servicios,nombre'],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'estado' => ['sometimes', 'boolean'],
        ]);

        $tipo = TipoServicio::create($data);

        $this->auditService->register(
            'crear',
            'TipoServicio',
            $tipo->id,
            null,
            $data,
            "Tipo de servicio {$tipo->nombre} creado",
        );

        return to_route('admin.tipo-servicios.index')
            ->with('success', "Tipo de servicio {$tipo->nombre} creado correctamente.");
    }

    public function edit(TipoServicio $tipoServicio)
    {
        return view('admin.tipo-servicios.edit', compact('tipoServicio'));
    }

    public function update(Request $request, TipoServicio $tipoServicio)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:tipo_servicios,nombre,'.$tipoServicio->id],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'estado' => ['sometimes', 'boolean'],
        ]);

        $anterior = $tipoServicio->only(['nombre', 'descripcion', 'estado']);
        $tipoServicio->update($data);

        $this->auditService->register(
            'editar',
            'TipoServicio',
            $tipoServicio->id,
            $anterior,
            $data,
            "Tipo de servicio {$tipoServicio->nombre} editado",
        );

        return to_route('admin.tipo-servicios.index')
            ->with('success', "Tipo de servicio {$tipoServicio->nombre} actualizado correctamente.");
    }

    public function toggleEstado(TipoServicio $tipoServicio)
    {
        $tipoServicio->update(['estado' => ! $tipoServicio->estado]);

        $accion = $tipoServicio->estado ? 'activar' : 'desactivar';

        $this->auditService->register(
            $accion,
            'TipoServicio',
            $tipoServicio->id,
            ['estado' => ! $tipoServicio->estado],
            ['estado' => $tipoServicio->estado],
            "Tipo de servicio {$tipoServicio->nombre} {$accion}do",
        );

        return to_route('admin.tipo-servicios.index')->with(
            'success',
            ($tipoServicio->estado ? 'Tipo de servicio activado' : 'Tipo de servicio desactivado').' correctamente.',
        );
    }
}
