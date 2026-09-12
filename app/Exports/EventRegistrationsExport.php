<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventRegistrationsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private readonly Event $event) {}

    public function query()
    {
        return $this->event->registrations()->orderBy('name')->getQuery();
    }

    public function headings(): array
    {
        return ['Nama', 'Email', 'WhatsApp', 'Jenis Kelamin', 'Kota', 'Organisasi', 'Status Undangan', 'Kode Registrasi', 'Terdaftar Pada'];
    }

    public function map($registration): array
    {
        return [
            $registration->name,
            $registration->email,
            $registration->phone,
            $registration->gender === 'male' ? 'Laki-laki' : 'Perempuan',
            $registration->city,
            $registration->organization,
            $registration->invitation_status,
            $registration->registration_code,
            $registration->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
