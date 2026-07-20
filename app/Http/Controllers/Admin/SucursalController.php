<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSucursalRequest;
use App\Http\Requests\Admin\UpdateSucursalRequest;
use App\Models\sucursal;
use App\Services\AuditService;

class SucursalController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function index()
    {
        $sucursales = sucursal::withCount('empleados')
            ->orderBy('nombre')
            ->paginate(15);

        return view('admin.sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        return view('admin.sucursales.create');
    }

    public function store(StoreSucursalRequest $request)
    {
        $sucursal = sucursal::create($request->validated());

        $this->auditService->register(
            'crear',
            'Sucursal',
            $sucursal->id,
            null,
            $request->safe()->toArray(),
            "Sucursal {$sucursal->nombre} creada",
        );

        return to_route('admin.sucursales.index')
            ->with('success', "Sucursal {$sucursal->nombre} creada correctamente.");
    }

    public function show(sucursal $sucursale)
    {
        $sucursale->loadCount('empleados');
        $sucursale->load('empleados.user:id,nombre,email');

        return view('admin.sucursales.show', compact('sucursale'));
    }

    public function edit(sucursal $sucursale)
    {
        return view('admin.sucursales.edit', compact('sucursale'));
    }

    public function update(UpdateSucursalRequest $request, sucursal $sucursale)
    {
        $anterior = $sucursale->only(['nombre', 'direccion', 'telefono', 'estado']);
        $sucursale->update($request->validated());

        $this->auditService->register(
            'editar',
            'Sucursal',
            $sucursale->id,
            $anterior,
            $request->safe()->toArray(),
            "Sucursal {$sucursale->nombre} editada",
        );

        return to_route('admin.sucursales.index')
            ->with('success', "Sucursal {$sucursale->nombre} actualizada correctamente.");
    }

    public function toggleEstado(sucursal $sucursale)
    {
        $sucursale->update(['estado' => ! $sucursale->estado]);

        $accion = $sucursale->estado ? 'activar' : 'desactivar';

        $this->auditService->register(
            $accion,
            'Sucursal',
            $sucursale->id,
            ['estado' => ! $sucursale->estado],
            ['estado' => $sucursale->estado],
            "Sucursal {$sucursale->nombre} {$accion}da",
        );

        $mensaje = $sucursale->estado
            ? "Sucursal {$sucursale->nombre} activada correctamente."
            : "Sucursal {$sucursale->nombre} desactivada correctamente.";

        return to_route('admin.sucursales.index')->with('success', $mensaje);
    }
}
