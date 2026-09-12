<?php

namespace App\Http\Controllers;

use App\Exports\EventAttendancesExport;
use App\Exports\EventRegistrationsExport;
use App\Models\Event;
use App\Models\Registration;
use App\Services\RegistrationQrCodeService;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function registrations(Event $event)
    {
        return Excel::download(
            new EventRegistrationsExport($event),
            'peserta-'.str($event->slug)->slug().'.xlsx'
        );
    }

    public function attendances(Event $event)
    {
        return Excel::download(
            new EventAttendancesExport($event),
            'kehadiran-'.str($event->slug)->slug().'.xlsx'
        );
    }

    public function registrationQrCode(Registration $registration, RegistrationQrCodeService $qrCodeService)
    {
        $image = $qrCodeService->generate($registration);

        $filename = str($registration->name)->slug().'-'.str($registration->event->name)->slug().'.png';

        return response($image, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
