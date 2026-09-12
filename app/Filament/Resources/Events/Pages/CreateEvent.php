<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function getRedirectUrl(): string
    {
        return EventResource::getUrl('index');
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Event berhasil dibuat')
            ->body('Link pendaftaran: '.route('events.show', ['event' => $this->record->slug]))
            ->success()
            ->send();
    }
}
