<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Events\Tables\EventRegistrationsTable;
use App\Filament\Resources\Registrations\RegistrationResource;
use App\Filament\Resources\Registrations\Schemas\RegistrationForm;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $relationshipTitle = 'Peserta';

    protected static string | BackedEnum | null $icon = Heroicon::OutlinedUserGroup;

    protected static ?string $badgeColor = 'info';

    /**
     * Tampilkan jumlah peserta pada tab, disesuaikan dengan isi tabel.
     */
    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return (string) $ownerRecord->registrations()->count();
    }

    public function form(Schema $schema): Schema
    {
        return RegistrationForm::configure($schema);
    }

    public function infolist(Schema $schema): Schema
    {
        return RegistrationResource::infolist($schema);
    }

    public function table(Table $table): Table
    {
        return EventRegistrationsTable::configure($table);
    }
}
