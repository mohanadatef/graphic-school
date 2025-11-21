<?php

namespace Modules\Support\Tickets\Http\Controllers;

use App\Support\Controllers\BaseController;
use Modules\Support\Tickets\Models\SupportTicket;
use Modules\Support\Tickets\Services\TicketService;
use Modules\Support\Tickets\Http\Requests\StoreTicketRequest;
use Modules\Support\Tickets\Http\Requests\UpdateTicketRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * CHANGE-006: Ticketing System (Admin ⇄ Technical Company)
 */
class TicketController extends BaseController
{
    public function __construct(
        private TicketService $ticketService
    ) {}

    /**
     * Get all tickets (Admin only)
     */
    public function index(Request $request): JsonResponse
    {
        $query = SupportTicket::with(['user', 'assignedTo'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->forStatus($request->input('status'));
        }

        // Filter by type
        if ($request->has('type')) {
            $query->forType($request->input('type'));
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->forPriority($request->input('priority'));
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $tickets = $query->paginate($request->input('per_page', 15));

        return $this->paginated($tickets, 'Tickets retrieved successfully');
    }

    /**
     * Get ticket details
     */
    public function show(int $id): JsonResponse
    {
        $ticket = SupportTicket::with(['user', 'assignedTo'])->findOrFail($id);

        return $this->success($ticket, 'Ticket retrieved successfully');
    }

    /**
     * Create new ticket (Admin only)
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $ticket = SupportTicket::create($validated);

        // TODO: Send notification to technical company

        return $this->created($ticket->load(['user', 'assignedTo']), 'Ticket created successfully');
    }

    /**
     * Update ticket (Admin only)
     */
    public function update(UpdateTicketRequest $request, int $id): JsonResponse
    {
        $ticket = SupportTicket::findOrFail($id);

        $validated = $request->validated();

        // Track status updates
        if (isset($validated['status']) && $validated['status'] !== $ticket->status) {
            $updates = $ticket->updates ?? [];
            $updates[] = [
                'status' => $validated['status'],
                'updated_by' => $request->user()->id,
                'updated_at' => now()->toIso8601String(),
            ];
            $validated['updates'] = $updates;
        }

        $ticket->update($validated);

        return $this->success($ticket->load(['user', 'assignedTo']), 'Ticket updated successfully');
    }

    /**
     * Upload attachment to ticket
     */
    public function uploadAttachment(Request $request, int $id): JsonResponse
    {
        $ticket = SupportTicket::findOrFail($id);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('tickets/' . $id, 'public');

        $attachments = $ticket->attachments ?? [];
        $attachments[] = [
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_at' => now()->toIso8601String(),
        ];

        $ticket->update(['attachments' => $attachments]);

        return $this->success($ticket, 'Attachment uploaded successfully');
    }

    /**
     * Get ticket reports
     */
    public function reports(Request $request): JsonResponse
    {
        $query = SupportTicket::query();

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->input('from_date'));
        }
        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->input('to_date'));
        }

        $totalTickets = $query->count();
        $byStatus = [
            'open' => $query->clone()->forStatus('open')->count(),
            'in_progress' => $query->clone()->forStatus('in_progress')->count(),
            'resolved' => $query->clone()->forStatus('resolved')->count(),
            'closed' => $query->clone()->forStatus('closed')->count(),
        ];
        $byType = [
            'bug' => $query->clone()->forType('bug')->count(),
            'change_request' => $query->clone()->forType('change_request')->count(),
            'new_feature' => $query->clone()->forType('new_feature')->count(),
        ];

        return $this->success([
            'summary' => [
                'total_tickets' => $totalTickets,
            ],
            'by_status' => $byStatus,
            'by_type' => $byType,
        ], 'Ticket reports retrieved successfully');
    }
}
