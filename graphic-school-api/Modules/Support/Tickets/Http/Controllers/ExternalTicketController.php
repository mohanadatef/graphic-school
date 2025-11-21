<?php

namespace Modules\Support\Tickets\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Support\Tickets\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExternalTicketController extends Controller
{
    public function __construct(private TicketService $ticketService)
    {
        // This will be protected by API key middleware
    }

    /**
     * Create ticket from external system
     */
    public function store(Request $request): JsonResponse
    {
        // Validate API key
        $apiKey = $request->header('X-API-Key');
        $expectedKey = config('app.support_api_key');

        if ($apiKey !== $expectedKey) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'type' => ['required', 'string', 'in:issue,feature_request'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
        ]);

        $ticket = $this->ticketService->create($request->validated());

        return response()->json($ticket, 201);
    }
}

