@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:py-20">
    <div class="max-w-2xl">
        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.25em] text-[#159b83]">Metal Health Community</p>
        <h1 class="text-4xl font-bold tracking-tight text-[#104b68] sm:text-6xl">Temukan event MHC berikutnya.</h1>
        <p class="mt-5 text-base leading-7 text-[#5d7d86]">Daftar untuk bertemu, berbagi cerita, dan membangun ruang yang lebih suportif bersama komunitas.</p>
    </div>

    <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($events as $event)
            <a href="{{ route('events.show', $event) }}" class="group overflow-hidden rounded-2xl border border-[#d7eee8] bg-white shadow-sm transition hover:-translate-y-1 hover:border-[#20a88d] hover:shadow-lg">
                <div class="aspect-[4/5] bg-[#e8f6f2]">
                    @if ($poster = $event->getFirstMediaUrl('event-posters', 'thumbnail'))
                        <img src="{{ $poster }}" alt="Poster {{ $event->name }}" class="h-full w-full object-contain">
                    @else
                        <div class="flex h-full items-center justify-center text-sm text-[#7a9a9d]">Poster event</div>
                    @endif
                </div>
                <div class="p-5">
                    <p class="flex items-center gap-2 text-sm text-[#159b83]"><x-heroicon-o-calendar-days class="h-4 w-4" /> {{ $event->event_date->translatedFormat('d F Y') }}</p>
                    <h2 class="mt-2 text-xl font-semibold text-[#104b68] group-hover:text-[#159b83]">{{ $event->name }}</h2>
                    <p class="mt-2 line-clamp-2 text-sm text-[#5d7d86]">{{ \Illuminate\Support\Str::of(strip_tags($event->description))->squish()->limit(140) }}</p>
                    <p class="mt-5 flex items-center gap-2 text-sm text-[#356b78]"><x-heroicon-o-map-pin class="h-4 w-4 shrink-0 text-[#159b83]" /> {{ $event->location_name }}</p>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-dashed border-white/15 p-8 text-stone-400 md:col-span-2 lg:col-span-3">Belum ada event yang tersedia.</div>
        @endforelse
    </div>

    @if ($events->hasPages())
        <div class="mt-10">
            {{ $events->onEachSide(1)->links('pagination.mhc') }}
        </div>
    @endif
</section>
@endsection
