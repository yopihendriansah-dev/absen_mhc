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
}
