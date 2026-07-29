<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\EscanerBuscarRequest;
use App\Http\Requests\Admin\RepuestoRequest;
use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Repuesto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class RepuestoController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:repuestos.ver', only: ['index', 'buscarPorEscaner']),
            new Middleware('permiso:repuestos.crear', only: ['create', 'store']),
            new Middleware('permiso:repuestos.editar', only: ['edit', 'update', 'destroy']),
        ];
    }
    public function index(Request $request): View
    {
        $query = Repuesto::query()->with('proveedor');

        $this->aplicarFiltros($request, $query, ['estado', 'proveedor_id']);
        $this->aplicarBusqueda($query, $request, [
            'codigo',
            'nombre',
            'descripcion',
        ]);

        $repuestos = $query->orderBy('nombre')->paginate(15)->withQueryString();

        $proveedores = Proveedor::orderBy('nombre_empresa')->get();

        return view('admin.repuestos.index', [
            'repuestos' => $repuestos,
            'proveedores' => $proveedores,
        ]);
    }
    public function create(): View
    {
        return view('admin.repuestos.create', [
            'categorias' => Categoria::where('activo', true)->orderBy('nombre')->get(),
            'repuesto' => new \App\Models\Repuesto(),
        ]);
    }

    public function store(RepuestoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? true);

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto = DB::transaction(function () use ($datos, $request) {
            $r = Repuesto::create($datos);

            $cantidadInicial = $r->tipo === 'herramienta' ? (int) $request->input('cantidad_inicial', 1) : 0;

            $inv = Inventario::firstOrCreate(
                ['repuesto_id' => $r->id],
                [
                    'cantidad_actual' => $cantidadInicial,
                    'cantidad_reservada' => 0,
                    'costo_promedio' => $datos['costo_compra'] ?? 0,
                    'fecha_actualizacion' => now(),
                ]
            );

            if ($cantidadInicial > 0) {
                MovimientoInventario::create([
                    'inventario_id' => $inv->id,
                    'usuario_id' => Auth::id(),
                    'tipo' => 'entrada_inicial',
                    'cantidad' => $cantidadInicial,
                    'existencia_anterior' => 0,
                    'existencia_nueva' => $cantidadInicial,
                    'motivo' => $r->tipo === 'herramienta' ? 'Registro inicial de herramienta' : 'Registro inicial de repuesto',
                    'fecha_movimiento' => now(),
                ]);
            }

            return $r;
        });

        return redirect()->route('admin.inventario.index')->with('success', "{$producto->nombre} creado con éxito.");
    }

    public function buscarPorEscaner(EscanerBuscarRequest $request)
    {
        $codigo = trim($request->input('codigo'));

        $repuesto = Repuesto::where('codigo_barras', $codigo)
            ->orWhere('codigo', $codigo)
            ->orWhere('codigo_fabricante', $codigo)
            ->with(['inventarios' => fn ($q) => $q->select('repuesto_id', 'sucursal_id', 'cantidad_actual', 'cantidad_reservada')])
            ->first();

        if (! $repuesto) {
            return response()->json([
                'encontrado' => false,
                'codigo' => $codigo,
                'mensaje' => 'El código no está registrado.',
            ]);
        }

        $stockTotal = (int) $repuesto->inventarios->sum('cantidad_actual');
        $stockReservado = (int) $repuesto->inventarios->sum('cantidad_reservada');
        $disponible = $stockTotal - $stockReservado;

        return response()->json([
            'encontrado' => true,
            'codigo' => $codigo,
            'repuesto' => [
                'id' => $repuesto->id,
                'codigo_interno' => $repuesto->codigo,
                'codigo_barras' => $repuesto->codigo_barras,
                'codigo_fabricante' => $repuesto->codigo_fabricante,
                'nombre' => $repuesto->nombre,
                'marca' => $repuesto->marca,
                'categoria' => $repuesto->categoria,
                'descripcion' => $repuesto->descripcion,
                'stock_total' => $stockTotal,
                'stock_reservado' => $stockReservado,
                'stock_disponible' => $disponible,
                'precio_venta' => (float) ($repuesto->precio_venta ?? 0),
                'costo_compra' => auth()->user()?->tienePermiso('precios.ver')
                    ? (float) ($repuesto->costo_compra ?? 0)
                    : null,
                'tipo' => $repuesto->tipo,
                'estado' => $repuesto->estado,
            ],
        ]);
    }

    public function edit(Repuesto $repuesto): View
    {
        return view('admin.repuestos.edit', [
            'repuesto' => $repuesto,
            'categorias' => Categoria::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function update(RepuestoRequest $request, Repuesto $repuesto): RedirectResponse
    {
        $datos = $request->validated();
        $datos['estado'] = (bool) ($datos['estado'] ?? false);

        if ($request->boolean('eliminar_imagen') && $repuesto->imagen) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($repuesto->imagen);
            $datos['imagen'] = null;
        }

        if ($request->hasFile('imagen')) {
            if ($repuesto->imagen) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($repuesto->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $repuesto->update($datos);
        return redirect()->route('admin.inventario.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Repuesto $repuesto): RedirectResponse
    {
        if ($repuesto->inventarios()->where('cantidad_actual', '>', 0)->exists()) {
            return back()->with('error', 'No se puede eliminar porque tiene stock.');
        }
        $repuesto->delete();
        return back()->with('success', 'Producto eliminado.');
    }
}
