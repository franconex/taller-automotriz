@props([
    'id' => 'adminOffcanvas',
    'title' => 'Formulario',
    'subtitle' => null,
    'action' => '#',
    'method' => 'POST',
    'submitText' => 'Guardar',
    'submitIcon' => 'bi-check2',
    'cancelText' => 'Cancelar',
    'enctype' => null,
])

<div class="offcanvas offcanvas-end admin-offcanvas"
     tabindex="-1"
     id="{{ $id }}"
     aria-labelledby="{{ $id }}Label">
    <div class="offcanvas-header">
        <div>
            <h2 class="offcanvas-title" id="{{ $id }}Label">
                {{ $title }}
                @if ($subtitle)<small>{{ $subtitle }}</small>@endif
            </h2>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>

    <form method="POST"
          action="{{ $action }}"
          @if ($enctype) enctype="{{ $enctype }}" @endif
          class="d-flex flex-column flex-grow-1">
        @csrf
        @unless (strtoupper($method) === 'POST')
            @method($method)
        @endunless

        <div class="offcanvas-body flex-grow-1">
            {{ $slot }}
        </div>

        <div class="admin-offcanvas__footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">
                {{ $cancelText }}
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="bi {{ $submitIcon }}" aria-hidden="true"></i>
                {{ $submitText }}
            </button>
        </div>
    </form>
</div>
