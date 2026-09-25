<?php

namespace Tests\Feature;

use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\RelationManagers\RegistrationsRelationManager;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_admin_can_open_event_and_registration_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/events')->assertOk();
        $this->actingAs($user)->get('/admin/registrations')->assertOk();
        $this->actingAs($user)->get('/admin/attendances')->assertOk();
    }

    public function test_event_edit_page_contains_participant_tab_and_only_related_registrations(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'name' => 'Event Tab Peserta',
            'slug' => 'event-tab-peserta',
            'event_date' => now()->addWeek(),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'description' => 'Deskripsi event untuk pengujian.',
            'location_name' => 'Aula Komunitas',
            'location_address' => 'Tasikmalaya',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ]);
        $otherEvent = Event::create([
            'name' => 'Event Lain',
            'slug' => 'event-lain',
            'event_date' => now()->addWeeks(2),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'description' => 'Deskripsi event lain.',
            'location_name' => 'Aula Komunitas',
            'location_address' => 'Tasikmalaya',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $registration = Registration::create([
            'event_id' => $event->id,
            'registration_code' => 'TAB-PESERTA-001',
            'name' => 'Peserta Event Ini',
            'email' => 'peserta.event.ini@example.com',
            'phone' => '081234567890',
            'gender' => 'female',
            'data_consent_at' => now(),
            'status' => Registration::STATUS_REGISTERED,
            'invitation_status' => Registration::INVITATION_PENDING,
        ]);
        $otherRegistration = Registration::create([
            'event_id' => $otherEvent->id,
            'registration_code' => 'TAB-PESERTA-002',
            'name' => 'Peserta Event Lain',
            'email' => 'peserta.event.lain@example.com',
            'phone' => '081234567891',
            'gender' => 'male',
            'data_consent_at' => now(),
            'status' => Registration::STATUS_REGISTERED,
            'invitation_status' => Registration::INVITATION_PENDING,
        ]);

        $this->actingAs($user)
            ->get("/admin/events/{$event->getRouteKey()}/edit")
            ->assertOk()
            ->assertSee('Detail event')
            ->assertSee('Peserta');

        Livewire::test(RegistrationsRelationManager::class, [
            'ownerRecord' => $event,
            'pageClass' => EditEvent::class,
        ])
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('status')
            ->assertCountTableRecords(1)
            ->assertCanSeeTableRecords([$registration])
            ->assertCanNotSeeTableRecords([$otherRegistration])
            ->assertSee('Peserta Event Ini')
            ->assertDontSee('Peserta Event Lain');
    }
}
