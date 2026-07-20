<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use App\Services\AuditService;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function index()
    {
        $especialidades = Especialidad::orderBy('nombre')->paginate(15);

        return view('admin.especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        return view('admin.especialidades.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:especialidades,nombre'],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'estado' => ['sometimes', 'boolean'],
        ]);

        $especialidad = Especialidad::create($data);

        $this->auditService->register(
            'crear',
            'Especialidad',
            $especialidad->id,
            null,
            $data,
            "Especialidad {$especialidad->nombre} creada",
        );

        return to_route('admin.especialidades.index')
            ->with('success', "Especialidad {$especialidad->nombre} creada correctamente.");
    }

    public function edit(Especialidad $especialidade)
    {
        return view('admin.especialidades.edit', compact('especialidade'));
    }

    public function update(Request $request, Especialidad $especialidade)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:especialidades,nombre,'.$especialidade->id],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'estado' => ['sometimes', 'boolean'],
        ]);

        $anterior = $especialidade->only(['nombre', 'descripcion', 'estado']);
        $especialidade->update($data);

        $this->auditService->register(
            'editar',
            'Especialidad',
            $especialidade->id,
            $anterior,
            $data,
            "Especialidad {$especialidade->nombre} editada",
        );

        return to_route('admin.especialidades.index')
            ->with('success', "Especialidad {$especialidade->nombre} actualizada correctamente.");
    }

    public function toggleEstado(Especialidad $especialidade)
    {
        $especialidade->update(['estado' => ! $especialidade->estado]);

        $accion = $especialidade->estado ? 'activar' : 'desactivar';

        $this->auditService->register(
            $accion,
            'Especialidad',
            $especialidade->id,
            ['estado' => ! $especialidade->estado],
            ['estado' => $especialidade->estado],
            "Especialidad {$especialidade->nombre} {$accion}da",
        );

        return to_route('admin.especialidades.index')->with(
            'success',
            ($especialidade->estado ? 'Especialidad activada' : 'Especialidad desactivada').' correctamente.',
        );
    }
}
