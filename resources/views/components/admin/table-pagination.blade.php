@props([
    'paginator' => null,
    'simple' => false,
])

@if ($paginator && method_exists($paginator, 'links'))
    <div class="admin-pagination">
        <div>
            Mostrando
            <strong>{{ $paginator->firstItem() ?? 0 }}</strong>–<strong>{{ $paginator->lastItem() ?? 0 }}</strong>
            de <strong>{{ $paginator->total() }}</strong>
        </div>

        @if (! $simple)
            <ul class="admin-pagination__pages" role="navigation" aria-label="Paginación">
                @if ($paginator->onFirstPage())
                    <li><span class="admin-pagination__link disabled" aria-disabled="true">‹</span></li>
                @else
                    <li><a class="admin-pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Anterior">‹</a></li>
                @endif

                @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><span class="admin-pagination__link active" aria-current="page">{{ $page }}</span></li>
                    @else
                        <li><a class="admin-pagination__link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li><a class="admin-pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Siguiente">›</a></li>
                @else
                    <li><span class="admin-pagination__link disabled" aria-disabled="true">›</span></li>
                @endif
            </ul>
        @endif
    </div>
@endif
