<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
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
}
