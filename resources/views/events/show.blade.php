@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
    <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-sm text-[#5d7d86] transition hover:text-[#159b83]"><x-heroicon-o-arrow-left class="h-4 w-4" /> Semua event</a>

    @php
        $registeredCount = $event->registrations()->where('status', 'registered')->count();
        $isFull = $event->capacity_type === 'limited' && $registeredCount >= (int) $event->capacity;
        $isClosed = $event->status === \App\Models\Event::STATUS_CLOSED;
        $isEnded = $event->isEnded();
        $isRegistrationOpen = ! $isClosed && ! $isEnded && ! $isFull;
        $remainingSeats = $event->capacity_type === 'limited' ? max((int) $event->capacity - $registeredCount, 0) : null;
        $statusBadge = match (true) {
            $isClosed => ['label' => 'Event close', 'class' => 'bg-slate-100 text-slate-600'],
            $isEnded => ['label' => 'Sudah berakhir', 'class' => 'bg-amber-50 text-amber-700'],
            $isFull => ['label' => 'Pendaftaran penuh', 'class' => 'bg-red-50 text-red-700'],
            default => ['label' => 'Tersedia', 'class' => 'bg-[#e8f8f3] text-[#159b83]'],
        };
        $descriptionHtml = trim($event->description) === strip_tags($event->description)
            ? nl2br(e($event->description))
            : $event->description;
    @endphp

    <div class="mt-6 grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_380px]">
        <div class="space-y-8">
            <article class="overflow-hidden rounded-3xl border border-[#d7eee8] bg-white shadow-sm">
                <div class="p-6 sm:p-8 lg:px-10 lg:pt-8 lg:pb-6">
                    <h1 class="event-title max-w-4xl text-2xl font-bold leading-[1.12] text-[#104b68] sm:text-3xl lg:text-4xl">{{ $event->name }}</h1>
                </div>
                <div class="grid items-start md:grid-cols-[minmax(240px,420px)_minmax(0,1fr)]">
                <div class="p-4 md:p-6">
                    <div class="mx-auto aspect-[4/5] w-full max-w-[480px] overflow-hidden rounded-2xl bg-[#e8f8f3] md:mx-0 md:max-w-none">
                        @if ($poster = $event->getFirstMediaUrl('event-posters'))
                            <button type="button" data-poster-open class="group relative block h-full w-full cursor-zoom-in text-left" aria-label="Lihat gambar {{ $event->name }}">
                                <img src="{{ $poster }}" alt="Poster {{ $event->name }}" class="h-full w-full object-contain transition duration-300 group-hover:scale-[1.02]">
                                <span class="absolute bottom-4 right-4 inline-flex items-center gap-2 rounded-full bg-[#104b68]/90 px-3 py-2 text-xs font-semibold text-white opacity-90 shadow-sm transition group-hover:bg-[#159b83]"><x-heroicon-o-magnifying-glass-plus class="h-4 w-4" /> Lihat gambar</span>
                            </button>
                        @else
                            <div class="flex h-full items-center justify-center text-sm text-[#7a9a9d]">Poster event</div>
                        @endif
                    </div>
                </div>

                <div class="p-6 sm:p-8 lg:p-10 lg:pl-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#159b83]">{{ $event->event_date->translatedFormat('d F Y') }}</p>
                    <span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
                    <div class="mt-6 space-y-5 text-sm leading-6 text-[#356b78]">
                        <p class="flex items-start gap-3"><x-heroicon-o-clock class="mt-1 h-5 w-5 shrink-0 text-[#159b83]" /><span><span class="text-[#7a9a9d]">Waktu</span><br>{{ substr($event->start_time, 0, 5) }}{{ $event->end_time ? ' – '.substr($event->end_time, 0, 5) : '' }} WIB</span></p>
                        <p class="flex items-start gap-3"><x-heroicon-o-map-pin class="mt-1 h-5 w-5 shrink-0 text-[#159b83]" /><span><span class="text-[#7a9a9d]">Lokasi</span><br>{{ $event->location_name }}@if ($event->location_address)<br>{{ $event->location_address }}@endif @if ($event->google_maps_url)<br><a class="mt-2 inline-flex items-center gap-1 font-semibold text-[#159b83] hover:underline" href="{{ $event->google_maps_url }}" target="_blank" rel="noopener">Buka Google Maps <x-heroicon-o-arrow-up-right class="h-4 w-4" /></a>@endif</span></p>
                    </div>

                    <div class="mt-6 flex items-center pt-1 text-sm">
                        @if ($isClosed)
                            <p class="flex items-center gap-2 text-slate-600"><x-heroicon-o-lock-closed class="h-5 w-5 text-slate-500" />Pendaftaran sudah ditutup</p>
                        @elseif ($isEnded)
                            <p class="flex items-center gap-2 text-amber-700"><x-heroicon-o-calendar-days class="h-5 w-5 text-amber-600" />Event sudah berakhir</p>
                        @elseif ($event->capacity_type === 'limited')
                            <p class="flex items-center gap-2 {{ $isFull ? 'text-red-600' : 'text-[#5d7d86]' }}"><x-heroicon-o-user-group class="h-5 w-5 {{ $isFull ? 'text-red-500' : 'text-[#159b83]' }}" />{{ $isFull ? 'Pendaftaran penuh' : 'Tersisa '.number_format($remainingSeats).' kursi dari '.number_format($event->capacity) }}</p>
                        @else
                            <p class="flex items-center gap-2 text-[#5d7d86]"><x-heroicon-o-user-group class="h-5 w-5 text-[#159b83]" />Pendaftaran terbuka</p>
                        @endif
                    </div>
                </div>
                </div>
            </article>

            <article class="rounded-3xl border border-[#d7eee8] bg-white p-6 shadow-sm sm:p-8 lg:p-10">
                {{-- <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#159b83]">Tentang event</p> --}}
                {{-- <h2 class="mt-2 text-2xl font-bold leading-tight text-[#104b68]">{{ $event->name }}</h2> --}}
                <div class="event-description mt-6 text-[#356b78]">{!! $descriptionHtml !!}</div>
            </article>

            <aside class="rounded-3xl border border-[#d7eee8] bg-[#e8f8f3] p-6 sm:p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#159b83]">Informasi penting</p>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-[#356b78]">
                    <li class="flex gap-2"><x-heroicon-o-chat-bubble-left-right class="mt-1 h-5 w-5 shrink-0 text-[#159b83]" /><span>Undangan dan QR Code dikirim melalui WhatsApp admin atau melalui email yang terdaftar.</span></li>
                    <li class="flex gap-2"><x-heroicon-o-qr-code class="mt-1 h-5 w-5 shrink-0 text-[#159b83]" /><span>Tunjukkan QR Code saat check-in di lokasi.</span></li>
                    <li class="flex gap-2"><x-heroicon-o-map-pin class="mt-1 h-5 w-5 shrink-0 text-[#159b83]" /><span>Pastikan detail lokasi sudah kamu cek sebelum datang.</span></li>
                </ul>
            </aside>
        </div>

        <aside id="registration-form" class="hidden scroll-mt-24 lg:sticky lg:top-6 lg:block">
            <div class="rounded-3xl border border-[#d7eee8] bg-white p-6 shadow-sm sm:p-8">
                <div class="flex items-start gap-3 border-b border-[#d7eee8] pb-5">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#e8f8f3] text-[#159b83]"><x-heroicon-o-user class="h-6 w-6" /></span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#159b83]">Pendaftaran peserta</p>
                        <h2 class="mt-1 text-2xl font-bold leading-tight text-[#104b68]">Daftar event</h2>
                    </div>
                </div>

                @if (! $isRegistrationOpen)
                    <div class="mt-6 rounded-2xl {{ $isFull ? 'bg-red-50 text-red-700' : 'bg-slate-50 text-slate-600' }} p-4 text-sm leading-6">
                        @if ($isClosed)
                            Pendaftaran untuk event ini sudah ditutup.
                        @elseif ($isFull)
                            Pendaftaran untuk event ini sudah penuh.
                        @else
                            Event ini sudah berakhir.
                        @endif
                    </div>
                @else
                    <p class="mt-5 text-sm leading-6 text-[#5d7d86]">Isi data berikut untuk mendaftar. Pastikan email yang digunakan aktif karena undangan dan QR Code akan dikirim melalui email admin.</p>
                    @if ($errors->has('event'))
                        <div class="mt-4 rounded-xl bg-red-50 p-3 text-sm text-red-700">{{ $errors->first('event') }}</div>
                    @endif
                    <form method="POST" action="{{ route('registrations.store', $event) }}" class="mt-6 space-y-4">
                        @csrf
                        <div><label class="field-label" for="name">Nama lengkap *</label><input class="field-input" id="name" name="name" value="{{ old('name') }}" required>@error('name')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div><label class="field-label" for="email">Email *</label><input class="field-input" id="email" name="email" type="email" inputmode="email" value="{{ old('email') }}" required>@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div><label class="field-label" for="phone">Nomor WhatsApp *</label><input class="field-input" id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" required>@error('phone')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div><label class="field-label" for="gender">Jenis kelamin *</label><select class="field-input" id="gender" name="gender" required><option value="">Pilih jenis kelamin</option><option value="male" @selected(old('gender') === 'male')>Laki-laki</option><option value="female" @selected(old('gender') === 'female')>Perempuan</option></select>@error('gender')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div><label class="field-label" for="city">Kota/domisili</label><input class="field-input" id="city" name="city" value="{{ old('city') }}"></div>
                        <div><label class="field-label" for="organization">Asal komunitas/instansi</label><input class="field-input" id="organization" name="organization" value="{{ old('organization') }}"></div>
                        <div><label class="field-label" for="notes">Catatan tambahan</label><textarea class="field-input" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea></div>
                        <div><label class="field-label" for="referral_source">Tahu event ini dari</label><select class="field-input" id="referral_source" name="referral_source"><option value="">Pilih sumber</option><option @selected(old('referral_source') === 'Instagram')>Instagram</option><option @selected(old('referral_source') === 'WhatsApp')>WhatsApp</option><option @selected(old('referral_source') === 'Teman')>Teman</option><option @selected(old('referral_source') === 'Komunitas')>Komunitas</option><option @selected(old('referral_source') === 'Lainnya')>Lainnya</option></select></div>
                        <label class="flex gap-3 text-sm leading-5 text-[#356b78]"><input class="mt-1 accent-[#159b83]" type="checkbox" name="data_consent" value="1" @checked(old('data_consent')) required><span>Saya menyetujui data digunakan untuk pendaftaran, komunikasi event, dan absensi.</span></label>
                        @error('data_consent')<p class="field-error">{{ $message }}</p>@enderror
                        <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#159b83] px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-[#0f826f]" type="submit"><x-heroicon-o-arrow-right class="h-5 w-5" /> Kirim pendaftaran</button>
                    </form>
                @endif
            </div>
        </aside>
    </div>
</section>

@if ($isRegistrationOpen)
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-[#cfe7e1] bg-white/95 p-3 shadow-[0_-6px_20px_rgba(16,75,104,0.08)] backdrop-blur lg:hidden">
        <a href="{{ route('events.register', $event) }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#159b83] px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-[#0f826f]"><x-heroicon-o-calendar-days class="h-5 w-5" /> Daftar event</a>
    </div>
@endif

<x-public.whatsapp-floating-button :event="$event" />

@if (isset($poster) && $poster)
    <dialog id="poster-viewer" class="poster-viewer m-auto max-h-[92vh] max-w-[min(94vw,1100px)] overflow-auto rounded-2xl border border-white/20 bg-[#104b68]/95 p-2 text-white shadow-2xl backdrop:bg-[#104b68]/70">
        <div class="relative">
            <div class="poster-viewer-stage flex min-h-[70vh] min-w-[min(88vw,1040px)] items-center justify-center overflow-auto rounded-xl bg-[#0b3b53] p-4">
                <img id="poster-image" src="{{ $poster }}" alt="Poster {{ $event->name }}" class="max-h-[82vh] max-w-full origin-center rounded-xl object-contain transition-transform duration-200">
            </div>
            <button type="button" data-poster-close aria-label="Tutup poster" class="absolute right-3 top-3 rounded-full bg-[#104b68]/85 p-2 text-white transition hover:bg-[#159b83]"><x-heroicon-o-x-mark class="h-5 w-5" /></button>
            <div class="absolute bottom-5 left-1/2 flex -translate-x-1/2 items-center gap-1 rounded-full bg-[#104b68]/90 p-1 shadow-lg">
                <button type="button" data-poster-zoom-out aria-label="Perkecil poster" class="rounded-full p-2 text-white transition hover:bg-[#159b83]"><x-heroicon-o-minus class="h-4 w-4" /></button>
                <button type="button" data-poster-zoom-reset aria-label="Reset ukuran poster" class="rounded-full p-2 text-white transition hover:bg-[#159b83]"><x-heroicon-o-arrow-path class="h-4 w-4" /></button>
                <button type="button" data-poster-zoom-in aria-label="Perbesar poster" class="rounded-full p-2 text-white transition hover:bg-[#159b83]"><x-heroicon-o-plus class="h-4 w-4" /></button>
            </div>
        </div>
    </dialog>
@endif

@if (isset($poster) && $poster)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const viewer = document.getElementById('poster-viewer');
            const posterImage = document.getElementById('poster-image');
            const openButton = document.querySelector('[data-poster-open]');
            const closeButton = document.querySelector('[data-poster-close]');
            const zoomInButton = document.querySelector('[data-poster-zoom-in]');
            const zoomOutButton = document.querySelector('[data-poster-zoom-out]');
            const zoomResetButton = document.querySelector('[data-poster-zoom-reset]');
            let zoom = 1;

            const updateZoom = () => {
                if (posterImage) posterImage.style.transform = `scale(${zoom})`;
            };

            openButton?.addEventListener('click', () => {
                zoom = 1;
                updateZoom();
                viewer?.showModal();
            });
            closeButton?.addEventListener('click', () => viewer?.close());
            zoomInButton?.addEventListener('click', () => { zoom = Math.min(zoom + 0.25, 5); updateZoom(); });
            zoomOutButton?.addEventListener('click', () => { zoom = Math.max(zoom - 0.25, 1); updateZoom(); });
            zoomResetButton?.addEventListener('click', () => { zoom = 1; updateZoom(); });
            viewer?.addEventListener('click', (event) => {
                if (event.target === viewer) viewer.close();
            });
            viewer?.addEventListener('wheel', (event) => {
                if (!viewer.open) return;
                event.preventDefault();
                zoom = Math.min(Math.max(zoom + (event.deltaY < 0 ? 0.1 : -0.1), 1), 5);
                updateZoom();
            }, { passive: false });
        });
    </script>
@endif
@endsection
