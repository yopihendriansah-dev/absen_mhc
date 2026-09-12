<?php

namespace App\Filament\Resources\Registrations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')->label('Event')->relationship('event', 'name')->searchable()->preload()->required()->disabled(),
                TextInput::make('name')->label('Nama lengkap')->required(),
                TextInput::make('email')->email()->required(),
                TextInput::make('phone')->label('Nomor WhatsApp')->required(),
                Select::make('gender')->label('Jenis kelamin')->options(['male' => 'Laki-laki', 'female' => 'Perempuan'])->required(),
                TextInput::make('city')->label('Kota/domisi'),
                TextInput::make('organization')->label('Asal komunitas/instansi'),
                Textarea::make('notes')->label('Catatan tambahan')->columnSpanFull(),
                Select::make('status')->options(['registered' => 'Terdaftar', 'cancelled' => 'Dibatalkan'])->default('registered')->required(),
            ]);
    }
}
