<?php

namespace App\Filament\Resources\Events\Tables;

use App\Models\Event;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No.')
                    ->rowIndex()
                    ->alignCenter(),
                TextColumn::make('name')->label('Event')->searchable()->sortable()->wrap(),
                TextColumn::make('event_date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        Event::STATUS_DRAFT => 'Draft',
                        Event::STATUS_PUBLISHED => 'Published',
                        Event::STATUS_CLOSED => 'Closed',
                        default => ucfirst((string) $state),
                    }),
                TextColumn::make('capacity')->label('Kapasitas')->state(fn (Event $record): string => $record->capacity_type === Event::CAPACITY_UNLIMITED ? 'Unlimited' : (string) $record->capacity),
                TextColumn::make('registrations_count')->label('Peserta')->counts('registrations')->sortable(),
                TextColumn::make('registration_link')
                    ->label('Link pendaftaran')
                    ->state('Salin link')
                    ->icon('heroicon-m-clipboard-document')
                    ->copyable()
                    ->copyableState(fn (Event $record): string => route('events.show', ['event' => $record->slug]))
                    ->copyMessage('Link pendaftaran disalin')
                    ->copyMessageDuration(1500)
                    ->color('primary')
                    ->weight('semibold'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    Event::STATUS_DRAFT => 'Draft',
                    Event::STATUS_PUBLISHED => 'Published',
                    Event::STATUS_CLOSED => 'Closed',
                ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('publish')
                        ->label('Publish')
                        ->color('success')
                        ->visible(fn (Event $record): bool => $record->status === Event::STATUS_DRAFT)
                        ->requiresConfirmation()
                        ->action(fn (Event $record) => $record->update(['status' => Event::STATUS_PUBLISHED])),
                    Action::make('exportParticipants')
                        ->label('Export peserta')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->url(fn (Event $record): string => route('exports.registrations', $record))
                        ->openUrlInNewTab(),
                    Action::make('exportAttendances')
                        ->label('Export kehadiran')
                        ->icon('heroicon-m-clipboard-document-check')
                        ->url(fn (Event $record): string => route('exports.attendances', $record))
                        ->openUrlInNewTab(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
