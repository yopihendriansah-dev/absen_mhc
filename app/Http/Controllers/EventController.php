<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->whereIn('status', [Event::STATUS_PUBLISHED, Event::STATUS_CLOSED])
            ->withCount([
                'registrations as registered_registrations_count' => fn ($query) => $query->where('status', Registration::STATUS_REGISTERED),
            ])
            ->orderByRaw('event_date >= ? desc', [today()->toDateString()])
            ->orderBy('event_date')
            ->paginate(9);

        return view('events.index', compact('events'));
    }

    public function show(Event $event)
    {
        abort_unless($event->isPubliclyVisible(), 404);

        return view('events.show', compact('event'));
    }

    public function register(Event $event)
    {
        abort_unless($event->status === Event::STATUS_PUBLISHED && ! $event->isEnded(), 404);

        $registeredCount = $event->registrations()->where('status', 'registered')->count();
        $isFull = $event->capacity_type === Event::CAPACITY_LIMITED && $registeredCount >= (int) $event->capacity;

        return view('events.register', compact('event', 'isFull'));
    }
}
