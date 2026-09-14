<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Event::create([
            'name' => 'MHC Test Event',
            'slug' => 'mhc-test-event',
            'event_date' => today()->addDay(),
            'start_time' => '10:00',
            'description' => 'Test event.',
            'location_name' => 'Test location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_public_index_shows_closed_and_ended_events_as_history(): void
    {
        Event::create([
            'name' => 'Closed History Event',
            'slug' => 'closed-history-event',
            'event_date' => today()->subDay(),
            'start_time' => '10:00',
            'description' => 'Closed event.',
            'location_name' => 'History location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_CLOSED,
        ]);

        Event::create([
            'name' => 'Ended Published Event',
            'slug' => 'ended-published-event',
            'event_date' => today()->subDays(2),
            'start_time' => '10:00',
            'description' => 'Ended event.',
            'location_name' => 'Ended location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_PUBLISHED,
        ]);

        $this->get(route('events.index'))
            ->assertOk()
            ->assertSee('Closed History Event')
            ->assertSee('Ditutup')
            ->assertSee('Ended Published Event')
            ->assertSee('Sudah berakhir');
    }

    public function test_closed_event_detail_is_visible_but_registration_page_is_not_available(): void
    {
        $event = Event::create([
            'name' => 'Closed Event Detail',
            'slug' => 'closed-event-detail',
            'event_date' => today()->subDay(),
            'start_time' => '10:00',
            'description' => 'Closed event.',
            'location_name' => 'History location',
            'capacity_type' => Event::CAPACITY_UNLIMITED,
            'status' => Event::STATUS_CLOSED,
        ]);

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('Event close')
            ->assertSee('Pendaftaran untuk event ini sudah ditutup.');

        $this->get(route('events.register', $event))->assertNotFound();
    }
}
