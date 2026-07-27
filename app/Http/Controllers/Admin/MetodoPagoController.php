<?php

namespace App\Http\Controllers\Admin;

use App\Models\MetodoPago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class MetodoPagoController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:metodos-pago.ver', only: ['index', 'show']),
            new Middleware('permiso:metodos-pago.editar', only: ['edit', 'update', 'destroy', 'toggle']),
        ];
    }
    private const FIJOS = ['Efectivo', 'QR', 'Tarjeta'];

    public function index(Request $request): View
    {
        $query = MetodoPago::query()->withCount('pagos');

        $this->aplicarFiltros($request, $query, ['estado']);
        $this->aplicarBusqueda($query, $request, ['nombre', 'descripcion']);

        $metodos = $query->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('admin.metodos-pago.index', [
            'metodos' => $metodos,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.metodos-pago.index')
            ->with('info', 'No es posible agregar nuevos métodos de pago. Solo están disponibles Efectivo, Tarjeta y QR.');
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('admin.metodos-pago.index')
            ->with('info', 'No es posible agregar nuevos métodos de pago.');
    }

    public function show(MetodoPago $metodoPago): View
    {
        $metodoPago->loadCount('pagos');

        return view('admin.metodos-pago.show', [
            'metodo' => $metodoPago,
        ]);
    }

    public function edit(MetodoPago $metodoPago): RedirectResponse|View
    {
        if ($this->esFijo($metodoPago, 'Efectivo')) {
            return redirect()->route('admin.metodos-pago.index')
                ->with('info', 'El método de pago Efectivo no se puede editar.');
        }

        return view('admin.metodos-pago.edit', [
            'metodo' => $metodoPago,
        ]);
    }

    public function update(Request $request, MetodoPago $metodoPago): RedirectResponse
    {
        if ($this->esFijo($metodoPago, 'Efectivo')) {
            return back()->with('error', 'El método de pago Efectivo no se puede modificar.');
        }

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::unique('metodos_pago', 'nombre')->ignore($metodoPago->id)],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'boolean'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
            'unique' => 'El :attribute ya está registrado.',
            'boolean' => 'El campo :attribute debe ser verdadero o falso.',
        ]);

        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        $metodoPago->update($datos);

        return $this->redirigirConExito('métodos de pago', 'actualizado');
    }

    public function destroy(): RedirectResponse
    {
        return back()->with('error', 'No se puede eliminar métodos de pago del sistema.');
    }

    public function toggle(Request $request, MetodoPago $metodoPago): RedirectResponse
    {
        if ($this->esFijo($metodoPago, 'Efectivo')) {
            return back()->with('error', 'El método de pago Efectivo no se puede desactivar.');
        }

        return $this->cambiarEstado($request, $metodoPago, 'métodos de pago');
    }

    private function esFijo(MetodoPago $metodoPago, string $nombre): bool
    {
        return strcasecmp($metodoPago->nombre, $nombre) === 0;
    }
}
