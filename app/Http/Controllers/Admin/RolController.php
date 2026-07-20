<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRolRequest;
use App\Http\Requests\Admin\UpdateRolRequest;
use App\Models\permisos;
use App\Models\Rol;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function index()
    {
        $roles = Rol::withCount('users', 'permisos')
            ->orderBy('nombre')
            ->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(StoreRolRequest $request)
    {
        $rol = Rol::create($request->validated());

        $this->auditService->register(
            'crear',
            'Rol',
            $rol->id,
            null,
            $request->safe()->toArray(),
            "Rol {$rol->nombre} creado",
        );

        return to_route('admin.roles.index')
            ->with('success', "Rol {$rol->nombre} creado correctamente.");
    }

    public function show(Rol $rol)
    {
        $rol->loadCount('users', 'permisos');
        $rol->load('permisos:id,nombre,codigo,modulo');

        return view('admin.roles.show', compact('rol'));
    }

    public function edit(Rol $rol)
    {
        $rol->load('permisos:id');
        $permisos = permisos::orderBy('modulo')->orderBy('nombre')->get();
        $permisosAgrupados = $permisos->groupBy('modulo');

        return view('admin.roles.edit', compact('rol', 'permisos', 'permisosAgrupados'));
    }

    public function update(UpdateRolRequest $request, Rol $rol)
    {
        $anterior = $rol->only(['nombre', 'descripcion', 'estado']);
        $rol->update($request->validated());

        $this->auditService->register(
            'editar',
            'Rol',
            $rol->id,
            $anterior,
            $request->safe()->toArray(),
            "Rol {$rol->nombre} editado",
        );

        return to_route('admin.roles.index')
            ->with('success', "Rol {$rol->nombre} actualizado correctamente.");
    }

    public function toggleEstado(Rol $rol)
    {
        if ($rol->nombre === 'Administrador' && $rol->estado) {
            $adminsActivos = Rol::where('nombre', 'Administrador')->first()?->users()->where('estado', true)->count() ?? 0;

            if ($adminsActivos > 0) {
                return back()->with('error', 'No puedes desactivar el rol Administrador mientras haya administradores activos.');
            }
        }

        $rol->update(['estado' => ! $rol->estado]);

        $accion = $rol->estado ? 'activar' : 'desactivar';

        $this->auditService->register(
            $accion,
            'Rol',
            $rol->id,
            ['estado' => ! $rol->estado],
            ['estado' => $rol->estado],
            "Rol {$rol->nombre} {$accion}do",
        );

        $mensaje = $rol->estado
            ? "Rol {$rol->nombre} activado correctamente."
            : "Rol {$rol->nombre} desactivado correctamente.";

        return to_route('admin.roles.index')->with('success', $mensaje);
    }

    public function asignarPermisos(Request $request, Rol $rol)
    {
        $request->validate([
            'permisos' => ['nullable', 'array'],
            'permisos.*' => ['exists:permisos,id'],
        ]);

        $permisosSeleccionados = $request->input('permisos', []);

        DB::transaction(function () use ($rol, $permisosSeleccionados) {
            $rol->permisos()->sync($permisosSeleccionados);
        });

        $this->auditService->register(
            'asignar_permisos',
            'Rol',
            $rol->id,
            null,
            ['permisos_asignados' => $permisosSeleccionados],
            "Permisos asignados al rol {$rol->nombre}",
        );

        return to_route('admin.roles.index')
            ->with('success', "Permisos asignados al rol {$rol->nombre} correctamente.");
    }
}
