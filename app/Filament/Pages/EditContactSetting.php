<?php

namespace App\Filament\Pages;

use App\Models\ContactSetting;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EditContactSetting extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static ?string $navigationLabel = 'Kontak Admin';

    protected static ?string $title = 'Kontak Admin';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.edit-contact-setting';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(ContactSetting::singleton()->only([
            'admin_name',
            'admin_whatsapp_number',
        ]));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Data kontak')
                    ->description('Kontak ini dipakai untuk tombol WhatsApp floating di halaman public event.')
                    ->schema([
                        TextInput::make('admin_name')
                            ->label('Nama admin')
                            ->placeholder('Admin MHC')
                            ->maxLength(255),
                        TextInput::make('admin_whatsapp_number')
                            ->label('Nomor WhatsApp admin')
                            ->placeholder('0812 3456 7890')
                            ->tel()
                            ->maxLength(30)
                            ->helperText('Gunakan nomor aktif. Format 08..., 628..., atau +62... bisa dipakai.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        ContactSetting::singleton()->update($this->form->getState());

        Notification::make()
            ->title('Kontak admin berhasil disimpan.')
            ->success()
            ->send();
    }
}
