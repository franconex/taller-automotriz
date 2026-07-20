<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetodoPago;
use App\Services\AuditService;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function index()
    {
        $metodos = MetodoPago::orderBy('nombre')->paginate(15);

        return view('admin.metodos-pago.index', compact('metodos'));
    }

    public function create()
    {
        return view('admin.metodos-pago.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:metodos_pago,nombre'],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'activo' => ['sometimes', 'boolean'],
        ]);

        $metodo = MetodoPago::create($data);

        $this->auditService->register(
            'crear',
            'MetodoPago',
            $metodo->id,
            null,
            $data,
            "Método de pago {$metodo->nombre} creado",
        );

        return to_route('admin.metodos-pago.index')
            ->with('success', "Método de pago {$metodo->nombre} creado correctamente.");
    }

    public function edit(MetodoPago $metodoPago)
    {
        return view('admin.metodos-pago.edit', compact('metodoPago'));
    }

    public function update(Request $request, MetodoPago $metodoPago)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:metodos_pago,nombre,'.$metodoPago->id],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'activo' => ['sometimes', 'boolean'],
        ]);

        $anterior = $metodoPago->only(['nombre', 'descripcion', 'activo']);
        $metodoPago->update($data);

        $this->auditService->register(
            'editar',
            'MetodoPago',
            $metodoPago->id,
            $anterior,
            $data,
            "Método de pago {$metodoPago->nombre} editado",
        );

        return to_route('admin.metodos-pago.index')
            ->with('success', "Método de pago {$metodoPago->nombre} actualizado correctamente.");
    }

    public function toggleEstado(MetodoPago $metodoPago)
    {
        $metodoPago->update(['activo' => ! $metodoPago->activo]);

        $accion = $metodoPago->activo ? 'activar' : 'desactivar';

        $this->auditService->register(
            $accion,
            'MetodoPago',
            $metodoPago->id,
            ['activo' => ! $metodoPago->activo],
            ['activo' => $metodoPago->activo],
            "Método de pago {$metodoPago->nombre} {$accion}do",
        );

        return to_route('admin.metodos-pago.index')->with(
            'success',
            ($metodoPago->activo ? 'Método de pago activado' : 'Método de pago desactivado').' correctamente.',
        );
    }
}
