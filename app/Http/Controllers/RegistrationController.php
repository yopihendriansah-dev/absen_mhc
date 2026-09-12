<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function store(Request $request, Event $event, RegistrationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'min:8', 'max:30'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'city' => ['nullable', 'string', 'max:100'],
            'organization' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'referral_source' => ['nullable', 'string', 'max:100'],
            'data_consent' => ['accepted'],
        ], [
            'data_consent.accepted' => 'Persetujuan penggunaan data wajib dicentang.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        unset($validated['data_consent']);

        $registration = $service->register($event, $validated);

        return to_route('registrations.success', $registration);
    }

    public function success(Registration $registration)
    {
        $registration->load('event');

        return view('registrations.success', compact('registration'));
    }
}
