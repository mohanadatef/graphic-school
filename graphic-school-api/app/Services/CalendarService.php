<?php

namespace App\Services;

use App\Models\CalendarEvent;
use Modules\LMS\Sessions\Models\Session;
use Illuminate\Support\Carbon;

class CalendarService
{
    /**
     * Get events for user
     */
    public function getEventsForUser(?int $userId = null, ?Carbon $start = null, ?Carbon $end = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = CalendarEvent::query();

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereNull('user_id'); // System events
            });
        } else {
            $query->whereNull('user_id'); // Only system events
        }

        if ($start) {
            $query->where('start_datetime', '>=', $start);
        }

        if ($end) {
            $query->where('start_datetime', '<=', $end);
        }

        return $query->orderBy('start_datetime')->get();
    }

    /**
     * Sync events from sessions
     */
    public function syncSessionEvents(): void
    {
        $sessions = Session::whereNotNull('scheduled_at')
            ->whereHas('group')
            ->with('group')
            ->get();

        foreach ($sessions as $session) {
            CalendarEvent::updateOrCreate(
                [
                    'event_type' => 'session',
                    'reference_id' => $session->id,
                ],
                [
                    'user_id' => null,
                    'title' => $session->title ?? 'Session',
                    'description' => $session->description,
                    'start_datetime' => $session->scheduled_at,
                    'end_datetime' => $session->scheduled_at->addHours(2),
                    'color' => '#3b82f6', // Blue for sessions
                ]
            );
        }
    }


    /**
     * Create custom event
     */
    public function createCustomEvent(int $userId, array $data): CalendarEvent
    {
        return CalendarEvent::create(array_merge($data, [
            'user_id' => $userId,
            'event_type' => 'custom',
        ]));
    }
}

