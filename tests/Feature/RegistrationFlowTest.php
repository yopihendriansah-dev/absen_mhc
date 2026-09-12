<?php

namespace Tests\Feature;

use App\Mail\EventInvitationMail;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_can_register_and_see_whatsapp_invitation(): void
    {
        $event = $this->event(['whatsapp_group_url' => 'https://chat.whatsapp.com/example']);

        $response = $this->post(route('registrations.store', $event), [
            'name' => 'Budi MHC',
            'email' => 'BUDI@example.com',
            'phone' => '08123456789',
            'gender' => 'male',
            'data_consent' => '1',
        ]);

        $registration = Registration::firstOrFail();

        $response->assertRedirect(route('registrations.success', $registration));
        $this->get(route('registrations.success', $registration))
            ->assertOk()
            ->assertSee('Terima kasih, Budi MHC!')
            ->assertSee('https://chat.whatsapp.com/example');
        $this->assertSame('budi@example.com', $registration->email);
    }

    public function test_email_can_only_register_once_per_event_but_can_register_again_on_other_event(): void
    {
        $event = $this->event();
        $payload = [
            'name' => 'Sari',
            'email' => 'sari@example.com',
            'phone' => '08123456789',
            'gender' => 'female',
            'data_consent' => '1',
        ];

        $this->post(route('registrations.store', $event), $payload)->assertRedirect();
        $this->from(route('events.show', $event))
            ->post(route('registrations.store', $event), $payload)
            ->assertSessionHasErrors('email');

        $otherEvent = $this->event(['slug' => 'other-event', 'name' => 'Other Event']);
        $this->post(route('registrations.store', $otherEvent), $payload)->assertRedirect();
        $this->assertDatabaseCount('registrations', 2);
    }

    public function test_limited_event_rejects_registration_when_full(): void
    {
        $event = $this->event(['capacity_type' => Event::CAPACITY_LIMITED, 'capacity' => 1]);
        $payload = [
            'name' => 'Peserta Pertama', 'email' => 'first@example.com', 'phone' => '08123456789',
            'gender' => 'male', 'data_consent' => '1',
        ];
        $this->post(route('registrations.store', $event), $payload)->assertRedirect();

        $this->post(route('registrations.store', $event), [
            ...$payload, 'name' => 'Peserta Kedua', 'email' => 'second@example.com',
        ])->assertSessionHasErrors('event');
    }

    public function test_admin_can_send_invitation_and_check_in_once(): void
    {
        Mail::fake();
        $admin = User::factory()->create();
        $event = $this->event();
        $registration = Registration::create([
            'event_id' => $event->id,
            'registration_code' => 'MHC-TESTCODE01',
            'name' => 'Dina',
            'email' => 'dina@example.com',
            'phone' => '0812',
            'gender' => 'female',
            'data_consent_at' => now(),
        ]);

        app(InvitationService::class)->send($registration);
        Mail::assertSent(EventInvitationMail::class);
        $this->assertSame(Registration::INVITATION_SENT, $registration->fresh()->invitation_status);

        $this->actingAs($admin)
            ->postJson(route('check-in.store'), ['code' => $registration->registration_code])
            ->assertOk()
            ->assertJsonPath('participant', 'Dina');
        $this->assertDatabaseCount('attendances', 1);

        $this->actingAs($admin)
            ->postJson(route('check-in.store'), ['code' => $registration->registration_code])
            ->assertStatus(422);
        $this->assertInstanceOf(Attendance::class, $registration->fresh()->attendance);
    }

    public function test_invitation_email_embeds_qr_code_as_inline_image(): void
    {
        $registration = Registration::create([
            'event_id' => $this->event()->id,
            'registration_code' => 'MHC-INLINEQR01',
            'name' => 'Dina',
            'email' => 'dina@example.com',
            'phone' => '0812',
            'gender' => 'female',
            'data_consent_at' => now(),
        ]);

        $mail = new EventInvitationMail($registration->load('event'));
        $html = $mail->render();

        $this->assertStringContainsString('cid:'.$mail->qrCodeCid, $html);
        $this->assertStringContainsString('cid:'.$mail->logoCid, $html);
        $this->assertStringNotContainsString('data:image/png;base64,', $html);

        $mail->build();
        $message = new Email;
        foreach ($mail->callbacks as $callback) {
            $callback($message);
        }

        $this->assertSame($mail->qrCodeCid, $message->getAttachments()[0]->getContentId());
        $this->assertSame($mail->logoCid, $message->getAttachments()[1]->getContentId());
    }

    private function event(array $overrides = []): Event
    {
        return Event::create(array_merge([
            'name' => 'MHC Test Event',
            'slug' => 'mhc-test-event-'.fake()->unique()->slug(),
            'event_date' => today()->addDay(),
            'start_time' => '10:00',
            'description' => 'Test event.',
            'location_name' => 'Test location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ], $overrides));
    }
}
