<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\EmpleadoRequest;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\Mecanico;
use App\Models\Rol;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmpleadoController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Empleado::query()->with(['sucursal', 'user', 'rol']);

        $this->scopeSucursal($query, 'sucursal_id');
        $this->aplicarFiltros($request, $query, ['estado', 'sucursal_id', 'rol_id']);
        $this->aplicarBusqueda($query, $request, [
            'nombre_completo',
            'ci',
            'cargo',
            'telefono',
            'email',
        ]);

        $empleados = $query->orderBy('nombre_completo')->paginate(15)->withQueryString();

        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        $roles = Rol::where('estado', true)->orderBy('nombre')->get();

        return view('admin.empleados.index', [
            'empleados' => $empleados,
            'sucursales' => $sucursales,
            'roles' => $roles,
        ]);
    }

    public function create(): View
    {
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        $roles = Rol::where('estado', true)->orderBy('nombre')->get();
        $especialidades = Especialidad::orderBy('nombre')->get();

        return view('admin.empleados.create', [
            'empleado' => new \App\Models\Empleado(),
            'sucursales' => $sucursales,
            'roles' => $roles,
            'especialidades' => $especialidades,
        ]);
    }

    public function store(EmpleadoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);

        $rol = Rol::findOrFail($datos['rol_id']);
        $esMecanico = strcasecmp($rol->nombre, 'Mecánico') === 0;

        try {
            DB::transaction(function () use ($datos, $esMecanico, $request) {
                $empleado = Empleado::create($datos);

                if ($esMecanico) {
                    Mecanico::create([
                        'empleado_id' => $empleado->id,
                        'especialidad_id' => $datos['especialidad_id'],
                        'disponibilidad' => $datos['disponibilidad'] ?? 'disponible',
                        'observaciones' => $datos['observaciones_mecanico'] ?? null,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error al registrar el empleado: '.$e->getMessage());
        }

        return $this->redirigirConExito('empleados', 'registrado');
    }

    public function show(Empleado $empleado): View
    {
        $empleado->load(['sucursal', 'user.rol', 'mecanico.especialidad', 'rol']);

        return view('admin.empleados.show', [
            'empleado' => $empleado,
        ]);
    }

    public function edit(Empleado $empleado): View
    {
        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        $roles = Rol::where('estado', true)->orderBy('nombre')->get();
        $especialidades = Especialidad::orderBy('nombre')->get();
        $empleado->load('mecanico');

        return view('admin.empleados.edit', [
            'empleado' => $empleado,
            'sucursales' => $sucursales,
            'roles' => $roles,
            'especialidades' => $especialidades,
        ]);
    }

    public function update(EmpleadoRequest $request, Empleado $empleado): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $rol = Rol::findOrFail($datos['rol_id']);
        $esMecanico = strcasecmp($rol->nombre, 'Mecánico') === 0;

        try {
            DB::transaction(function () use ($empleado, $datos, $esMecanico) {
                $empleado->update($datos);

                if ($esMecanico) {
                    $mecanicoExistente = Mecanico::withTrashed()
                        ->where('empleado_id', $empleado->id)
                        ->first();

                    if ($mecanicoExistente) {
                        if ($mecanicoExistente->trashed()) {
                            $mecanicoExistente->restore();
                        }
                        $mecanicoExistente->update([
                            'especialidad_id' => $datos['especialidad_id'],
                            'disponibilidad' => $datos['disponibilidad'] ?? 'disponible',
                            'observaciones' => $datos['observaciones_mecanico'] ?? null,
                        ]);
                    } else {
                        Mecanico::create([
                            'empleado_id' => $empleado->id,
                            'especialidad_id' => $datos['especialidad_id'],
                            'disponibilidad' => $datos['disponibilidad'] ?? 'disponible',
                            'observaciones' => $datos['observaciones_mecanico'] ?? null,
                        ]);
                    }
                } else {
                    $mecanicoExistente = Mecanico::withTrashed()
                        ->where('empleado_id', $empleado->id)
                        ->first();
                    if ($mecanicoExistente) {
                        $mecanicoExistente->forceDelete();
                    }
                }
            });
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error al actualizar el empleado: '.$e->getMessage());
        }

        return $this->redirigirConExito('empleados', 'actualizado');
    }

    public function destroy(Empleado $empleado): RedirectResponse
    {
        $empleado->estado = false;
        $empleado->save();

        if ($empleado->user()->exists()) {
            $empleado->user->estado = 'inactivo';
            $empleado->user->save();
        }

        return back()->with('success', 'El empleado fue dado de baja correctamente. Su cuenta de acceso fue desactivada.');
    }

    public function toggle(Request $request, Empleado $empleado): RedirectResponse
    {
        $nuevoEstado = !$empleado->estado;
        $empleado->estado = $nuevoEstado;
        $empleado->save();

        if ($empleado->user()->exists()) {
            $empleado->user->estado = $nuevoEstado ? 'activo' : 'inactivo';
            $empleado->user->save();
        }

        $texto = $nuevoEstado ? 'activado' : 'desactivado';
        $usuarioTexto = '';
        if ($empleado->user()->exists()) {
            $usuarioTexto = " Su cuenta de acceso fue {$texto}.";
        }

        return back()->with('success', "El empleado fue {$texto} correctamente.{$usuarioTexto}");
    }
}
