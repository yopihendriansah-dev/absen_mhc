@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-xl px-4 py-8 pb-12 sm:px-6 lg:py-12">
    <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-2 text-sm text-[#5d7d86] transition hover:text-[#159b83]"><x-heroicon-o-arrow-left class="h-4 w-4" /> Kembali ke detail event</a>

    <div class="mt-6 rounded-3xl border border-[#d7eee8] bg-white p-6 shadow-sm sm:p-8">
        <div class="flex items-start gap-3 border-b border-[#d7eee8] pb-5">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#e8f8f3] text-[#159b83]"><x-heroicon-o-user class="h-6 w-6" /></span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#159b83]">Pendaftaran peserta</p>
                <h2 class="mt-1 text-2xl font-bold leading-tight text-[#104b68]">Daftar event</h2>
                <p class="mt-1 text-sm text-[#5d7d86]">{{ $event->name }}</p>
            </div>
        </div>

        @if ($isFull)
            <div class="mt-6 rounded-2xl bg-red-50 p-4 text-sm leading-6 text-red-700">Pendaftaran untuk event ini sudah penuh.</div>
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
</section>
@endsection
