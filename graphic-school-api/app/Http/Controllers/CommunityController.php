<?php

namespace App\Http\Controllers;

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
     * Get posts with filters
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['program_id', 'batch_id', 'group_id', 'user_id', 'tag', 'sort']);
        $perPage = $request->get('per_page', 20);

        $posts = $this->communityService->getPosts($filters, $perPage);

        return ApiResponse::success($posts, 'Posts retrieved successfully');
    }

    /**
     * Get a single post with thread
     */
    public function show(int $id): JsonResponse
    {
        $post = $this->communityService->getPostWithThread($id);

        return ApiResponse::success($post, 'Post retrieved successfully');
    }

    /**
     * Create a new post
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'program_id' => 'nullable|exists:programs,id',
            'batch_id' => 'nullable|exists:batches,id',
            'group_id' => 'nullable|exists:groups,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'attachments' => 'nullable|array',
        ]);

        $post = $this->communityService->createPost($request->user(), $validated);

        return ApiResponse::success($post->load(['user', 'tags']), 'Post created successfully', 201);
    }

    /**
     * Create a comment
     */
    public function createComment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:community_posts,id',
            'body' => 'required|string',
            'attachments' => 'nullable|array',
        ]);

        $comment = $this->communityService->createComment(
            $request->user(),
            $validated['post_id'],
            $validated
        );

        return ApiResponse::success($comment->load(['user']), 'Comment created successfully', 201);
    }

    /**
     * Create a reply
     */
    public function createReply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'comment_id' => 'required|exists:community_comments,id',
            'body' => 'required|string',
        ]);

        $reply = $this->communityService->createReply(
            $request->user(),
            $validated['comment_id'],
            $validated
        );

        return ApiResponse::success($reply->load(['user']), 'Reply created successfully', 201);
    }

    /**
     * Toggle like
     */
    public function toggleLike(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:post,comment,reply',
            'id' => 'required|integer',
        ]);

        $result = $this->communityService->toggleLike(
            $request->user(),
            $validated['type'],
            $validated['id']
        );

        return ApiResponse::success($result, 'Like toggled successfully');
    }

    /**
     * Report content
     */
    public function report(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:post,comment,reply',
            'id' => 'required|integer',
            'reason' => 'required|string|max:1000',
        ]);

        $report = $this->communityService->reportContent(
            $request->user(),
            $validated['type'],
            $validated['id'],
            $validated['reason']
        );

        return ApiResponse::success($report, 'Content reported successfully', 201);
    }

    /**
     * Get trending posts
     */
    public function trending(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $posts = $this->communityService->getTrendingPosts($limit);

        return ApiResponse::success($posts, 'Trending posts retrieved successfully');
    }

    /**
     * Get user's posts
     */
    public function myPosts(Request $request): JsonResponse
    {
        $filters = ['user_id' => $request->user()->id];
        $perPage = $request->get('per_page', 20);

        $posts = $this->communityService->getPosts($filters, $perPage);

        return ApiResponse::success($posts, 'Your posts retrieved successfully');
    }
}

