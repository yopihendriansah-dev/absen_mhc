@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4" aria-label="Navigasi pagination">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center rounded-xl border border-[#d7eee8] bg-[#f2faf8] px-4 py-2 text-sm font-semibold text-[#9ab7b5]">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center rounded-xl border border-[#cfe7e1] bg-white px-4 py-2 text-sm font-semibold text-[#356b78] transition hover:border-[#159b83] hover:text-[#159b83]">Sebelumnya</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center rounded-xl border border-[#cfe7e1] bg-white px-4 py-2 text-sm font-semibold text-[#356b78] transition hover:border-[#159b83] hover:text-[#159b83]">Berikutnya</a>
            @else
                <span class="inline-flex items-center rounded-xl border border-[#d7eee8] bg-[#f2faf8] px-4 py-2 text-sm font-semibold text-[#9ab7b5]">Berikutnya</span>
            @endif
        </div>

        <div class="hidden flex-1 items-center justify-between sm:flex">
            <p class="text-sm text-[#5d7d86]">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</p>

            <div class="flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#d7eee8] bg-[#f2faf8] text-[#9ab7b5]"><x-heroicon-o-chevron-left class="h-4 w-4" /></span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" aria-label="Halaman sebelumnya" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#cfe7e1] bg-white text-[#356b78] transition hover:border-[#159b83] hover:text-[#159b83]"><x-heroicon-o-chevron-left class="h-4 w-4" /></a>
                @endif

                @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg bg-[#159b83] px-2 text-sm font-semibold text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-[#cfe7e1] bg-white px-2 text-sm font-semibold text-[#356b78] transition hover:border-[#159b83] hover:text-[#159b83]">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" aria-label="Halaman berikutnya" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#cfe7e1] bg-white text-[#356b78] transition hover:border-[#159b83] hover:text-[#159b83]"><x-heroicon-o-chevron-right class="h-4 w-4" /></a>
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#d7eee8] bg-[#f2faf8] text-[#9ab7b5]"><x-heroicon-o-chevron-right class="h-4 w-4" /></span>
                @endif
            </div>
        </div>
    </nav>
@endif
