<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmpleadoRequest;
use App\Http\Requests\Admin\UpdateEmpleadoRequest;
use App\Models\empleado;
use App\Models\Rol;
use App\Models\sucursal;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function index(Request $request)
    {
        $query = empleado::with('user:id,nombre,email,estado,rol_id', 'user.rol:id,nombre', 'sucursal:id,nombre');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('ci', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === 'activo');
        }

        $empleados = $query->orderByDesc('id')->paginate(15);
        $sucursales = sucursal::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);
        $roles = Rol::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.empleados.index', compact('empleados', 'sucursales', 'roles'));
    }

    public function create()
    {
        $sucursales = sucursal::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);
        $roles = Rol::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.empleados.create', compact('sucursales', 'roles'));
    }

    public function store(StoreEmpleadoRequest $request)
    {
        $data = $request->validated();

        $empleado = DB::transaction(function () use ($data) {
            if (! empty($data['crear_usuario'])) {
                $user = User::create([
                    'rol_id' => $data['rol_id'],
                    'nombre' => $data['nombre'].' '.$data['apellido'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'estado' => true,
                    'debe_cambiar_password' => true,
                ]);

                $data['user_id'] = $user->id;
            }

            unset($data['crear_usuario'], $data['username'], $data['email'], $data['password'], $data['password_confirmation'], $data['rol_id']);

            return empleado::create($data);
        });

        $this->auditService->register(
            'crear',
            'Empleado',
            $empleado->id,
            null,
            $request->safe()->except(['password', 'password_confirmation']),
            "Empleado {$empleado->nombre} {$empleado->apellido} creado",
        );

        return to_route('admin.empleados.index')
            ->with('success', "Empleado {$empleado->nombre} {$empleado->apellido} creado correctamente.");
    }

    public function show(empleado $empleado)
    {
        $empleado->load('user:id,nombre,email,username,estado,ultimo_acceso,rol_id', 'user.rol:id,nombre', 'sucursal:id,nombre');

        return view('admin.empleados.show', compact('empleado'));
    }

    public function edit(empleado $empleado)
    {
        $sucursales = sucursal::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);
        $roles = Rol::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.empleados.edit', compact('empleado', 'sucursales', 'roles'));
    }

    public function update(UpdateEmpleadoRequest $request, empleado $empleado)
    {
        $anterior = $empleado->only(['nombre', 'apellido', 'ci', 'sucursal_id', 'cargo', 'estado']);
        $empleado->update($request->validated());

        $this->auditService->register(
            'editar',
            'Empleado',
            $empleado->id,
            $anterior,
            $request->safe()->toArray(),
            "Empleado {$empleado->nombre} {$empleado->apellido} editado",
        );

        return to_route('admin.empleados.index')
            ->with('success', "Empleado {$empleado->nombre} {$empleado->apellido} actualizado correctamente.");
    }

    public function toggleEstado(empleado $empleado)
    {
        $empleado->update(['estado' => ! $empleado->estado]);

        $accion = $empleado->estado ? 'activar' : 'desactivar';

        $this->auditService->register(
            $accion,
            'Empleado',
            $empleado->id,
            ['estado' => ! $empleado->estado],
            ['estado' => $empleado->estado],
            "Empleado {$empleado->nombre} {$empleado->apellido} {$accion}do",
        );

        $mensaje = $empleado->estado
            ? "Empleado {$empleado->nombre} {$empleado->apellido} activado correctamente."
            : "Empleado {$empleado->nombre} {$empleado->apellido} desactivado correctamente.";

        return to_route('admin.empleados.index')->with('success', $mensaje);
    }

    public function cambiarRol(Request $request, empleado $empleado)
    {
        $request->validate([
            'rol_id' => ['required', 'exists:roles,id,estado,1'],
        ]);

        $user = $empleado->user;
        if (! $user) {
            return back()->with('error', 'Este empleado no tiene una cuenta de usuario asociada.');
        }

        $anterior = ['rol_id' => $user->rol_id];
        $user->update(['rol_id' => $request->rol_id]);
        $rolNuevo = Rol::find($request->rol_id);

        $this->auditService->register(
            'cambiar_rol',
            'Empleado',
            $empleado->id,
            $anterior,
            ['rol_id' => $request->rol_id],
            "Rol de {$empleado->nombre} {$empleado->apellido} cambiado a {$rolNuevo?->nombre}",
        );

        return to_route('admin.empleados.index')
            ->with('success', "Rol de {$empleado->nombre} {$empleado->apellido} actualizado correctamente.");
    }
}
