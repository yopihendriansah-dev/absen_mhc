<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->where('status', Event::STATUS_PUBLISHED)
            ->whereDate('event_date', '>=', today())
            ->orderBy('event_date')
            ->paginate(9);

        return view('events.index', compact('events'));
    }

    public function show(Event $event)
    {
        abort_unless($event->status === Event::STATUS_PUBLISHED, 404);

        return view('events.show', compact('event'));
    }

    public function register(Event $event)
    {
        abort_unless($event->status === Event::STATUS_PUBLISHED, 404);

        $registeredCount = $event->registrations()->where('status', 'registered')->count();
        $isFull = $event->capacity_type === Event::CAPACITY_LIMITED && $registeredCount >= (int) $event->capacity;

        return view('events.register', compact('event', 'isFull'));
    }
}
