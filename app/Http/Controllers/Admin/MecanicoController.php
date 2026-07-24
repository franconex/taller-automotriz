<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MecanicoRequest;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\Mecanico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MecanicoController extends AdminController
{
    public function index(Request $request): View
    {
        $query = Mecanico::query()
            ->with(['empleado.sucursal', 'especialidad']);

        if ($sucursalId = $this->usuarioSucursalId()) {
            $query->whereHas('empleado', fn ($q) => $q->where('sucursal_id', $sucursalId));
        }

        $this->aplicarFiltros($request, $query, ['disponibilidad']);
        $this->aplicarBusqueda($query, $request, [
            'empleado.nombre_completo',
            'especialidad.nombre',
        ]);

        $mecanicos = $query->orderBy('disponibilidad')->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.mecanicos.index', [
            'mecanicos' => $mecanicos,
        ]);
    }

    public function create(): View
    {
        $empleados = Empleado::query()
            ->whereDoesntHave('mecanico')
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->orderBy('nombre_completo')
            ->get();
        $especialidades = Especialidad::orderBy('nombre')->get();

        return view('admin.mecanicos.create', [
            'empleados' => $empleados,
            'especialidades' => $especialidades,
            'mecanico' => new \App\Models\Mecanico(),
        ]);
    }

    public function store(MecanicoRequest $request): RedirectResponse
    {
        Mecanico::create($request->validated());

        return $this->redirigirConExito('mecánicos', 'registrado');
    }

    public function show(Mecanico $mecanico): View
    {
        $mecanico->load(['empleado.sucursal', 'especialidad', 'asignaciones.ordenTrabajo']);

        return view('admin.mecanicos.show', [
            'mecanico' => $mecanico,
        ]);
    }

    public function edit(Mecanico $mecanico): View
    {
        $empleados = Empleado::query()
            ->where(function ($q) use ($mecanico) {
                $q->whereDoesntHave('mecanico')
                  ->orWhere('id', $mecanico->empleado_id);
            })
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
            ->orderBy('nombre_completo')
            ->get();
        $especialidades = Especialidad::orderBy('nombre')->get();

        return view('admin.mecanicos.edit', [
            'mecanico' => $mecanico,
            'empleados' => $empleados,
            'especialidades' => $especialidades,
        ]);
    }

    public function update(MecanicoRequest $request, Mecanico $mecanico): RedirectResponse
    {
        $mecanico->update($request->validated());

        return $this->redirigirConExito('mecánicos', 'actualizado');
    }

    public function destroy(Mecanico $mecanico): RedirectResponse
    {
        if ($mecanico->asignaciones()->exists()) {
            return back()->with('error', 'No se puede eliminar el mecánico porque tiene asignaciones registradas.');
        }

        $mecanico->delete();

        return $this->redirigirConExito('mecánicos', 'eliminado');
    }

    public function toggle(Request $request, Mecanico $mecanico): RedirectResponse
    {
        $estados = ['disponible', 'ocupado', 'ausente'];
        $actual = array_search($mecanico->disponibilidad, $estados);
        $nuevo = $estados[($actual + 1) % count($estados)];
        $mecanico->disponibilidad = $nuevo;
        $mecanico->save();

        return back()->with('success', "El mecánico fue marcado como {$nuevo}.");
    }
}
