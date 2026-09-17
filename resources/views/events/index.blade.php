@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:py-14">
    <div class="max-w-xl">
        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.22em] text-[#159b83] sm:text-sm">Metal Health Community</p>
        <h1 class="text-3xl font-bold leading-tight text-[#104b68] sm:text-4xl lg:text-[44px]">Temukan event dan history MHC.</h1>
        <p class="mt-4 text-sm leading-7 text-[#5d7d86] sm:text-base">Lihat event yang sedang tersedia, pendaftaran yang sudah ditutup, dan arsip pertemuan komunitas.</p>
    </div>

    <form method="GET" action="{{ route('events.index') }}" class="mt-8 max-w-xl">
        <label for="search" class="sr-only">Cari event</label>
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#7a9a9d]" />
                <input id="search" name="search" value="{{ $search }}" class="min-h-12 w-full rounded-2xl border border-[#cceae4] bg-white py-3 pl-12 pr-4 text-sm text-[#104b68] shadow-sm outline-none transition placeholder:text-[#7a9a9d] focus:border-[#159b83] focus:ring-4 focus:ring-[#159b83]/10" placeholder="Cari nama event atau lokasi">
            </div>
            <button type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-2xl bg-[#159b83] px-5 text-sm font-bold text-white shadow-sm transition hover:bg-[#0f826f]">
                <x-heroicon-o-magnifying-glass class="h-5 w-5" />
                Cari
            </button>
            @if ($search !== '')
                <a href="{{ route('events.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-[#cceae4] bg-white px-5 text-sm font-bold text-[#104b68] transition hover:bg-[#f1faf8]">Reset</a>
            @endif
        </div>
    </form>

    <div class="mt-9 grid gap-6 md:grid-cols-2 lg:grid-cols-[repeat(auto-fill,minmax(300px,340px))]">
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
                <div class="p-5 lg:p-4">
                    <div class="flex items-center justify-between gap-3">
                        <p class="flex min-w-0 items-center gap-2 text-sm text-[#159b83]"><x-heroicon-o-calendar-days class="h-4 w-4 shrink-0" /> <span class="truncate">{{ $event->event_date->translatedFormat('d F Y') }}</span></p>
                        <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                    </div>
                    <h2 class="mt-2 text-lg font-semibold leading-snug text-[#104b68] group-hover:text-[#159b83] sm:text-xl lg:text-lg">{{ $event->name }}</h2>
                    <p class="mt-2 line-clamp-2 text-sm text-[#5d7d86]">{{ \Illuminate\Support\Str::of(strip_tags($event->description))->squish()->limit(140) }}</p>
                    <p class="mt-4 flex items-center gap-2 text-sm text-[#356b78]"><x-heroicon-o-map-pin class="h-4 w-4 shrink-0 text-[#159b83]" /> {{ $event->location_name }}</p>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-dashed border-[#cceae4] bg-white/60 p-8 text-sm text-[#5d7d86] md:col-span-2 lg:col-span-3">
                {{ $search !== '' ? 'Event yang kamu cari belum ditemukan.' : 'Belum ada event yang tersedia.' }}
            </div>
        @endforelse
    </div>

    @if ($events->hasPages())
        <div class="mt-10">
            {{ $events->onEachSide(1)->links('pagination.mhc') }}
        </div>
    @endif
</section>

<x-public.whatsapp-floating-button />
@endsection
