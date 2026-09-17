@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:py-20">
    <div class="max-w-2xl">
        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.25em] text-[#159b83]">Metal Health Community</p>
        <h1 class="text-4xl font-bold tracking-tight text-[#104b68] sm:text-6xl">Temukan event dan history MHC.</h1>
        <p class="mt-5 text-base leading-7 text-[#5d7d86]">Lihat event yang sedang tersedia, pendaftaran yang sudah ditutup, dan arsip pertemuan komunitas.</p>
    </div>

    <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($events as $event)
            @php
                $registeredCount = $event->registered_registrations_count ?? $event->registrations()->where('status', 'registered')->count();
                $isFull = $event->capacity_type === 'limited' && $registeredCount >= (int) $event->capacity;
                $badge = match (true) {
                    $event->status === \App\Models\Event::STATUS_CLOSED => ['label' => 'Ditutup', 'class' => 'bg-slate-100 text-slate-600'],
                    $event->isEnded() => ['label' => 'Sudah berakhir', 'class' => 'bg-amber-50 text-amber-700'],
                    $isFull => ['label' => 'Penuh', 'class' => 'bg-red-50 text-red-700'],
                    default => ['label' => 'Tersedia', 'class' => 'bg-[#e8f8f3] text-[#159b83]'],
                };
            @endphp
            <a href="{{ route('events.show', $event) }}" class="group overflow-hidden rounded-2xl border border-[#d7eee8] bg-white shadow-sm transition hover:-translate-y-1 hover:border-[#20a88d] hover:shadow-lg">
                <div class="aspect-[4/5] bg-[#e8f6f2]">
                    @if ($poster = $event->getFirstMediaUrl('event-posters'))
                        <img src="{{ $poster }}" alt="Poster {{ $event->name }}" class="h-full w-full object-contain" loading="lazy" decoding="async">
                    @else
                        <div class="flex h-full items-center justify-center text-sm text-[#7a9a9d]">Poster event</div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between gap-3">
                        <p class="flex min-w-0 items-center gap-2 text-sm text-[#159b83]"><x-heroicon-o-calendar-days class="h-4 w-4 shrink-0" /> <span class="truncate">{{ $event->event_date->translatedFormat('d F Y') }}</span></p>
                        <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                    </div>
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
