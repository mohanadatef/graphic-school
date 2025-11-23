<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\CommunityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function __construct(
        private CommunityService $communityService
    ) {
    }

    /**
     * Pin a post in instructor's group
     */
    public function pinPost(int $id, Request $request): JsonResponse
    {
        $post = \App\Models\CommunityPost::with('group')->findOrFail($id);
        
        // Verify instructor has access to this group
        $instructor = $request->user();
        $hasAccess = $post->group && $post->group->instructors->contains($instructor->id);
        
        if (!$hasAccess) {
            return ApiResponse::error('You do not have permission to pin posts in this group', [], 403);
        }

        $post = $this->communityService->togglePin($id, true);

        return ApiResponse::success($post, 'Post pinned successfully');
    }

    /**
     * Unpin a post in instructor's group
     */
    public function unpinPost(int $id, Request $request): JsonResponse
    {
        $post = \App\Models\CommunityPost::with('group')->findOrFail($id);
        
        // Verify instructor has access to this group
        $instructor = $request->user();
        $hasAccess = $post->group && $post->group->instructors->contains($instructor->id);
        
        if (!$hasAccess) {
            return ApiResponse::error('You do not have permission to unpin posts in this group', [], 403);
        }

        $post = $this->communityService->togglePin($id, false);

        return ApiResponse::success($post, 'Post unpinned successfully');
    }
}

