<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegistrationService
{
    public function register(Event $event, array $data): Registration
    {
        return DB::transaction(function () use ($event, $data): Registration {
            $event = Event::query()->lockForUpdate()->findOrFail($event->id);

            if ($event->status !== Event::STATUS_PUBLISHED || $event->isEnded()) {
                throw ValidationException::withMessages([
                    'event' => 'Pendaftaran untuk event ini sudah tidak tersedia.',
                ]);
            }

            if (! $event->hasAvailableCapacity()) {
                throw ValidationException::withMessages([
                    'event' => 'Maaf, kapasitas peserta untuk event ini sudah penuh.',
                ]);
            }

            $email = Str::lower(trim($data['email']));

            if ($event->registrations()->where('email', $email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'Email ini sudah terdaftar pada event yang sama.',
                ]);
            }

            return $event->registrations()->create([
                ...$data,
                'email' => $email,
                'registration_code' => $this->makeRegistrationCode(),
                'data_consent_at' => now(),
                'status' => Registration::STATUS_REGISTERED,
                'invitation_status' => Registration::INVITATION_PENDING,
            ]);
        });
    }

    private function makeRegistrationCode(): string
    {
        do {
            $code = 'MHC-'.strtoupper(Str::random(10));
        } while (Registration::query()->where('registration_code', $code)->exists());

        return $code;
    }
}
