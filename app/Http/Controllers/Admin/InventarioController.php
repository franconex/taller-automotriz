<?php

namespace App\Http\Controllers\Admin;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\Sucursal;
use App\Http\Requests\Admin\EntradaInventarioRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class InventarioController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:inventario.ajustar', only: ['edit', 'update', 'toggle']),
            new Middleware('permiso:roles.editar', only: ['destroy']),
        ];
    }
    public function index(Request $request): View
    {
        $query = Repuesto::query()
            ->select('repuestos.*')
            ->with(['inventarios' => fn ($q) => $q->select('repuesto_id', 'cantidad_actual', 'cantidad_reservada')])
            ->where('estado', true);

        $this->aplicarFiltros($request, $query, ['tipo']);
        $this->aplicarBusqueda($query, $request, ['nombre', 'codigo', 'codigo_barras', 'marca']);

        if ($request->filled('stock')) {
            if ($request->stock === 'bajo') {
                $query->whereRaw('(SELECT COALESCE(SUM(cantidad_actual), 0) - COALESCE(SUM(cantidad_reservada), 0) FROM inventarios WHERE repuesto_id = repuestos.id) < 5');
            } elseif ($request->stock === 'normal') {
                $query->whereRaw('(SELECT COALESCE(SUM(cantidad_actual), 0) - COALESCE(SUM(cantidad_reservada), 0) FROM inventarios WHERE repuesto_id = repuestos.id) >= 5');
            }
        }

        $productos = $query->orderBy('nombre')->paginate(20)->withQueryString();

        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        $proveedores = Proveedor::where('estado', true)->orderBy('nombre_empresa')->get();

        $categorias = Repuesto::whereNotNull('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        $marcas = Repuesto::whereNotNull('marca')
            ->distinct()
            ->orderBy('marca')
            ->pluck('marca');

        return view('admin.inventario.index', [
            'productos' => $productos,
            'sucursales' => $sucursales,
            'proveedores' => $proveedores,
            'categorias' => $categorias,
            'marcas' => $marcas,
        ]);
    }

    public function entradaRapida(Request $request): RedirectResponse
    {
        $request->validate([
            'repuesto_id' => ['required', 'exists:repuestos,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($request) {
            $inventario = Inventario::firstOrCreate(
                ['repuesto_id' => $request->repuesto_id],
                ['cantidad_actual' => 0, 'cantidad_reservada' => 0, 'fecha_actualizacion' => now()]
            );

            $cantidad = (int) $request->cantidad;
            $anterior = (int) $inventario->cantidad_actual;
            $inventario->update(['cantidad_actual' => $anterior + $cantidad, 'fecha_actualizacion' => now()]);

            MovimientoInventario::create([
                'inventario_id' => $inventario->id,
                'usuario_id' => Auth::id(),
                'tipo' => 'entrada_compra',
                'cantidad' => $cantidad,
                'existencia_anterior' => $anterior,
                'existencia_nueva' => $anterior + $cantidad,
                'motivo' => $request->motivo ?? 'Entrada de stock',
                'fecha_movimiento' => now(),
            ]);
        });

        return back()->with('success', "+{$request->cantidad} unidades registradas.");
    }

    public function registrarEntrada(EntradaInventarioRequest $request)
    {
        $datos = $request->validated();
        $datos['sucursal_id'] = $datos['sucursal_id'] ?? $this->usuarioSucursalId();

        $movimiento = DB::transaction(function () use ($datos) {
            $inventario = Inventario::firstOrCreate(
                [
                    'sucursal_id' => $datos['sucursal_id'],
                    'repuesto_id' => $datos['repuesto_id'],
                ],
                [
                    'cantidad_actual' => 0,
                    'cantidad_reservada' => 0,
                    'fecha_actualizacion' => now(),
                ]
            );

            $cantidad = (int) $datos['cantidad'];
            $anterior = (int) $inventario->cantidad_actual;
            $nuevoStock = $anterior + $cantidad;

            $inventario->cantidad_actual = $nuevoStock;
            $inventario->fecha_actualizacion = now();

            if (($datos['precio_unitario'] ?? 0) > 0) {
                $precioNuevo = (float) $datos['precio_unitario'];
                $costoActual = (float) ($inventario->costo_promedio ?? 0);

                if ($costoActual == 0) {
                    $inventario->costo_promedio = $precioNuevo;
                } else {
                    $inventario->costo_promedio = (($costoActual * $anterior) + ($precioNuevo * $cantidad)) / max(1, $nuevoStock);
                }
            }

            $inventario->save();

            $mov = MovimientoInventario::create([
                'inventario_id' => $inventario->id,
                'usuario_id' => Auth::id(),
                'tipo' => 'entrada_compra',
                'cantidad' => $cantidad,
                'existencia_anterior' => $anterior,
                'existencia_nueva' => $nuevoStock,
                'motivo' => $datos['observaciones'] ?? 'Entrada por escáner',
                'fecha_movimiento' => now(),
            ]);

            return $mov;
        });

        $repuesto = Repuesto::find($datos['repuesto_id']);

        return response()->json([
            'exito' => true,
            'mensaje' => "+{$datos['cantidad']} unidades de {$repuesto?->nombre} registradas.",
            'stock_anterior' => (int) $movimiento->existencia_anterior,
            'stock_nuevo' => (int) $movimiento->existencia_nueva,
            'repuesto' => [
                'id' => $repuesto?->id,
                'nombre' => $repuesto?->nombre,
                'stock_total' => $repuesto?->inventarios()->sum('cantidad_actual') ?? 0,
            ],
        ]);
    }

    public function crearDesdeEscaner(Request $request): RedirectResponse
    {
        $request->validate([
            'codigo_barras' => ['nullable', 'string', 'max:50'],
            'codigo_fabricante' => ['nullable', 'string', 'max:50'],
            'nombre' => ['required', 'string', 'max:150'],
            'tipo' => ['required', 'in:repuesto,herramienta'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'marca' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'costo_compra' => ['nullable', 'numeric', 'min:0'],
            'precio_venta' => ['nullable', 'numeric', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:0'],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'sucursal_id' => ['nullable', 'exists:sucursales,id'],
        ]);

        $producto = DB::transaction(function () use ($request) {
            $codigo = $request->codigo_barras;
            $prefijo = $request->tipo === 'herramienta' ? 'HERR-' : 'REP-';

            $r = Repuesto::create([
                'codigo' => $prefijo . strtoupper(substr(md5(uniqid()), 0, 8)),
                'codigo_barras' => $codigo,
                'codigo_fabricante' => $request->codigo_fabricante,
                'tipo' => $request->tipo,
                'nombre' => $request->nombre,
                'categoria' => $request->categoria,
                'marca' => $request->marca,
                'descripcion' => $request->descripcion,
                'costo_compra' => $request->costo_compra ?? 0,
                'precio_venta' => $request->precio_venta ?? 0,
                'stock_minimo' => $request->stock_minimo ?? 0,
                'proveedor_id' => $request->proveedor_id,
                'estado' => true,
            ]);

            $esHerramienta = $request->tipo === 'herramienta';
            $cantidad = (int) $request->cantidad;
            if ($esHerramienta && $cantidad === 0) $cantidad = 1;
            $sucursalId = $request->sucursal_id ?? $this->usuarioSucursalId();

            if ($cantidad > 0) {
                $data = [
                    'repuesto_id' => $r->id,
                    'cantidad_actual' => $cantidad,
                    'cantidad_reservada' => 0,
                    'costo_promedio' => $request->costo_compra ?? 0,
                    'fecha_actualizacion' => now(),
                ];
                if ($sucursalId) {
                    $data['sucursal_id'] = $sucursalId;
                }

                $inv = Inventario::create($data);

                MovimientoInventario::create([
                    'inventario_id' => $inv->id,
                    'usuario_id' => Auth::id(),
                    'tipo' => 'entrada_inicial',
                    'cantidad' => $cantidad,
                    'existencia_anterior' => 0,
                    'existencia_nueva' => $cantidad,
                    'motivo' => $esHerramienta ? 'Registro de herramienta desde escáner' : 'Registro de repuesto desde escáner',
                    'fecha_movimiento' => now(),
                ]);
            }

            return $r;
        });

        return redirect()->route('admin.inventario.index')
            ->with('success', "{$producto->nombre} registrado con éxito.");
    }

    public function show(Inventario $inventario): View
    {
        $inventario->load(['repuesto', 'movimientos' => fn ($q) => $q->orderByDesc('fecha_movimiento')->limit(20)]);
        return view('admin.inventario.show', ['inventario' => $inventario]);
    }

    public function edit(Inventario $inventario): View
    {
        $inventario->load('repuesto');
        return view('admin.inventario.edit', ['inventario' => $inventario]);
    }

    public function update(Request $request, Inventario $inventario): RedirectResponse
    {
        $request->validate([
            'cantidad_reservada' => ['nullable', 'integer', 'min:0'],
            'costo_promedio' => ['nullable', 'numeric', 'min:0'],
        ]);
        $inventario->cantidad_reservada = (int) $request->input('cantidad_reservada', 0);
        $inventario->costo_promedio = $request->filled('costo_promedio') ? (float) $request->costo_promedio : null;
        $inventario->fecha_actualizacion = now();
        $inventario->save();
        return $this->redirigirConExito('inventario', 'actualizado');
    }

    public function destroy(Inventario $inventario): RedirectResponse
    {
        if ($inventario->cantidad_actual > 0) {
            return back()->with('error', 'No se puede eliminar porque tiene stock.');
        }
        $inventario->delete();
        return $this->redirigirConExito('inventario', 'eliminado');
    }
}
