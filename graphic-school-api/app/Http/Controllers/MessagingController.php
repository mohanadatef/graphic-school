<?php

namespace App\Http\Controllers;

use App\Support\Controllers\BaseController;
use App\Models\Conversation;
use App\Models\Message;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * CHANGE-005: Messaging System (Student ⇄ Instructor)
 */
class MessagingController extends BaseController
{
    /**
     * Get all conversations for authenticated user
     */
    public function conversations(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = Conversation::with(['student', 'instructor', 'course', 'session'])
            ->orderBy('last_message_at', 'desc');

        if ($user->isStudent()) {
            $query->forStudent($user->id);
        } elseif ($user->isInstructor()) {
            $query->forInstructor($user->id);
        } else {
            return $this->error('Unauthorized', [], 403);
        }

        // Filter by course
        if ($request->has('course_id')) {
            $query->forCourse($request->input('course_id'));
        }

        // Filter archived
        if ($request->has('archived')) {
            if ($request->boolean('archived')) {
                $query->where('is_archived', true);
            } else {
                $query->notArchived();
            }
        } else {
            $query->notArchived();
        }

        $conversations = $query->paginate($request->input('per_page', 15));

        // Add unread count for each conversation
        $conversations->getCollection()->transform(function ($conversation) use ($user) {
            $conversation->unread_count = $conversation->unreadCountFor($user->id);
            return $conversation;
        });

        return $this->paginated($conversations, 'Conversations retrieved successfully');
    }

    /**
     * Get or create conversation
     */
    public function getOrCreateConversation(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:users,id',
            'session_id' => 'nullable|exists:sessions,id',
            'subject' => 'nullable|string|max:255',
        ]);

        // Verify user is student and instructor is assigned to course
        if (! $user->isStudent()) {
            return $this->error('Only students can create conversations', [], 403);
        }

        $course = Course::findOrFail($validated['course_id']);
        if (! $course->instructors()->where('instructor_id', $validated['instructor_id'])->exists()) {
            return $this->error('Instructor is not assigned to this course', [], 403);
        }

        // Check if conversation already exists
        $conversation = Conversation::where('student_id', $user->id)
            ->where('instructor_id', $validated['instructor_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'student_id' => $user->id,
                'instructor_id' => $validated['instructor_id'],
                'course_id' => $validated['course_id'],
                'session_id' => $validated['session_id'] ?? null,
                'subject' => $validated['subject'] ?? null,
            ]);
        }

        return $this->success($conversation->load(['student', 'instructor', 'course', 'session']), 'Conversation retrieved');
    }

    /**
     * Get messages for a conversation
     */
    public function messages(Request $request, int $conversationId): JsonResponse
    {
        $user = $request->user();
        
        $conversation = Conversation::findOrFail($conversationId);

        // Verify user is part of this conversation
        if ($conversation->student_id !== $user->id && $conversation->instructor_id !== $user->id) {
            return $this->error('Unauthorized', [], 403);
        }

        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->paginate($request->input('per_page', 50));

        // Mark messages as read
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Update conversation last_message_at
        $conversation->update(['last_message_at' => now()]);

        return $this->paginated($messages, 'Messages retrieved successfully');
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string|max:5000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string',
        ]);

        $conversation = Conversation::findOrFail($validated['conversation_id']);

        // Verify user is part of this conversation
        if ($conversation->student_id !== $user->id && $conversation->instructor_id !== $user->id) {
            return $this->error('Unauthorized', [], 403);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $validated['message'],
            'attachments' => $validated['attachments'] ?? null,
        ]);

        // Update conversation
        $conversation->update([
            'last_message_at' => now(),
            'last_message_by' => $user->id,
        ]);

        // TODO: Send notification to the other party
        // event(new MessageCreated($message));

        return $this->created($message->load('sender'), 'Message sent successfully');
    }

    /**
     * Archive conversation
     */
    public function archive(Request $request, int $conversationId): JsonResponse
    {
        $user = $request->user();
        
        $conversation = Conversation::findOrFail($conversationId);

        // Verify user is part of this conversation
        if ($conversation->student_id !== $user->id && $conversation->instructor_id !== $user->id) {
            return $this->error('Unauthorized', [], 403);
        }

        $conversation->update(['is_archived' => true]);

        return $this->success($conversation, 'Conversation archived');
    }
}
