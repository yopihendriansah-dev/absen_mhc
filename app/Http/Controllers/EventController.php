<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $events = Event::query()
            ->whereIn('status', [Event::STATUS_PUBLISHED, Event::STATUS_CLOSED])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhere('location_name', 'like', '%'.$search.'%')
                        ->orWhere('location_address', 'like', '%'.$search.'%');
                });
            })
            ->withCount([
                'registrations as registered_registrations_count' => fn ($query) => $query->where('status', Registration::STATUS_REGISTERED),
            ])
            ->latest('event_date')
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        return view('events.index', compact('events', 'search'));
    }

    public function show(Event $event)
    {
        abort_unless($event->isPubliclyVisible(), 404);

        // Open Graph untuk preview link WhatsApp/Telegram/FB.
        // Gambar = poster asli apa adanya (tanpa crop), fallback ke logo.
        $posterUrl = $event->getFirstMediaUrl('event-posters') ?: asset('images/logo-mhc.jpg');
        $plainDescription = \Illuminate\Support\Str::of(strip_tags($event->description ?? ''))->squish()->limit(160)->toString();
        $eventDate = $event->event_date ? $event->event_date->translatedFormat('d F Y') : null;

        return view('events.show', [
            'event' => $event,
            'title' => $event->name.' — MHC Community',
            'metaDescription' => $plainDescription !== '' ? $plainDescription : $event->name.($eventDate ? ' — '.$eventDate : ''),
            'ogType' => 'article',
            'ogTitle' => $event->name,
            'ogDescription' => $plainDescription !== '' ? $plainDescription : $event->name.($eventDate ? ' — '.$eventDate : ''),
            'ogImage' => $posterUrl,
            'ogUrl' => route('events.show', $event),
        ]);
    }

    public function register(Event $event)
    {
        abort_unless($event->status === Event::STATUS_PUBLISHED && ! $event->isEnded(), 404);

        $registeredCount = $event->registrations()->where('status', 'registered')->count();
        $isFull = $event->capacity_type === Event::CAPACITY_LIMITED && $registeredCount >= (int) $event->capacity;

        return view('events.register', compact('event', 'isFull'));
    }
}
