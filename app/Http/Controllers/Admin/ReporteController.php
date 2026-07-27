<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Mecanico;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Models\Repuesto;
use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class ReporteController extends AdminController
{
    public function index(): View
    {
        return view('admin.reportes.index');
    }

    public function mostrar(Request $request, string $tipo): View
    {
        $sucursalId = $this->usuarioSucursalId();
        $desde = $request->filled('desde') ? Carbon::parse($request->input('desde'))->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $hasta = $request->filled('hasta') ? Carbon::parse($request->input('hasta'))->endOfDay() : Carbon::now()->endOfDay();

        $datos = match ($tipo) {
            'ingresos' => $this->reporteIngresos($desde, $hasta, $sucursalId),
            'ordenes-estado' => $this->reporteOrdenesEstado($sucursalId),
            'mecanicos-productividad' => $this->reporteMecanicosProductividad($desde, $hasta, $sucursalId),
            'stock-critico' => $this->reporteStockCritico($sucursalId),
            'clientes-frecuentes' => $this->reporteClientesFrecuentes($desde, $hasta, $sucursalId),
            'servicios-mas-vendidos' => $this->reporteServiciosMasVendidos($desde, $hasta, $sucursalId),
            default => null,
        };

        if ($datos === null) {
            abort(404, 'Reporte no encontrado.');
        }

        return view('admin.reportes.mostrar', [
            'tipo' => $tipo,
            'datos' => $datos,
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
    }

    private function reporteIngresos(Carbon $desde, Carbon $hasta, ?int $sucursalId): array
    {
        $pagos = Pago::query()
            ->where('estado', 'confirmado')
            ->whereBetween('fecha_pago', [$desde, $hasta])
            ->when($sucursalId, fn ($q) => $q->whereHas('ordenTrabajo', fn ($q2) => $q2->where('sucursal_id', $sucursalId)))
            ->with('metodoPago')
            ->orderBy('fecha_pago')
            ->get();

        $porMetodo = $pagos->groupBy(fn ($p) => $p->metodoPago->nombre ?? 'Sin método')
            ->map(fn ($g) => ['cantidad' => $g->count(), 'total' => (float) $g->sum('monto')])
            ->sortByDesc('total');

        return [
            'titulo' => 'Ingresos por período',
            'descripcion' => 'Pagos confirmados entre ' . $desde->format('d/m/Y') . ' y ' . $hasta->format('d/m/Y') . '.',
            'total' => (float) $pagos->sum('monto'),
            'por_metodo' => $porMetodo,
            'pagos' => $pagos,
        ];
    }

    private function reporteOrdenesEstado(?int $sucursalId): array
    {
        $ordenes = OrdenTrabajo::query()
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->select('estado', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(total_general) as monto'))
            ->groupBy('estado')
            ->get();

        return [
            'titulo' => 'Órdenes por estado',
            'descripcion' => 'Distribución actual de las órdenes según su estado.',
            'por_estado' => $ordenes,
        ];
    }

    private function reporteMecanicosProductividad(Carbon $desde, Carbon $hasta, ?int $sucursalId): array
    {
        $mecanicos = Mecanico::query()
            ->with(['empleado', 'asignaciones' => fn ($q) => $q->whereBetween('fecha_asignacion', [$desde, $hasta])])
            ->when($sucursalId, fn ($q) => $q->whereHas('empleado', fn ($q2) => $q2->where('sucursal_id', $sucursalId)))
            ->get()
            ->map(function ($m) {
                $finalizadas = $m->asignaciones->where('estado', 'finalizada');
                return [
                    'mecanico' => $m->empleado->nombre_completo ?? '—',
                    'asignaciones' => $m->asignaciones->count(),
                    'finalizadas' => $finalizadas->count(),
                ];
            })
            ->sortByDesc('finalizadas');

        return [
            'titulo' => 'Productividad de mecánicos',
            'descripcion' => 'Asignaciones y finalizaciones entre ' . $desde->format('d/m/Y') . ' y ' . $hasta->format('d/m/Y') . '.',
            'mecanicos' => $mecanicos,
        ];
    }

    private function reporteStockCritico(?int $sucursalId): array
    {
        $items = Inventario::query()
            ->with(['repuesto', 'sucursal'])
            ->when($sucursalId, fn ($q) => $q->where('sucursal_id', $sucursalId))
            ->whereHas('repuesto', function ($q) {
                $q->whereColumn('inventarios.cantidad_actual', '<=', 'repuestos.stock_minimo');
            })
            ->orderBy('cantidad_actual')
            ->get();

        return [
            'titulo' => 'Stock crítico',
            'descripcion' => 'Repuestos con stock igual o menor al mínimo.',
            'items' => $items,
        ];
    }

    private function reporteClientesFrecuentes(Carbon $desde, Carbon $hasta, ?int $sucursalId): array
    {
        $clientes = Cliente::query()
            ->withCount(['ordenesTrabajo' => function ($q) use ($desde, $hasta, $sucursalId) {
                $q->whereBetween('fecha_emision', [$desde, $hasta]);
                if ($sucursalId) {
                    $q->where('sucursal_id', $sucursalId);
                }
            }])
            ->with(['ordenesTrabajo' => function ($q) use ($desde, $hasta, $sucursalId) {
                $q->whereBetween('fecha_emision', [$desde, $hasta]);
                if ($sucursalId) {
                    $q->where('sucursal_id', $sucursalId);
                }
            }])
            ->get()
            ->map(function ($c) {
                $ordenes = $c->ordenesTrabajo;
                return [
                    'cliente' => $c->nombre_completo,
                    'ordenes' => $ordenes->count(),
                    'monto_total' => (float) $ordenes->sum('total_general'),
                ];
            })
            ->filter(fn ($r) => $r['ordenes'] > 0)
            ->sortByDesc('ordenes')
            ->values();

        return [
            'titulo' => 'Clientes frecuentes',
            'descripcion' => 'Clientes con mayor número de órdenes en el período.',
            'clientes' => $clientes,
        ];
    }

    private function reporteServiciosMasVendidos(Carbon $desde, Carbon $hasta, ?int $sucursalId): array
    {
        $servicios = Servicio::query()
            ->with('tipoServicio')
            ->get()
            ->map(function ($s) use ($desde, $hasta, $sucursalId) {
                $query = \App\Models\DetalleOrdenTrabajo::query()
                    ->where('servicio_id', $s->id)
                    ->where('tipo', 'servicio')
                    ->whereHas('ordenTrabajo', function ($q) use ($desde, $hasta, $sucursalId) {
                        $q->whereBetween('fecha_emision', [$desde, $hasta]);
                        if ($sucursalId) {
                            $q->where('sucursal_id', $sucursalId);
                        }
                    });
                return [
                    'servicio' => $s->nombre,
                    'tipo' => $s->tipoServicio->nombre ?? '—',
                    'veces' => $query->count(),
                ];
            })
            ->filter(fn ($r) => $r['veces'] > 0)
            ->sortByDesc('veces')
            ->values();

        return [
            'titulo' => 'Servicios más vendidos',
            'descripcion' => 'Servicios con mayor cantidad registrada en el período.',
            'servicios' => $servicios,
        ];
    }

    private function obtenerDatosReporte(Request $request, string $tipo): ?array
    {
        $sucursalId = $this->usuarioSucursalId();
        $desde = $request->filled('desde') ? Carbon::parse($request->input('desde'))->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $hasta = $request->filled('hasta') ? Carbon::parse($request->input('hasta'))->endOfDay() : Carbon::now()->endOfDay();

        return match ($tipo) {
            'ingresos' => $this->reporteIngresos($desde, $hasta, $sucursalId),
            'ordenes-estado' => $this->reporteOrdenesEstado($sucursalId),
            'mecanicos-productividad' => $this->reporteMecanicosProductividad($desde, $hasta, $sucursalId),
            'stock-critico' => $this->reporteStockCritico($sucursalId),
            'clientes-frecuentes' => $this->reporteClientesFrecuentes($desde, $hasta, $sucursalId),
            'servicios-mas-vendidos' => $this->reporteServiciosMasVendidos($desde, $hasta, $sucursalId),
            default => null,
        };
    }

    public function descargarPdf(Request $request, string $tipo)
    {
        $datos = $this->obtenerDatosReporte($request, $tipo);
        if (!$datos) abort(404);

        $pdf = Pdf::loadView('admin.reportes.pdf.reporte', [
            'tipo' => $tipo,
            'datos' => $datos,
        ])->setPaper('letter', 'portrait');

        $nombre = 'reporte-' . $tipo . '-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($nombre);
    }

    public function descargarCsv(Request $request, string $tipo)
    {
        $datos = $this->obtenerDatosReporte($request, $tipo);
        if (!$datos) abort(404);

        $nombre = 'reporte-' . $tipo . '-' . now()->format('Ymd-His') . '.csv';
        $separador = ';';
        $lineas = [];

        // Header
        $encabezados = match ($tipo) {
            'ingresos' => ['Fecha', 'Orden', 'Metodo', 'Monto'],
            'ordenes-estado' => ['Estado', 'Cantidad', 'Monto'],
            'mecanicos-productividad' => ['Mecanico', 'Asignaciones', 'Finalizadas'],
            'stock-critico' => ['Repuesto', 'Codigo', 'Stock', 'Minimo'],
            'clientes-frecuentes' => ['Cliente', 'Ordenes', 'Monto total'],
            'servicios-mas-vendidos' => ['Servicio', 'Veces'],
            default => [],
        };
        $lineas[] = implode($separador, $encabezados);

        // Data rows
        $filas = match ($tipo) {
            'ingresos' => $datos['pagos']->map(fn ($p) => [
                $p->fecha_pago?->format('d/m/Y'), $p->ordenTrabajo->numero_orden ?? '',
                $p->metodoPago->nombre ?? '', number_format((float) $p->monto, 2, ',', '.'),
            ]),
            'ordenes-estado' => $datos['por_estado']->map(fn ($row) => [
                str_replace('_', ' ', $row->estado), $row->cantidad, number_format((float) $row->monto, 2, ',', '.'),
            ]),
            'mecanicos-productividad' => collect($datos['mecanicos'])->map(fn ($m) => [
                $m['mecanico'], $m['asignaciones'], $m['finalizadas'],
            ]),
            'stock-critico' => $datos['items']->map(fn ($it) => [
                $it->repuesto->nombre ?? '', $it->repuesto->codigo ?? '',
                $it->cantidad_actual, $it->repuesto->stock_minimo ?? 0,
            ]),
            'clientes-frecuentes' => collect($datos['clientes'])->map(fn ($c) => [
                $c['cliente'], $c['ordenes'], number_format($c['monto_total'], 2, ',', '.'),
            ]),
            'servicios-mas-vendidos' => collect($datos['servicios'])->map(fn ($s) => [
                $s['servicio'], $s['veces'],
            ]),
            default => collect([]),
        };

        foreach ($filas as $fila) {
            $lineas[] = implode($separador, array_map(fn ($v) => '"' . str_replace('"', '""', (string) $v) . '"', $fila));
        }

        $contenido = implode("\n", $lineas);

        return Response::make($contenido, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $nombre . '"',
        ]);
    }
}
