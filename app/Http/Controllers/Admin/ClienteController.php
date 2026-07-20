<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClienteRequest;
use App\Http\Requests\Admin\UpdateClienteRequest;
use App\Models\Cliente;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('ci', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === 'activo');
        }

        $clientes = $query->orderByDesc('id')->paginate(15);

        return view('admin.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('admin.clientes.create');
    }

    public function store(StoreClienteRequest $request)
    {
        $data = $request->validated();
        $data['creado_por'] = auth()->id();

        $cliente = Cliente::create($data);

        $this->auditService->register(
            'crear',
            'Cliente',
            $cliente->id,
            null,
            $request->safe()->toArray(),
            "Cliente {$cliente->nombre} {$cliente->apellido} creado",
        );

        return to_route('admin.clientes.index')
            ->with('success', "Cliente {$cliente->nombre} {$cliente->apellido} creado correctamente.");
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('creador:id,nombre');

        return view('admin.clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('admin.clientes.edit', compact('cliente'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $anterior = $cliente->only(['nombre', 'apellido', 'ci', 'telefono', 'estado']);
        $data = $request->validated();
        $data['actualizado_por'] = auth()->id();

        $cliente->update($data);

        $this->auditService->register(
            'editar',
            'Cliente',
            $cliente->id,
            $anterior,
            $request->safe()->toArray(),
            "Cliente {$cliente->nombre} {$cliente->apellido} editado",
        );

        return to_route('admin.clientes.index')
            ->with('success', "Cliente {$cliente->nombre} {$cliente->apellido} actualizado correctamente.");
    }

    public function toggleEstado(Cliente $cliente)
    {
        $cliente->update(['estado' => ! $cliente->estado]);

        $accion = $cliente->estado ? 'activar' : 'desactivar';

        $this->auditService->register(
            $accion,
            'Cliente',
            $cliente->id,
            ['estado' => ! $cliente->estado],
            ['estado' => $cliente->estado],
            "Cliente {$cliente->nombre} {$cliente->apellido} {$accion}do",
        );

        return to_route('admin.clientes.index')->with(
            'success',
            ($cliente->estado ? 'Cliente activado' : 'Cliente desactivado').' correctamente.',
        );
    }
}
