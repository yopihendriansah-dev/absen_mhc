<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('registration.name')->label('Peserta')->disabled(),
                TextInput::make('registration.event.name')->label('Event')->disabled(),
                DateTimePicker::make('checked_in_at')->label('Waktu check-in')->disabled(),
                TextInput::make('check_in_method')->label('Metode')->disabled(),
            ]);
    }
}
