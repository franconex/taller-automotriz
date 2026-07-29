<?php

namespace App\Http\Controllers\Admin;

use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\Subservicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SubservicioController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:subservicios.ver', only: ['index', 'show']),
            new Middleware('permiso:subservicios.crear', only: ['create', 'store']),
            new Middleware('permiso:subservicios.editar', only: ['edit', 'update', 'destroy', 'toggle']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Subservicio::with('servicio.tipoServicio');

        if ($request->filled('servicio_id')) {
            $query->where('servicio_id', $request->servicio_id);
        }

        $subservicios = $query->orderBy('nombre')->paginate(20);
        $servicios = Servicio::where('estado', true)->orderBy('nombre')->get();

        return view('admin.subservicios.index', compact('subservicios', 'servicios'));
    }

    public function create(): View
    {
        $servicios = Servicio::where('estado', true)->orderBy('nombre')->get();
        $repuestos = Repuesto::where('estado', true)->orderBy('nombre')->get();

        return view('admin.subservicios.create', compact('servicios', 'repuestos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'servicio_id' => ['required', 'exists:servicios,id'],
            'nombre' => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'duracion_estimada_minutos' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'requiere_diagnostico' => ['boolean'],
            'repuestos' => ['nullable', 'array'],
            'repuestos.*.repuesto_id' => ['exists:repuestos,id'],
            'repuestos.*.cantidad' => ['numeric', 'min:0.01'],
        ]);

        $sub = Subservicio::create([
            'servicio_id' => $data['servicio_id'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'precio_base' => $data['precio_base'],
            'duracion_estimada_minutos' => $data['duracion_estimada_minutos'] ?? null,
            'requiere_diagnostico' => $request->boolean('requiere_diagnostico'),
            'estado' => true,
        ]);

        if (! empty($data['repuestos'])) {
            foreach ($data['repuestos'] as $r) {
                $sub->repuestos()->attach($r['repuesto_id'], ['cantidad_sugerida' => $r['cantidad'] ?? 1]);
            }
        }

        return redirect()->route('admin.subservicios.index')
            ->with('success', 'Subservicio creado correctamente.');
    }

    public function edit(Subservicio $subservicio): View
    {
        $servicios = Servicio::where('estado', true)->orderBy('nombre')->get();
        $repuestos = Repuesto::where('estado', true)->orderBy('nombre')->get();
        $subservicio->load('repuestos');

        return view('admin.subservicios.edit', compact('subservicio', 'servicios', 'repuestos'));
    }

    public function update(Request $request, Subservicio $subservicio): RedirectResponse
    {
        $data = $request->validate([
            'servicio_id' => ['required', 'exists:servicios,id'],
            'nombre' => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'duracion_estimada_minutos' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'requiere_diagnostico' => ['boolean'],
            'repuestos' => ['nullable', 'array'],
            'repuestos.*.repuesto_id' => ['exists:repuestos,id'],
            'repuestos.*.cantidad' => ['numeric', 'min:0.01'],
        ]);

        $subservicio->update([
            'servicio_id' => $data['servicio_id'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'precio_base' => $data['precio_base'],
            'duracion_estimada_minutos' => $data['duracion_estimada_minutos'] ?? null,
            'requiere_diagnostico' => $request->boolean('requiere_diagnostico'),
        ]);

        if (! empty($data['repuestos'])) {
            $sync = [];
            foreach ($data['repuestos'] as $r) {
                $sync[$r['repuesto_id']] = ['cantidad_sugerida' => $r['cantidad'] ?? 1];
            }
            $subservicio->repuestos()->sync($sync);
        } else {
            $subservicio->repuestos()->detach();
        }

        return redirect()->route('admin.subservicios.index')
            ->with('success', 'Subservicio actualizado correctamente.');
    }

    public function toggle(Subservicio $subservicio): RedirectResponse
    {
        $subservicio->update(['estado' => ! $subservicio->estado]);
        return back()->with('success', 'Estado actualizado.');
    }

    // JSON endpoint para selectores dependientes
    public function porServicio(Servicio $servicio)
    {
        $subservicios = $servicio->subservicios()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'precio_base', 'duracion_estimada_minutos']);

        return response()->json($subservicios);
    }

    public function conRepuestos(Subservicio $subservicio)
    {
        $subservicio->load(['repuestos' => fn ($q) => $q->where('estado, true')]);

        return response()->json([
            'subservicio' => $subservicio,
            'repuestos' => $subservicio->repuestos->map(fn ($r) => [
                'id' => $r->id,
                'nombre' => $r->nombre,
                'codigo' => $r->codigo,
                'cantidad_sugerida' => $r->pivot->cantidad_sugerida,
            ]),
        ]);
    }
}
