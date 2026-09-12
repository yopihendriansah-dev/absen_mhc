<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\CheckInService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CheckInController extends Controller
{
    public function index()
    {
        return view('check-in.index');
    }

    public function store(Request $request, CheckInService $service): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100'],
            'method' => ['nullable', 'in:qr_code,manual'],
        ]);

        try {
            $attendance = $service->checkIn(
                $validated['code'],
                $request->user(),
                $validated['method'] ?? 'qr_code',
            );

            $attendance->load('registration.event');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Check-in berhasil.',
                    'participant' => $attendance->registration->name,
                    'event' => $attendance->registration->event->name,
                    'checked_in_at' => $attendance->checked_in_at->format('d M Y H:i'),
                ]);
            }

            return back()->with('success', 'Check-in berhasil untuk '.$attendance->registration->name.'.');
        } catch (ValidationException $exception) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $exception->getMessage(), 'errors' => $exception->errors()], 422);
            }

            throw $exception;
        }
    }

    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate(['query' => ['required', 'string', 'max:255']]);

        $registrations = Registration::query()
            ->with('event')
            ->where(function ($query) use ($validated): void {
                $query->where('registration_code', $validated['query'])
                    ->orWhere('email', 'like', '%'.$validated['query'].'%')
                    ->orWhere('name', 'like', '%'.$validated['query'].'%');
            })
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (Registration $registration): array => [
                'code' => $registration->registration_code,
                'name' => $registration->name,
                'email' => $registration->email,
                'event' => $registration->event->name,
                'checked_in' => $registration->attendance()->exists(),
            ]);

        return response()->json($registrations);
    }
}
