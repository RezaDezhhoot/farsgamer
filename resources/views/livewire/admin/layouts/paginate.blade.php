@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation">
        <ul class="pagination justify-content-center mt-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item previous disabled">
                    <button class="page-link"><i class="bx bx-chevron-right"></i></button>
                </li>
            @else
                <li class="page-item previous">
                    <button class="page-link" wire:click="previousPage"><i class="bx bx-chevron-right"></i></button>
                </li>
            @endif
            @foreach ($elements as $element)

                @if (is_string($element))
                    <li class="page-item disabled">
                        <button class="page-link">{{ $element }}</button>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active disabled">
                                <button class="page-link" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                            </li>
                        @else
                            <li class="page-item">
                                <button class="page-link" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item next">
                    <button class="page-link" wire:click="nextPage"><i class="bx bx-chevron-left"></i></button>
                </li>
            @else
                <li class="page-item next disabled">
                    <button class="page-link"><i class="bx bx-chevron-left"></i></button>
                </li>
            @endif
        </ul>
    </nav>
@endif
