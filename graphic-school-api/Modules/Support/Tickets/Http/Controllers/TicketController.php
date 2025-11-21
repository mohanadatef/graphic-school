<?php

namespace Modules\Support\Tickets\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Support\Tickets\Services\TicketService;
use Modules\Support\Tickets\Models\SupportTicket;
use Modules\Support\Tickets\Http\Requests\StoreTicketRequest;
use Modules\Support\Tickets\Http\Requests\UpdateTicketRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(private TicketService $ticketService)
    {
    }

    /**
     * Get tickets
     */
    public function index(Request $request): JsonResponse
    {
        $tickets = $this->ticketService->getTickets(
            $request->all(),
            $request->integer('per_page', 20)
        );

        return response()->json($tickets);
    }

    /**
     * Create ticket
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->create(
            $request->validated(),
            $request->user()?->id
        );

        return response()->json($ticket, 201);
    }

    /**
     * Get ticket
     */
    public function show(SupportTicket $ticket): JsonResponse
    {
        $ticket->load(['user', 'assignedTo']);

        return response()->json($ticket);
    }

    /**
     * Update ticket
     */
    public function update(UpdateTicketRequest $request, SupportTicket $ticket): JsonResponse
    {
        $ticket = $this->ticketService->update($ticket, $request->validated());

        return response()->json($ticket);
    }

    /**
     * Assign ticket
     */
    public function assign(Request $request, SupportTicket $ticket): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $ticket = $this->ticketService->assign($ticket, $request->integer('user_id'));

        return response()->json($ticket);
    }

    /**
     * Resolve ticket
     */
    public function resolve(SupportTicket $ticket): JsonResponse
    {
        $ticket = $this->ticketService->resolve($ticket);

        return response()->json($ticket);
    }

    /**
     * Close ticket
     */
    public function close(SupportTicket $ticket): JsonResponse
    {
        $ticket = $this->ticketService->close($ticket);

        return response()->json($ticket);
    }
}

