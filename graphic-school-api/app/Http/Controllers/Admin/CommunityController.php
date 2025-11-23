<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\CommunityService;
use App\Models\CommunityPost;
use App\Models\CommunityReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function __construct(
        private CommunityService $communityService
    ) {
    }

    /**
     * List all posts (admin view)
     */
    public function posts(Request $request): JsonResponse
    {
        $filters = $request->only(['program_id', 'batch_id', 'group_id', 'user_id', 'tag', 'sort']);
        $perPage = $request->get('per_page', 20);

        $posts = $this->communityService->getPosts($filters, $perPage);

        return ApiResponse::success($posts, 'Posts retrieved successfully');
    }

    /**
     * Pin/unpin a post
     */
    public function togglePin(int $id, Request $request): JsonResponse
    {
        $pin = $request->boolean('pin', true);
        $post = $this->communityService->togglePin($id, $pin);

        return ApiResponse::success($post, 'Post pin status updated successfully');
    }

    /**
     * Lock/unlock a post
     */
    public function toggleLock(int $id, Request $request): JsonResponse
    {
        $lock = $request->boolean('lock', true);
        $post = $this->communityService->toggleLock($id, $lock);

        return ApiResponse::success($post, 'Post lock status updated successfully');
    }

    /**
     * Delete a post
     */
    public function deletePost(int $id): JsonResponse
    {
        $this->communityService->deletePost($id);
        return ApiResponse::success(null, 'Post deleted successfully');
    }

    /**
     * List reports
     */
    public function reports(Request $request): JsonResponse
    {
        $query = CommunityReport::with(['user', 'reviewer', 'reportable'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->paginate($request->get('per_page', 20));

        return ApiResponse::success($reports, 'Reports retrieved successfully');
    }

    /**
     * Resolve a report
     */
    public function resolveReport(int $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:reviewed,rejected',
            'action' => 'nullable|string', // e.g., 'delete_post', 'warn_user'
        ]);

        $report = CommunityReport::findOrFail($id);
        $report->update([
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        // Take action if needed
        if (isset($validated['action'])) {
            switch ($validated['action']) {
                case 'delete_post':
                    if ($report->reportable_type === CommunityPost::class) {
                        $this->communityService->deletePost($report->reportable_id);
                    }
                    break;
                // Add more actions as needed
            }
        }

        return ApiResponse::success($report->fresh(), 'Report resolved successfully');
    }
}

