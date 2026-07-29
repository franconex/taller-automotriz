@php
    use Illuminate\Support\Facades\Route;

    $items = [
        ['route' => 'mecanico.dashboard', 'icon' => 'bi-grid-1x2-fill', 'label' => 'Panel'],
        ['route' => 'mecanico.citas.index', 'icon' => 'bi-calendar-check', 'label' => 'Citas asignadas'],
        ['route' => 'mecanico.ordenes.index', 'icon' => 'bi-journal-text', 'label' => 'Mis órdenes'],
    ];

    $consulta = [
        ['route' => 'admin.inventario.index', 'permission' => 'inventario.ver', 'icon' => 'bi-box-seam-fill', 'label' => 'Inventario'],
        ['route' => 'admin.servicios.index', 'permission' => 'servicios.ver', 'icon' => 'bi-gear-wide-connected', 'label' => 'Servicios'],
        ['route' => 'admin.vacaciones.index', 'permission' => null, 'icon' => 'bi-sun', 'label' => 'Vacaciones'],
    ];

    $hasVisible = function (array $list): bool {
        foreach ($list as $item) {
            $routeExists = Route::has($item['route']);
            $hasPermission = empty($item['permission']) || Auth::user()->tienePermiso($item['permission']);
            if ($routeExists && $hasPermission) {
                return true;
            }
        }
        return false;
    };

    $trabajosActivos = \App\Models\AsignacionTrabajo::where('mecanico_id', Auth::user()->empleado?->mecanico?->id)
        ->whereHas('ordenTrabajo', fn ($q) => $q->whereIn('estado', ['programada','recibida','diagnostico','en_proceso','esperando_repuesto','pausada','pendiente_autorizacion']))
        ->whereNull('fecha_finalizacion')
        ->count();
@endphp

<nav class="admin-sidebar__nav" aria-label="Menú mecánico">
    {{-- ESTADO --}}
    @php
        $mec = Auth::user()->empleado?->mecanico;
    @endphp
    @if ($mec)
        <div class="px-3 py-2">
            <div class="d-flex align-items-center gap-2 small">
                <span style="width:8px;height:8px;border-radius:50%;background:{{ $mec->disponibilidad === 'disponible' ? '#16A34A' : '#DC2626' }};display:inline-block;"></span>
                <span class="fw-semibold text-uppercase" style="font-size:.65rem;letter-spacing:.5px;color:{{ $mec->disponibilidad === 'disponible' ? '#16A34A' : '#DC2626' }};">
                    {{ $mec->disponibilidad === 'disponible' ? 'Disponible' : 'Ocupado' }}
                </span>
            </div>
        </div>
    @endif

    {{-- PRINCIPAL --}}
    <div class="admin-sidebar__section">Principal</div>
    <ul class="list-unstyled m-0">
        <x-admin.sidebar-item
            routeName="mecanico.dashboard"
            icon="bi-grid-1x2-fill"
            label="Panel" />
        <x-admin.sidebar-item
            routeName="mecanico.ordenes.index"
            icon="bi-journal-text"
            label="Mis órdenes" />
    </ul>

    {{-- TRABAJOS ACTIVOS --}}
    @if ($trabajosActivos > 0)
        <div class="px-3 py-2">
            <a href="{{ route('mecanico.ordenes.index') }}" class="d-flex align-items-center justify-content-between text-decoration-none rounded px-2 py-2" style="background:#eef2ff;">
                <span class="small text-primary fw-semibold"><i class="bi bi-tools me-1"></i> Trabajos activos</span>
                <span class="badge bg-primary">{{ $trabajosActivos }}</span>
            </a>
        </div>
    @endif

    {{-- CONSULTA --}}
    @if ($hasVisible($consulta))
        <div class="admin-sidebar__section">Consulta</div>
        <ul class="list-unstyled m-0">
            @foreach ($consulta as $item)
                <x-admin.sidebar-item
                    :routeName="$item['route']"
                    :permission="$item['permission'] ?? null"
                    :icon="$item['icon']"
                    :label="$item['label']" />
            @endforeach
        </ul>
    @endif
</nav>
