{{-- Paginação no estilo Agro Raiz (Laravel usa esta view automaticamente). --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('pagination.previous') }}" class="flex items-center justify-between">
        <div class="flex flex-1 items-center justify-between">
            <p class="text-sm text-raiz-600">
                {{ __('Mostrando') }}
                <span class="font-medium text-raiz-800">{{ $paginator->firstItem() }}</span>
                {{ __('a') }}
                <span class="font-medium text-raiz-800">{{ $paginator->lastItem() }}</span>
                {{ __('de') }}
                <span class="font-medium text-raiz-800">{{ $paginator->total() }}</span>
                {{ __('registros') }}
            </p>

            <div class="flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span class="cursor-not-allowed rounded-lg px-3 py-1.5 text-sm text-raiz-300">{!! __('pagination.previous') !!}</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="rounded-lg px-3 py-1.5 text-sm text-raiz-700 transition hover:bg-raiz-100">{!! __('pagination.previous') !!}</a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-3 py-1.5 text-sm text-raiz-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="rounded-lg bg-raiz-700 px-3 py-1.5 text-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="rounded-lg px-3 py-1.5 text-sm text-raiz-700 transition hover:bg-raiz-100">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="rounded-lg px-3 py-1.5 text-sm text-raiz-700 transition hover:bg-raiz-100">{!! __('pagination.next') !!}</a>
                @else
                    <span class="cursor-not-allowed rounded-lg px-3 py-1.5 text-sm text-raiz-300">{!! __('pagination.next') !!}</span>
                @endif
            </div>
        </div>
    </nav>
@endif
