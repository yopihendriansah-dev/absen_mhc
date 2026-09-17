<?php

namespace App\Services;

use App\Models\ContactSetting;
use App\Models\Event;

class AdminContactService
{
    public function whatsappUrl(?Event $event = null): ?string
    {
        $setting = ContactSetting::query()->first();
        $phone = $this->normalizePhoneNumber((string) $setting?->admin_whatsapp_number);

        if ($phone === '') {
            return null;
        }

        $adminName = trim((string) $setting?->admin_name);
        $greetingName = $adminName !== '' ? $adminName : 'admin MHC';
        $message = $event
            ? 'Halo '.$greetingName.', saya ingin bertanya tentang event '.$event->name.'.'
            : 'Halo '.$greetingName.', saya ingin bertanya tentang event komunitas MHC.';

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
}
