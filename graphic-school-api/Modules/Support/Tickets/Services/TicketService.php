<?php

namespace Modules\Support\Tickets\Services;

use Modules\Support\Tickets\Models\SupportTicket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class TicketService
{
    /**
     * Get tickets with filters
     */
    public function getTickets(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = SupportTicket::with(['user', 'assignedTo'])
            ->orderByDesc('created_at');

        if (!empty($filters['status'])) {
            $query->forStatus($filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->forType($filters['type']);
        }

        if (!empty($filters['priority'])) {
            $query->forPriority($filters['priority']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a ticket
     */
    public function create(array $data, ?int $userId = null): SupportTicket
    {
        $ticket = SupportTicket::create([
            'user_id' => $userId,
            'type' => $data['type'],
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'open',
        ]);

        Log::info('Support ticket created', [
            'ticket_id' => $ticket->id,
            'type' => $ticket->type,
            'user_id' => $userId,
        ]);

        return $ticket;
    }

    /**
     * Update ticket
     */
    public function update(SupportTicket $ticket, array $data): SupportTicket
    {
        $ticket->update($data);

        Log::info('Support ticket updated', [
            'ticket_id' => $ticket->id,
            'changes' => $data,
        ]);

        return $ticket->fresh();
    }

    /**
     * Assign ticket to user
     */
    public function assign(SupportTicket $ticket, int $userId): SupportTicket
    {
        $ticket->update([
            'assigned_to' => $userId,
            'status' => 'in_progress',
        ]);

        return $ticket->fresh();
    }

    /**
     * Resolve ticket
     */
    public function resolve(SupportTicket $ticket): SupportTicket
    {
        $ticket->update([
            'status' => 'resolved',
        ]);

        return $ticket->fresh();
    }

    /**
     * Close ticket
     */
    public function close(SupportTicket $ticket): SupportTicket
    {
        $ticket->update([
            'status' => 'closed',
        ]);

        return $ticket->fresh();
    }
}

