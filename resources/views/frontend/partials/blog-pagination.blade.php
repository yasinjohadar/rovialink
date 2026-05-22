@if ($paginator->hasPages())
<nav class="mt-5" aria-label="تصفح المقالات">
    <ul class="pagination justify-content-center gap-2">
        @if ($paginator->onFirstPage())
        <li class="page-item disabled">
            <span class="page-link bg-glass border-secondary text-secondary rounded-pill px-3"><i class="fas fa-chevron-right"></i></span>
        </li>
        @else
        <li class="page-item">
            <a class="page-link bg-glass border-secondary text-white rounded-pill px-3" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fas fa-chevron-right"></i></a>
        </li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
            <li class="page-item disabled"><span class="page-link bg-glass border-secondary text-secondary rounded-pill px-3">{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                    <li class="page-item active"><span class="page-link bg-accent border-accent text-white rounded-pill px-3">{{ $page }}</span></li>
                    @else
                    <li class="page-item"><a class="page-link bg-glass border-secondary text-white rounded-pill px-3" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
        <li class="page-item">
            <a class="page-link bg-glass border-secondary text-white rounded-pill px-3" href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="fas fa-chevron-left"></i></a>
        </li>
        @else
        <li class="page-item disabled">
            <span class="page-link bg-glass border-secondary text-secondary rounded-pill px-3"><i class="fas fa-chevron-left"></i></span>
        </li>
        @endif
    </ul>
</nav>
@endif
