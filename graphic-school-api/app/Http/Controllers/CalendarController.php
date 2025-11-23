<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;
use App\Models\CalendarEvent;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function __construct(
        private CalendarService $calendarService
    ) {
    }

    /**
     * Get calendar events
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        $start = $request->input('start') ? Carbon::parse($request->input('start')) : null;
        $end = $request->input('end') ? Carbon::parse($request->input('end')) : null;

        $events = $this->calendarService->getEventsForUser($userId, $start, $end);

        return ApiResponse::success($events, 'Calendar events retrieved successfully');
    }

    /**
     * Create custom event
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'nullable|date|after:start_datetime',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $event = $this->calendarService->createCustomEvent(
            $request->user()->id,
            $request->only(['title', 'description', 'start_datetime', 'end_datetime', 'color'])
        );

        return ApiResponse::success($event, 'Event created successfully');
    }
}

