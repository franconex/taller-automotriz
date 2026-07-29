<?php

namespace App\Http\Controllers\Admin;

use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\MetodoPago;
use App\Models\MovimientoInventario;
use App\Models\Pago;
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
            new Middleware('permiso:inventario.ver', only: ['index', 'show']),
            new Middleware('permiso:inventario.entrada', only: ['entradaRapida', 'registrarEntrada', 'crearDesdeEscaner']),
            new Middleware('permiso:inventario.ajustar', only: ['edit', 'update', 'toggle']),
            new Middleware('permiso:roles.editar', only: ['destroy']),
        ];
    }
    public function index(Request $request): View
    {
        $query = Repuesto::query()
            ->select('repuestos.*')
            ->with(['inventarios' => fn ($q) => $q->select('repuesto_id', 'cantidad_actual', 'cantidad_reservada')])
            ->with('categoria')
            ->where('estado', true);

        $this->aplicarFiltros($request, $query, ['tipo', 'categoria_id']);
        $this->aplicarBusqueda($query, $request, ['nombre', 'codigo', 'codigo_barras', 'marca', 'categoria', 'categoria.nombre']);

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

        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();
        $marcas = Repuesto::whereNotNull('marca')->distinct()->orderBy('marca')->pluck('marca');

        return view('admin.inventario.index', [
            'productos' => $productos,
            'sucursales' => $sucursales,
            'proveedores' => $proveedores,
            'categorias' => $categorias,
            'marcas' => $marcas,
        ]);
    }

    public function buscarSugerencias(Request $request)
    {
        $q = $request->input('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $productos = Repuesto::where('estado', true)
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                    ->orWhere('codigo', 'like', "%{$q}%")
                    ->orWhere('codigo_barras', 'like', "%{$q}%")
                    ->orWhere('marca', 'like', "%{$q}%");
            })
            ->with('categoria')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'text' => "{$r->nombre} ({$r->codigo})",
                'nombre' => $r->nombre,
                'codigo' => $r->codigo,
                'marca' => $r->marca,
                'categoria' => $r->categoria?->nombre,
                'stock' => (int) $r->inventarios()->sum('cantidad_actual'),
            ]);

        $categorias = Categoria::where('activo', true)
            ->where('nombre', 'like', "%{$q}%")
            ->limit(5)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'text' => "[Categoría] {$c->nombre}",
                'tipo' => 'categoria',
            ]);

        $sugerencias = collect();

        if ($categorias->isNotEmpty() && $productos->where('categoria', 'like', "%{$q}%")->isEmpty()) {
            $sugerencias = $sugerencias->merge($categorias);
        }

        $sugerencias = $sugerencias->merge($productos);

        return response()->json($sugerencias);
    }

    public function entradaRapida(Request $request): RedirectResponse
    {
        $request->validate([
            'repuesto_id' => ['required', 'exists:repuestos,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($request) {
            $inventario = Inventario::lockForUpdate()->firstOrCreate(
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

    public function crearDesdeEscaner(Request $request)
    {
        $request->validate([
            'codigo_barras' => ['nullable', 'string', 'max:50'],
            'codigo_fabricante' => ['nullable', 'string', 'max:50'],
            'nombre' => ['required', 'string', 'max:150'],
            'tipo' => ['required', 'in:repuesto,herramienta'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
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
                'categoria_id' => $request->categoria_id,
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
                if (!$sucursalId) {
                    $sucursalId = Sucursal::value('id');
                }

                $inv = Inventario::create([
                    'repuesto_id' => $r->id,
                    'sucursal_id' => $sucursalId,
                    'cantidad_actual' => $cantidad,
                    'cantidad_reservada' => 0,
                    'costo_promedio' => $request->costo_compra ?? 0,
                    'fecha_actualizacion' => now(),
                ]);

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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'exito' => true,
                'mensaje' => "{$producto->nombre} registrado con éxito.",
            ]);
        }

        return redirect()->route('admin.inventario.index')
            ->with('success', "{$producto->nombre} registrado con éxito.");
    }

    public function vender(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'repuesto_id' => ['required', 'exists:repuestos,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'precio_venta' => ['nullable', 'numeric', 'min:0'],
            'metodo_pago_id' => ['nullable', 'exists:metodos_pago,id'],
            'sucursal_id' => ['nullable', 'exists:sucursales,id'],
        ]);

        try {
            DB::transaction(function () use ($request) {
                $repuesto = Repuesto::findOrFail($request->repuesto_id);
                $cantidad = (int) $request->cantidad;
                $sucursalId = $request->sucursal_id ?? $this->usuarioSucursalId();

                $inventario = Inventario::where('repuesto_id', $repuesto->id)
                    ->where('sucursal_id', $sucursalId)
                    ->lockForUpdate()
                    ->first();

                if (!$inventario || $inventario->cantidad_actual - $inventario->cantidad_reservada < $cantidad) {
                    throw new \RuntimeException('Stock insuficiente para la venta.');
                }

                $anterior = (int) $inventario->cantidad_actual;
                $nueva = $anterior - $cantidad;
                $inventario->cantidad_actual = $nueva;
                $inventario->fecha_actualizacion = now();
                $inventario->save();

                MovimientoInventario::create([
                    'inventario_id' => $inventario->id,
                    'usuario_id' => Auth::id(),
                    'tipo' => 'salida_venta',
                    'cantidad' => $cantidad,
                    'existencia_anterior' => $anterior,
                    'existencia_nueva' => $nueva,
                    'motivo' => 'Venta directa: ' . $repuesto->nombre,
                    'fecha_movimiento' => now(),
                ]);

                if ($request->metodo_pago_id && $request->precio_venta > 0) {
                    Pago::create([
                        'orden_trabajo_id' => null,
                        'metodo_pago_id' => $request->metodo_pago_id,
                        'monto' => $request->precio_venta * $cantidad,
                        'estado' => 'confirmado',
                        'usuario_id' => Auth::id(),
                        'fecha_pago' => now(),
                        'descripcion' => 'Venta directa: ' . $repuesto->nombre . ' x' . $cantidad,
                    ]);
                }
            });

            return response()->json(['exito' => true, 'mensaje' => 'Venta registrada correctamente.']);
        } catch (\RuntimeException $e) {
            return response()->json(['exito' => false, 'mensaje' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return response()->json(['exito' => false, 'mensaje' => 'Error al procesar la venta.'], 500);
        }
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
