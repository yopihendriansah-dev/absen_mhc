<?php

namespace App\Services;

use App\Mail\EventInvitationMail;
use App\Models\Registration;
use Illuminate\Support\Facades\Mail;

class InvitationService
{
    public function send(Registration $registration): void
    {
        $registration->loadMissing('event');
        $registration->increment('invitation_send_count');

        try {
            Mail::to($registration->email)->send(new EventInvitationMail($registration));

            $registration->forceFill([
                'invitation_status' => Registration::INVITATION_SENT,
                'invitation_sent_at' => now(),
                'last_invitation_error' => null,
            ])->save();
        } catch (\Throwable $exception) {
            $registration->forceFill([
                'invitation_status' => Registration::INVITATION_FAILED,
                'last_invitation_error' => $exception->getMessage(),
            ])->save();

            throw $exception;
        }
    }
}
