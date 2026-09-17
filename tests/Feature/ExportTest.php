<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Services\WhatsAppInvitationUrlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_registration_qr_code(): void
    {
        $admin = User::factory()->create();
        $event = Event::create([
            'name' => 'MHC Export Event',
            'slug' => 'mhc-export-event',
            'event_date' => today()->addDay(),
            'start_time' => '10:00',
            'description' => 'Test event.',
            'location_name' => 'Test location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ]);
        $registration = Registration::create([
            'event_id' => $event->id,
            'registration_code' => 'MHC-TESTCODE01',
            'name' => 'Dina',
            'email' => 'dina@example.com',
            'phone' => '0812',
            'gender' => 'female',
            'data_consent_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('exports.registration-qr-code', $registration));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('Content-Disposition', 'attachment; filename="dina-mhc-export-event.png"');
    }

    public function test_participant_qr_code_can_be_downloaded_from_public_link(): void
    {
        $registration = $this->registration();

        $this->get(route('registrations.qr-code', $registration))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('Content-Disposition', 'attachment; filename="dina-mhc-export-event.png"');
    }

    public function test_whatsapp_invitation_url_contains_downloadable_qr_link(): void
    {
        $registration = $this->registration(['phone' => '0812 3456 7890']);

        $url = app(WhatsAppInvitationUrlService::class)->make($registration);

        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url);
        $this->assertStringContainsString(rawurlencode(route('registrations.qr-code', $registration)), $url);
    }

    public function test_admin_whatsapp_invitation_redirect_marks_registration_as_sent(): void
    {
        $admin = User::factory()->create();
        $registration = $this->registration(['phone' => '0812 3456 7890']);

        $this->actingAs($admin)
            ->get(route('admin.invitations.whatsapp', $registration))
            ->assertRedirectContains('https://wa.me/6281234567890');

        $registration->refresh();

        $this->assertNotNull($registration->whatsapp_invitation_sent_at);
        $this->assertSame(1, $registration->whatsapp_invitation_send_count);
    }

    private function registration(array $overrides = []): Registration
    {
        $event = Event::create([
            'name' => 'MHC Export Event',
            'slug' => 'mhc-export-event',
            'event_date' => today()->addDay(),
            'start_time' => '10:00',
            'description' => 'Test event.',
            'location_name' => 'Test location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        return Registration::create(array_merge([
            'event_id' => $event->id,
            'registration_code' => 'MHC-TESTCODE01',
            'name' => 'Dina',
            'email' => 'dina@example.com',
            'phone' => '0812',
            'gender' => 'female',
            'data_consent_at' => now(),
        ], $overrides));
    }
}
