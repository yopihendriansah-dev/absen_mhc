@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-10 lg:px-8">
    <div class="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ url('/admin') }}" class="inline-flex items-center gap-2 text-sm font-medium text-[#5d7d86] transition hover:text-[#159b83]">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
                Kembali ke dashboard
            </a>
            <p class="mt-6 text-xs font-bold uppercase tracking-[0.22em] text-[#159b83]">Admin check-in</p>
            <h1 class="mt-2 text-2xl font-bold leading-tight text-[#104b68] sm:text-3xl">Scan QR Code peserta</h1>
            <p class="mt-2 max-w-xl text-sm leading-6 text-[#5d7d86]">Arahkan kamera ke QR Code pada undangan peserta untuk mencatat kehadiran.</p>
        </div>
        <div class="inline-flex w-fit items-center gap-2 rounded-full border border-[#cceae4] bg-white px-4 py-2 text-xs font-semibold text-[#3f7183] shadow-sm">
            <span class="h-2 w-2 rounded-full bg-[#159b83]"></span>
            Petugas: {{ auth()->user()->name }}
        </div>
    </div>

    <div id="scan-status" class="mb-5 hidden rounded-2xl border p-4 text-sm" role="status" aria-live="polite"></div>

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)] lg:items-start">
        <div class="rounded-3xl border border-[#d7eee8] bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-[#104b68]">Scanner kamera</h2>
                    <p id="camera-status" class="mt-1 text-xs text-[#7a9a9d]">Kamera belum dimulai.</p>
                </div>
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#e9f8f5] text-[#159b83]">
                    <x-heroicon-o-qr-code class="h-5 w-5" />
                </span>
            </div>

            <div class="relative overflow-hidden rounded-2xl bg-[#102f3d] p-2 sm:p-3">
                <div id="reader" class="min-h-[280px] overflow-hidden rounded-xl bg-[#0c2632] sm:min-h-[380px]"></div>
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                    <div class="h-48 w-48 rounded-2xl border-2 border-[#72d8c5] shadow-[0_0_0_999px_rgba(8,35,47,0.3)] sm:h-64 sm:w-64"></div>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                <button id="start-camera" type="button" class="inline-flex min-h-12 flex-1 items-center justify-center gap-2 rounded-xl bg-[#159b83] px-4 text-sm font-bold text-white shadow-sm transition hover:bg-[#0f826f] disabled:cursor-not-allowed disabled:opacity-50">
                    <x-heroicon-o-camera class="h-5 w-5" />
                    Mulai kamera
                </button>
                <button id="stop-camera" type="button" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-[#cceae4] bg-white px-4 text-sm font-bold text-[#104b68] transition hover:bg-[#f1faf8] disabled:cursor-not-allowed disabled:opacity-50" disabled>
                    <x-heroicon-o-x-mark class="h-5 w-5" />
                    Hentikan
                </button>
            </div>
            <p class="mt-4 text-center text-xs leading-5 text-[#7a9a9d]">Pastikan pencahayaan cukup dan seluruh QR Code masuk ke dalam garis panduan.</p>
        </div>

        <div class="space-y-5">
            <div class="rounded-3xl border border-[#d7eee8] bg-white p-5 shadow-sm sm:p-6">
                <div class="flex items-start gap-3">
                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#e9f8f5] text-[#159b83]">
                        <x-heroicon-o-qr-code class="h-5 w-5" />
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-[#104b68]">Input kode manual</h2>
                        <p class="mt-1 text-sm leading-6 text-[#5d7d86]">Gunakan cara ini jika kamera tidak tersedia atau QR Code sulit dibaca.</p>
                    </div>
                </div>
                <form id="manual-form" class="mt-5 space-y-3" method="POST" action="{{ route('check-in.store') }}">
                    @csrf
                    <label for="code" class="block text-xs font-bold uppercase tracking-[0.14em] text-[#3f7183]">Kode registrasi</label>
                    <input id="code" class="field-input min-h-12 w-full" name="code" placeholder="MHC-XXXXXXXXXX" autocomplete="off" required>
                    <input type="hidden" name="method" value="manual">
                    <button class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#104b68] px-4 text-sm font-bold text-white transition hover:bg-[#0b3b54]" type="submit">
                        <x-heroicon-o-check class="h-5 w-5" />
                        Check-in manual
                    </button>
                </form>
            </div>

            <div class="rounded-3xl border border-[#cceae4] bg-[#e9f8f5] p-5 sm:p-6">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-check class="mt-0.5 h-5 w-5 shrink-0 text-[#159b83]" />
                    <div class="text-sm leading-6 text-[#3f7183]">
                        <p class="font-bold text-[#104b68]">Alur check-in</p>
                        <ol class="mt-2 list-decimal space-y-1 pl-4">
                            <li>Izinkan akses kamera.</li>
                            <li>Scan QR Code peserta.</li>
                            <li>Pastikan pesan check-in berhasil muncul.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    #reader video { width: 100% !important; height: auto !important; min-height: 280px; object-fit: cover; }
    #reader__scan_region { min-height: 280px; }
    #reader__dashboard { padding: 10px 0 0 !important; }
    #reader__dashboard_section_csr button { border: 1px solid #cceae4; border-radius: 10px; padding: 8px 12px; color: #104b68; background: #fff; }
    @media (min-width: 640px) { #reader video, #reader__scan_region { min-height: 380px; } }
</style>

<script src="https://unpkg.com/html5-qrcode" defer></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const status = document.getElementById('scan-status');
    const cameraStatus = document.getElementById('camera-status');
    const startButton = document.getElementById('start-camera');
    const stopButton = document.getElementById('stop-camera');
    let scanner = null;
    let isScanning = false;
    let isSubmitting = false;

    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const showStatus = (message, type = 'error') => {
        const styles = {
            success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
            error: 'border-red-200 bg-red-50 text-red-700',
            info: 'border-[#cceae4] bg-[#e9f8f5] text-[#3f7183]',
        };
        status.textContent = message;
        status.className = `mb-5 rounded-2xl border p-4 text-sm ${styles[type] ?? styles.error}`;
        status.classList.remove('hidden');
    };

    const showAlert = (options, fallbackMessage, fallbackType = 'error') => {
        status.classList.add('hidden');

        if (window.Swal) {
            window.Swal.fire({
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#159b83',
                background: '#ffffff',
                color: '#104b68',
                customClass: { popup: 'mhc-alert-popup', confirmButton: 'mhc-alert-button' },
                ...options,
            });

            return;
        }

        showStatus(fallbackMessage, fallbackType);
    };

    const showLoading = (title = 'Memuat data peserta', text = 'Mohon tunggu sebentar.') => {
        status.classList.add('hidden');

        if (window.Swal) {
            window.Swal.fire({
                title,
                text,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                background: '#ffffff',
                color: '#104b68',
                customClass: { popup: 'mhc-alert-popup' },
                didOpen: () => window.Swal.showLoading(),
            });

            return;
        }

        showStatus(text, 'info');
    };

    const updateControls = () => {
        startButton.disabled = isScanning;
        stopButton.disabled = !isScanning;
    };

    const showSuccess = (message, participant = null, event = null) => {
        const detail = participant || event
            ? `<div style="text-align:left;line-height:1.6">
                    ${participant ? `<p style="margin:0 0 8px"><strong>Peserta:</strong> ${escapeHtml(participant)}</p>` : ''}
                    ${event ? `<p style="margin:0"><strong>Event:</strong> ${escapeHtml(event)}</p>` : ''}
                </div>`
            : escapeHtml(message);

        showAlert({
            icon: 'success',
            title: 'Check-in berhasil',
            html: detail,
            confirmButtonText: 'Selesai',
        }, message, 'success');
    };

    const showError = (message, title = 'Check-in gagal') => {
        showAlert({
            icon: 'error',
            title,
            text: message,
        }, message, 'error');
    };

    const showWarning = (message, title = 'Periksa kembali') => {
        showAlert({
            icon: 'warning',
            title,
            text: message,
        }, message, 'error');
    };

    const stopScanner = async () => {
        if (!scanner || !isScanning) return;
        try { await scanner.stop(); } catch (error) { /* Kamera mungkin sudah berhenti */ }
        isScanning = false;
        cameraStatus.textContent = 'Kamera dihentikan.';
        updateControls();
    };

    const submitCode = async (code, method = 'qr_code') => {
        if (isSubmitting) return;
        isSubmitting = true;
        const normalizedCode = String(code ?? '').trim();

        if (!normalizedCode) {
            isSubmitting = false;
            showWarning('Kode registrasi wajib diisi.');
            return;
        }

        showLoading(
            method === 'qr_code' ? 'Memproses QR Code' : 'Memuat data peserta',
            'Sedang mencocokkan data registrasi.'
        );
        const response = await fetch(@json(route('check-in.store')), {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token())},
            body: JSON.stringify({code: normalizedCode, method})
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.errors?.code?.[0] ?? data.message ?? 'Check-in gagal.');
        showSuccess(data.message, data.participant, data.event);
        cameraStatus.textContent = 'Check-in berhasil. Siap untuk scan berikutnya.';
        isSubmitting = false;
    };

    const startScanner = async (showCameraAlert = true) => {
        if (isScanning) return;
        if (typeof Html5Qrcode === 'undefined') {
            if (showCameraAlert) {
                showError('Scanner belum siap. Periksa koneksi internet atau gunakan input manual.', 'Scanner belum siap');
            }

            return;
        }
        scanner ??= new Html5Qrcode('reader');
        cameraStatus.textContent = 'Meminta izin kamera...';
        try {
            await scanner.start(
                {facingMode: 'environment'},
                {fps: 10, qrbox: (viewfinderWidth, viewfinderHeight) => {
                    const size = Math.floor(Math.min(viewfinderWidth, viewfinderHeight) * 0.68);
                    return {width: Math.max(size, 180), height: Math.max(size, 180)};
                }},
                async (text) => {
                    await stopScanner();
                    try { await submitCode(text); } catch (error) { isSubmitting = false; showError(error.message); }
                },
                () => {}
            );
            isScanning = true;
            cameraStatus.textContent = 'Kamera aktif. Arahkan ke QR Code peserta.';
            updateControls();
        } catch (error) {
            cameraStatus.textContent = 'Kamera tidak dapat digunakan.';
            if (showCameraAlert) {
                showError('Kamera tidak dapat digunakan. Pastikan izin kamera diberikan, lalu gunakan input manual jika diperlukan.', 'Kamera tidak tersedia');
            }
            isScanning = false;
            updateControls();
        }
    };

    document.getElementById('manual-form').addEventListener('submit', async (event) => {
        event.preventDefault();

        const form = event.currentTarget;
        const code = form.querySelector('[name="code"]').value;

        try {
            await submitCode(code, 'manual');
            form.reset();
        } catch (error) {
            isSubmitting = false;
            showError(error.message);
        }
    });

    startButton.addEventListener('click', () => startScanner(true));
    stopButton.addEventListener('click', stopScanner);
    updateControls();
    setTimeout(() => startScanner(false), 300);

    @if (session('success'))
        showSuccess(@json(session('success')));
    @endif
});
</script>
@endsection
