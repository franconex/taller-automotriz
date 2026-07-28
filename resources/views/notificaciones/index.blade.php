@php
    $layout = match(true) {
        Auth::user()?->tieneRol('Cliente') => 'layouts.cliente-sidebar',
        default => 'layouts.admin',
    };
@endphp
@extends($layout)

@section('title', 'Notificaciones')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Notificaciones</h4>
        <form method="POST" action="{{ route('notificaciones.marcar-todas') }}" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-outline-secondary">Marcar todas leídas</button>
        </form>
    </div>

    @if ($notificaciones->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-check2-circle display-4 d-block mb-3"></i>
            <p>No tienes notificaciones.</p>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                @foreach ($notificaciones as $n)
                    @php
                        $icono = $n->data['icono'] ?? 'bi-bell';
                        $url = $n->data['url'] ?? '#';
                        $noLeida = is_null($n->read_at);
                    @endphp
                    <a href="{{ $url }}"
                       class="list-group-item list-group-item-action d-flex gap-3 align-items-start {{ $noLeida ? 'fw-semibold' : '' }}"
                       style="{{ $noLeida ? 'background:#fef2f2;border-left:3px solid #E31E24;' : '' }}">
                        <div style="font-size:1.2rem;color:#E31E24;">
                            <i class="bi {{ $icono }}"></i>
                        </div>
                        <div class="flex-fill" style="min-width:0;">
                            <div class="d-flex justify-content-between">
                                <span>{{ $n->data['titulo'] ?? 'Notificación' }}</span>
                                <small class="text-muted ms-2" style="white-space:nowrap;">{{ $n->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="small text-muted text-truncate">{{ $n->data['mensaje'] ?? '' }}</div>
                        </div>
                        @if ($noLeida)
                            <span class="badge bg-danger rounded-pill" style="font-size:.5rem;">NUEVA</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
        <div class="mt-3">
            {{ $notificaciones->links() }}
        </div>
    @endif
@endsection
