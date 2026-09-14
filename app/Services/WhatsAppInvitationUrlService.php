<?php

namespace App\Services;

use App\Models\Registration;

class WhatsAppInvitationUrlService
{
    public function make(Registration $registration): string
    {
        $registration->loadMissing('event');

        $phone = $this->normalizePhoneNumber($registration->phone);
        $message = $this->makeMessage($registration);

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }

    private function normalizePhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    private function makeMessage(Registration $registration): string
    {
        $event = $registration->event;
        $time = substr((string) $event->start_time, 0, 5).($event->end_time ? ' - '.substr((string) $event->end_time, 0, 5) : '').' WIB';

        $lines = [
            'Halo '.$registration->name.',',
            '',
            'Terima kasih sudah mendaftar event MHC.',
            '',
            'Event: '.$event->name,
            'Tanggal: '.$event->event_date->translatedFormat('d F Y'),
            'Waktu: '.$time,
            'Lokasi: '.$event->location_name,
        ];

        if ($event->location_address) {
            $lines[] = $event->location_address;
        }

        if ($event->google_maps_url) {
            $lines[] = '';
            $lines[] = 'Google Maps: '.$event->google_maps_url;
        }

        $lines[] = '';
        $lines[] = 'Download QR Code check-in: '.route('registrations.qr-code', $registration);
        $lines[] = 'Tunjukkan QR Code tersebut saat check-in di lokasi.';

        if ($event->whatsapp_group_url) {
            $lines[] = '';
            $lines[] = 'Grup WhatsApp event: '.$event->whatsapp_group_url;
        }

        return implode("\n", $lines);
    }
}
