<?php

namespace App\Exports;

use App\Models\Event;
use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventAttendancesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private readonly Event $event) {}

    public function query()
    {
        return Registration::query()
            ->with('attendance.checkedInBy')
            ->where('event_id', $this->event->getKey())
            ->whereHas('attendance')
            ->orderBy('name');
    }

    public function headings(): array
    {
        return ['Nama', 'Email', 'WhatsApp', 'Kode Registrasi', 'Check-in Pada', 'Metode', 'Admin'];
    }

    public function map($registration): array
    {
        return [
            $registration->name,
            $registration->email,
            $registration->phone,
            $registration->registration_code,
            $registration->attendance->checked_in_at?->format('Y-m-d H:i:s'),
            $registration->attendance->check_in_method,
            $registration->attendance->checkedInBy?->name,
        ];
    }
}
