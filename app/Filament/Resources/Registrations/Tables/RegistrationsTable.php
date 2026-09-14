<?php

namespace App\Filament\Resources\Registrations\Tables;

use App\Models\Registration;
use App\Services\WhatsAppInvitationUrlService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rowIndex')->label('No.')->rowIndex()->alignCenter(),
                TextColumn::make('name')->label('Peserta')->searchable()->sortable()->weight('semibold'),
                TextColumn::make('event.name')->label('Event')->searchable()->sortable()->wrap(),
                TextColumn::make('email')->searchable()->toggleable(),
                TextColumn::make('phone')->label('WhatsApp')->toggleable(),
                TextColumn::make('gender')->label('Jenis kelamin')->formatStateUsing(fn (?string $state): string => $state === 'male' ? 'Laki-laki' : 'Perempuan')->toggleable(),
                TextColumn::make('status')->label('Pendaftaran')->badge()->formatStateUsing(fn (?string $state): string => $state === Registration::STATUS_REGISTERED ? 'Terdaftar' : 'Dibatalkan'),
                TextColumn::make('invitation_status')->label('Undangan')->badge()->formatStateUsing(fn (?string $state): string => match ($state) {
                    Registration::INVITATION_SENT => 'Terkirim',
                    Registration::INVITATION_FAILED => 'Gagal',
                    default => 'Pending',
                }),
                TextColumn::make('attendance.checked_in_at')->label('Check-in')->dateTime('d M Y H:i')->suffix(' WIB')->placeholder('Belum hadir')->sortable(),
                TextColumn::make('created_at')->label('Tanggal daftar')->dateTime('d M Y H:i')->suffix(' WIB')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('event_id')->label('Event')->relationship('event', 'name')->searchable()->preload(),
                SelectFilter::make('status')->label('Status pendaftaran')->options([
                    Registration::STATUS_REGISTERED => 'Terdaftar',
                    Registration::STATUS_CANCELLED => 'Dibatalkan',
                ]),
                SelectFilter::make('invitation_status')->options([
                    Registration::INVITATION_PENDING => 'Pending',
                    Registration::INVITATION_SENT => 'Terkirim',
                    Registration::INVITATION_FAILED => 'Gagal',
                ]),
                SelectFilter::make('gender')->label('Jenis kelamin')->options([
                    'male' => 'Laki-laki',
                    'female' => 'Perempuan',
                ]),
                TernaryFilter::make('checked_in')
                    ->label('Status check-in')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereHas('attendance'),
                        false: fn (Builder $query): Builder => $query->whereDoesntHave('attendance'),
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Lihat detail'),
                    Action::make('downloadQrCode')
                        ->label('Download QR Code')
                        ->icon('heroicon-m-qr-code')
                        ->url(fn (Registration $record): string => route('exports.registration-qr-code', $record)),
                    Action::make('sendWhatsAppInvitation')
                        ->label('Kirim WhatsApp')
                        ->icon('heroicon-m-chat-bubble-left-right')
                        ->url(fn (Registration $record): string => app(WhatsAppInvitationUrlService::class)->make($record))
                        ->openUrlInNewTab(),
                    EditAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
