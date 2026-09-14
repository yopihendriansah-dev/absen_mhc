<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CheckInService
{
    public function checkIn(string $code, User $user, string $method = 'qr_code'): Attendance
    {
        $registration = Registration::query()
            ->with('event')
            ->where('registration_code', trim($code))
            ->where('status', Registration::STATUS_REGISTERED)
            ->first();

        if (! $registration) {
            throw ValidationException::withMessages([
                'code' => 'Kode registrasi tidak valid atau sudah tidak aktif.',
            ]);
        }

        if ($registration->attendance()->exists()) {
            throw ValidationException::withMessages([
                'code' => 'Peserta sudah melakukan check-in sebelumnya.',
            ]);
        }

        return $registration->attendance()->create([
            'checked_in_by' => $user->id,
            'checked_in_at' => now(),
            'check_in_method' => $method,
        ]);
    }
}
