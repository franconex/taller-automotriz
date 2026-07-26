<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SolicitudCompraRequest;
use App\Models\DetalleSolicitudCompra;
use App\Models\Inventario;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\SolicitudCompra;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SolicitudCompraController extends AdminController
{
    public function index(Request $request): View
    {
        $query = SolicitudCompra::query()
            ->with(['sucursal', 'usuarioSolicitante', 'detalles']);

        $this->scopeSucursal($query, 'sucursal_id');
        $this->aplicarFiltros($request, $query, ['estado', 'prioridad', 'sucursal_id']);
        $this->aplicarBusqueda($query, $request, ['numero']);

        $solicitudes = $query->orderByDesc('fecha_solicitud')->paginate(15)->withQueryString();

        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.solicitudes-compra.index', [
            'solicitudes' => $solicitudes,
            'sucursales' => $sucursales,
        ]);
    }

    public function create(Request $request): View
    {
        $repuestosSeleccionados = collect();
        $repuestoIds = $request->input('repuestos', []);

        if (! empty($repuestoIds)) {
            $repuestosSeleccionados = Inventario::with(['repuesto', 'sucursal'])
                ->whereIn('repuesto_id', $repuestoIds)
                ->when($this->usuarioSucursalId(), fn ($q) => $q->where('sucursal_id', $this->usuarioSucursalId()))
                ->get();
        }

        $inventario = Inventario::with(['repuesto', 'sucursal'])
            ->select('inventarios.*')
            ->join('repuestos', 'inventarios.repuesto_id', '=', 'repuestos.id')
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('inventarios.sucursal_id', $this->usuarioSucursalId()))
            ->orderBy('repuestos.nombre')
            ->get();

        $sucursales = Sucursal::query()
            ->when($this->usuarioSucursalId(), fn ($q) => $q->where('id', $this->usuarioSucursalId()))
            ->orderBy('nombre')
            ->get();

        return view('admin.solicitudes-compra.create', [
            'inventario' => $inventario,
            'repuestosSeleccionados' => $repuestosSeleccionados,
            'sucursales' => $sucursales,
        ]);
    }

    public function store(SolicitudCompraRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $solicitud = DB::transaction(function () use ($datos) {
            $solicitud = SolicitudCompra::create([
                'numero' => $this->generarNumeroSolicitud(),
                'sucursal_id' => $this->usuarioSucursalId() ?? $datos['sucursal_id'],
                'usuario_solicitante_id' => Auth::id(),
                'prioridad' => $datos['prioridad'],
                'estado' => 'pendiente',
                'observaciones' => $datos['observaciones'] ?? null,
                'fecha_solicitud' => now(),
            ]);

            foreach ($datos['productos'] as $item) {
                DetalleSolicitudCompra::create([
                    'solicitud_compra_id' => $solicitud->id,
                    'repuesto_id' => $item['repuesto_id'],
                    'cantidad_solicitada' => $item['cantidad'],
                    'stock_actual' => $item['stock_actual'] ?? 0,
                    'stock_minimo' => $item['stock_minimo'] ?? 0,
                ]);
            }

            return $solicitud;
        });

        return $this->redirigirALista('admin.solicitudes-compra.index', "Solicitud {$solicitud->numero} creada con éxito.");
    }

    public function show(SolicitudCompra $solicitud): View
    {
        $solicitud->load([
            'sucursal',
            'usuarioSolicitante',
            'usuarioAutoriza',
            'detalles.repuesto',
            'cotizaciones.proveedor',
            'cotizaciones.detalles.repuesto',
            'cotizaciones.usuario',
        ]);

        $proveedoresCompatibles = $this->obtenerProveedoresCompatibles($solicitud);

        return view('admin.solicitudes-compra.show', [
            'solicitud' => $solicitud,
            'proveedoresCompatibles' => $proveedoresCompatibles,
        ]);
    }

    public function aprobar(SolicitudCompra $solicitud): RedirectResponse
    {
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden aprobar solicitudes pendientes.');
        }

        $solicitud->update([
            'estado' => 'aprobada',
            'usuario_autoriza_id' => Auth::id(),
            'fecha_aprobacion' => now(),
        ]);

        return back()->with('success', "Solicitud {$solicitud->numero} aprobada.");
    }

    public function rechazar(Request $request, SolicitudCompra $solicitud): RedirectResponse
    {
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden rechazar solicitudes pendientes.');
        }

        $request->validate(['motivo' => ['nullable', 'string', 'max:500']]);

        $solicitud->update([
            'estado' => 'rechazada',
            'usuario_autoriza_id' => Auth::id(),
            'fecha_aprobacion' => now(),
            'observaciones' => $request->motivo
                ? ($solicitud->observaciones ? $solicitud->observaciones . "\n\nMotivo de rechazo: " . $request->motivo : 'Motivo de rechazo: ' . $request->motivo)
                : $solicitud->observaciones,
        ]);

        return back()->with('success', "Solicitud {$solicitud->numero} rechazada.");
    }

    protected function generarNumeroSolicitud(): string
    {
        $anio = now()->format('Y');
        $ultimo = SolicitudCompra::whereYear('created_at', $anio)
            ->orderByDesc('id')
            ->value('numero');

        if ($ultimo && preg_match('/SC-' . $anio . '-(\d+)/', $ultimo, $m)) {
            $correlativo = (int) $m[1] + 1;
        } else {
            $correlativo = 1;
        }

        return 'SC-' . $anio . '-' . str_pad($correlativo, 6, '0', STR_PAD_LEFT);
    }

    protected function obtenerProveedoresCompatibles(SolicitudCompra $solicitud): array
    {
        $repuestoIds = $solicitud->detalles->pluck('repuesto_id');

        $directos = Proveedor::whereHas('repuestos', function ($q) use ($repuestoIds) {
            $q->whereIn('repuestos.id', $repuestoIds);
        })
            ->where('estado', true)
            ->withCount(['repuestos' => fn ($q) => $q->whereIn('id', $repuestoIds)])
            ->orderByDesc('repuestos_count')
            ->get();

        $idsDirectos = $directos->pluck('id');

        $sugeridos = Proveedor::where('estado', true)
            ->whereNotIn('id', $idsDirectos)
            ->orderBy('nombre_empresa')
            ->get();

        return [
            'directos' => $directos,
            'sugeridos' => $sugeridos,
        ];
    }
}
