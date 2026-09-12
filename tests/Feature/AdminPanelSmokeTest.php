<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
