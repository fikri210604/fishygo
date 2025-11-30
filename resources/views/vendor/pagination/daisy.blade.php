@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="w-full">
        <div class="flex items-center justify-center">
            <ul class="join">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="join-item btn btn-sm btn-disabled" aria-disabled="true" aria-label="Previous">
                        <span aria-hidden="true">«</span>
                    </li>
                @else
                    <a class="join-item btn btn-sm" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">«</a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="join-item btn btn-sm btn-disabled" aria-disabled="true"><span>{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="join-item btn btn-sm btn-active" aria-current="page"><span>{{ $page }}</span></li>
                            @else
                                <a class="join-item btn btn-sm" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a class="join-item btn btn-sm" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">»</a>
                @else
                    <li class="join-item btn btn-sm btn-disabled" aria-disabled="true" aria-label="Next">
                        <span aria-hidden="true">»</span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif

