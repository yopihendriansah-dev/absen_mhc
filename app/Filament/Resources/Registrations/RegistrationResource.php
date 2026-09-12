<?php

namespace App\Filament\Resources\Registrations;

use App\Filament\Resources\Registrations\Pages\EditRegistration;
use App\Filament\Resources\Registrations\Pages\ListRegistrations;
use App\Filament\Resources\Registrations\Schemas\RegistrationForm;
use App\Filament\Resources\Registrations\Tables\RegistrationsTable;
use App\Models\Registration;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Peserta';

    protected static ?string $modelLabel = 'Peserta';

    protected static ?string $pluralModelLabel = 'Peserta';

    public static function form(Schema $schema): Schema
    {
        return RegistrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data peserta')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')->label('Nama lengkap'),
                        TextEntry::make('gender')->label('Jenis kelamin')->formatStateUsing(fn (?string $state): string => $state === 'male' ? 'Laki-laki' : 'Perempuan'),
                        TextEntry::make('email')->label('Email'),
                        TextEntry::make('phone')->label('WhatsApp'),
                        TextEntry::make('city')->label('Kota/domisili')->placeholder('—'),
                        TextEntry::make('organization')->label('Asal komunitas/instansi')->placeholder('—'),
                    ]),
                Section::make('Status event')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('event.name')->label('Event'),
                        TextEntry::make('registration_code')->label('Kode registrasi')->copyable(),
                        TextEntry::make('status')->label('Status pendaftaran')->badge(),
                        TextEntry::make('invitation_status')->label('Status undangan')->badge(),
                        TextEntry::make('created_at')->label('Tanggal daftar')->dateTime('d M Y H:i')->suffix(' WIB'),
                        TextEntry::make('attendance.checked_in_at')->label('Waktu check-in')->dateTime('d M Y H:i')->suffix(' WIB')->placeholder('Belum hadir'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return RegistrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegistrations::route('/'),
            'edit' => EditRegistration::route('/{record}/edit'),
        ];
    }
}
