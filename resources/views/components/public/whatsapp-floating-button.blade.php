@props(['event' => null])

@php
    $url = app(\App\Services\AdminContactService::class)->whatsappUrl($event);
@endphp

@if ($url)
    <a
        href="{{ $url }}"
        target="_blank"
        rel="noopener"
        aria-label="Hubungi admin MHC via WhatsApp"
        class="fixed right-4 {{ $event ? 'bottom-24 lg:bottom-6' : 'bottom-6' }} z-50 transition hover:scale-110 sm:right-6"
    >
        <img src="{{ asset('images/logowa.webp') }}" alt="WhatsApp" class="h-[55px] w-[55px] object-contain">
    </a>
@endif
