<?php

namespace App\Http\Middleware;

use App\Models\Event;
use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            $event = $request->event instanceof Event ? $request->event : null;

            PageView::create([
                'url' => $request->fullUrl(),
                'session_id' => $request->getSession()?->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'event_id' => $event?->id,
            ]);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $routeName = $request->route()?->getName();

        if ($routeName !== 'events.show') {
            return false;
        }

        return true;
    }
}
