<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'MHC Community' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Mental Health Community Priangan Timur — temukan event dan pendaftaran komunitas.' }}">
    {{-- Open Graph default (fallback). Halaman detail event menimpa via @push('meta'). --}}
    <meta property="og:site_name" content="MHC Community">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:title" content="{{ $ogTitle ?? ($title ?? 'MHC Community') }}">
    <meta property="og:description" content="{{ $ogDescription ?? ($metaDescription ?? 'Mental Health Community Priangan Timur — temukan event dan pendaftaran komunitas.') }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/logo-mhc.jpg') }}">
    @if (! empty($ogImageWidth) && ! empty($ogImageHeight))
        <meta property="og:image:width" content="{{ $ogImageWidth }}">
        <meta property="og:image:height" content="{{ $ogImageHeight }}">
    @endif
    <meta property="og:url" content="{{ $ogUrl ?? url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle ?? ($title ?? 'MHC Community') }}">
    <meta name="twitter:description" content="{{ $ogDescription ?? ($metaDescription ?? 'Mental Health Community Priangan Timur — temukan event dan pendaftaran komunitas.') }}">
    <meta name="twitter:image" content="{{ $ogImage ?? asset('images/logo-mhc.jpg') }}">
    @stack('meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f7fbfa] text-[#104b68] antialiased">
    <header class="fixed inset-x-0 top-0 z-50 border-b border-[#d7eee8] bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex min-h-16 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3">
                <img src="{{ asset('images/logo-mhc.jpg') }}" alt="Mental Health Community Priangan Timur" class="h-10 w-10 rounded-lg object-cover sm:h-11 sm:w-11">
                <span class="leading-tight">
                    <span class="block text-[0.8rem] font-bold uppercase tracking-[0.08em] text-[#104b68] sm:hidden">MHC Community</span>
                    <span class="hidden text-sm font-bold uppercase tracking-[0.08em] text-[#104b68] sm:block">Mental Health Community</span>
                    <span class="mt-0.5 block text-[0.55rem] font-semibold uppercase tracking-[0.16em] text-[#159b83] sm:text-[0.6rem]">Priangan Timur</span>
                </span>
            </a>

        </div>
    </header>

    <main class="pt-16">
        @if (session('success'))
            <div class="mx-auto mt-4 max-w-2xl px-4">
                <div class="rounded-xl border border-emerald-400/30 bg-emerald-400/10 p-4 text-sm text-emerald-200">{{ session('success') }}</div>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
