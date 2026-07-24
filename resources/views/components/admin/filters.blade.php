@props([
    'action' => '#',
    'method' => 'GET',
    'autocomplete' => true,
    'searchPlaceholder' => 'Buscar...',
    'searchName' => 'q',
    'searchValue' => null,
])

<form method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}"
      action="{{ $action }}"
      class="admin-toolbar"
      @if (! $autocomplete) autocomplete="off" @endif
      role="search"
      aria-label="Filtros">
    @csrf
    @unless (strtoupper($method) === 'GET')
        @method($method)
    @endunless

    <div class="admin-search">
        <i class="bi bi-search admin-search__icon" aria-hidden="true"></i>
        <label for="tpSearch" class="visually-hidden">{{ $searchPlaceholder }}</label>
        <input type="search"
               id="tpSearch"
               name="{{ $searchName }}"
               value="{{ $searchValue ?? request($searchName) }}"
               class="form-control"
               placeholder="{{ $searchPlaceholder }}">
    </div>

    {{ $slot }}

    @if (request()->except([$searchName, 'page']))
        <button type="button" class="admin-filter-chip" onclick="window.location='{{ $action }}'">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
            Limpiar
        </button>
    @endif
</form>

@if (request()->except([$searchName, 'page']))
    <div class="admin-filter-summary" aria-live="polite">
        <span>Filtros aplicados:</span>
        @foreach (request()->except([$searchName, 'page', '_token']) as $key => $value)
            @if ($value !== null && $value !== '')
                <span class="admin-filter-pill">
                    {{ ucfirst($key) }}: {{ $value }}
                    <a href="{{ url()->current() }}?{{ http_build_query(array_diff_key(request()->query(), [$key => ''])) }}"
                       aria-label="Quitar filtro {{ $key }}">
                        <i class="bi bi-x" aria-hidden="true"></i>
                    </a>
                </span>
            @endif
        @endforeach
        <a href="{{ $action }}" class="admin-filter-summary__clear">Quitar todos</a>
    </div>
@endif
