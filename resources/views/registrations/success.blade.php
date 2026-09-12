@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-xl px-4 py-16 text-center sm:py-24">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#d9f4ec] text-[#159b83]"><x-heroicon-o-check class="h-8 w-8" /></div>
    <p class="mt-6 text-sm font-semibold uppercase tracking-[0.2em] text-[#159b83]">Pendaftaran berhasil</p>
    <h1 class="mt-3 text-3xl font-bold text-[#104b68] sm:text-4xl">Terima kasih, {{ $registration->name }}!</h1>
    <p class="mt-5 leading-7 text-[#5d7d86]">Kamu sudah terdaftar di <span class="font-semibold text-[#104b68]">{{ $registration->event->name }}</span>. Undangan berisi QR Code akan dikirimkan oleh admin ke email <span class="text-[#104b68]">{{ $registration->email }}</span>.</p>
    @if ($registration->event->whatsapp_group_url)
        <div class="mt-8 rounded-2xl border border-[#b9e7db] bg-[#e8f8f3] p-6">
            <h2 class="font-semibold text-[#146e67]">Yuk bergabung ke grup WhatsApp event</h2>
            <p class="mt-2 text-sm text-[#4c8b8a]">Dapatkan informasi dan update terbaru dari panitia.</p>
            <a href="{{ $registration->event->whatsapp_group_url }}" target="_blank" rel="noopener" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#159b83] px-5 py-3 font-semibold text-white hover:bg-[#0f826f]">Gabung grup WhatsApp <x-heroicon-o-arrow-up-right class="h-4 w-4" /></a>
        </div>
    @endif
    <p class="mt-8 text-xs text-[#7a9a9d]">Kode pendaftaran kamu: <span class="font-mono text-[#356b78]">{{ $registration->registration_code }}</span></p>
</section>
@endsection
