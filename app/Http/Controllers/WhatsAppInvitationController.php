<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\WhatsAppInvitationUrlService;
use Illuminate\Http\RedirectResponse;

class WhatsAppInvitationController extends Controller
{
    public function __invoke(Registration $registration, WhatsAppInvitationUrlService $service): RedirectResponse
    {
        $registration->forceFill([
            'whatsapp_invitation_sent_at' => now(),
            'whatsapp_invitation_send_count' => $registration->whatsapp_invitation_send_count + 1,
        ])->save();

        return redirect()->away($service->make($registration));
    }
}
