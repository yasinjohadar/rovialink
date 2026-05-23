@if ($paginator->hasPages())
<nav class="catalog-pagination section-fade-up" aria-label="تصفح الصفحات">
    <ul class="catalog-pagination__list">
        @if ($paginator->onFirstPage())
        <li class="catalog-pagination__item is-disabled">
            <span class="catalog-pagination__btn" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
        </li>
        @else
        <li class="catalog-pagination__item">
            <a class="catalog-pagination__btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="الصفحة السابقة">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
            <li class="catalog-pagination__item is-disabled">
                <span class="catalog-pagination__btn catalog-pagination__btn--dots">{{ $element }}</span>
            </li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                    <li class="catalog-pagination__item is-active" aria-current="page">
                        <span class="catalog-pagination__btn">{{ $page }}</span>
                    </li>
                    @else
                    <li class="catalog-pagination__item">
                        <a class="catalog-pagination__btn" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
        <li class="catalog-pagination__item">
            <a class="catalog-pagination__btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="الصفحة التالية">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
        @else
        <li class="catalog-pagination__item is-disabled">
            <span class="catalog-pagination__btn" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
        </li>
        @endif
    </ul>
</nav>
@endif
