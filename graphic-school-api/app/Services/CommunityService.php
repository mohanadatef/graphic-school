<?php

namespace App\Services;

use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\CommunityReply;
use App\Models\CommunityLike;
use App\Models\CommunityTag;
use App\Models\CommunityReport;
use App\Models\GamificationPointsWallet;
use Modules\ACL\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CommunityService
{
    protected $gamificationService;
    protected $notificationService;

    public function __construct()
    {
        $this->gamificationService = app(\App\Services\GamificationService::class);
        // Notification service will be injected when available
    }

    /**
     * Create a new post
     */
    public function createPost(User $user, array $data): CommunityPost
    {
        return DB::transaction(function () use ($user, $data) {
            // Check usage limit
            $subscriptionService = app(\App\Services\SubscriptionService::class);
            $subscriptionService->blockIfOverLimit($user, 'community_posts');
            
            $post = CommunityPost::create([
                'user_id' => $user->id,
                'program_id' => $data['program_id'] ?? null,
                'batch_id' => $data['batch_id'] ?? null,
                'group_id' => $data['group_id'] ?? null,
                'title' => $data['title'],
                'body' => $data['body'],
                'attachments' => $data['attachments'] ?? null,
            ]);

            // Attach tags
            if (isset($data['tags']) && is_array($data['tags'])) {
                $this->attachTags($post, $data['tags']);
            }

            // Increment usage
            $subscriptionService->incrementUsage($user, 'community_posts');

            // Award gamification points
            try {
                $this->gamificationService->awardPointsForEvent(
                    $user,
                    'community_post',
                    'community_posts',
                    $post->id,
                    ['title' => $post->title]
                );
            } catch (\Exception $e) {
                Log::warning('Gamification failed for community post', [
                    'post_id' => $post->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return $post->load(['user', 'tags', 'program', 'batch', 'group']);
        });
    }

    /**
     * Create a comment on a post
     */
    public function createComment(User $user, int $postId, array $data): CommunityComment
    {
        return DB::transaction(function () use ($user, $postId, $data) {
            $post = CommunityPost::findOrFail($postId);

            if ($post->is_locked) {
                throw new \Exception('Post is locked and cannot be commented on');
            }

            $comment = CommunityComment::create([
                'post_id' => $postId,
                'user_id' => $user->id,
                'body' => $data['body'],
                'attachments' => $data['attachments'] ?? null,
            ]);

            // Award gamification points
            try {
                $this->gamificationService->awardPointsForEvent(
                    $user,
                    'community_comment',
                    'community_comments',
                    $comment->id,
                    ['post_id' => $postId]
                );
            } catch (\Exception $e) {
                Log::warning('Gamification failed for community comment', [
                    'comment_id' => $comment->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Notify post author (if not the same user)
            if ($post->user_id !== $user->id) {
                $this->notifyPostAuthor($post, $user, 'comment');
            }

            return $comment->load(['user', 'post']);
        });
    }

    /**
     * Create a reply to a comment
     */
    public function createReply(User $user, int $commentId, array $data): CommunityReply
    {
        return DB::transaction(function () use ($user, $commentId, $data) {
            $comment = CommunityComment::with('post')->findOrFail($commentId);

            if ($comment->post->is_locked) {
                throw new \Exception('Post is locked and cannot be replied to');
            }

            $reply = CommunityReply::create([
                'comment_id' => $commentId,
                'user_id' => $user->id,
                'body' => $data['body'],
            ]);

            // Award gamification points
            try {
                $this->gamificationService->awardPointsForEvent(
                    $user,
                    'community_reply',
                    'community_replies',
                    $reply->id,
                    ['comment_id' => $commentId]
                );
            } catch (\Exception $e) {
                Log::warning('Gamification failed for community reply', [
                    'reply_id' => $reply->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Notify comment author (if not the same user)
            if ($comment->user_id !== $user->id) {
                $this->notifyCommentAuthor($comment, $user, 'reply');
            }

            return $reply->load(['user', 'comment']);
        });
    }

    /**
     * Toggle like on a post/comment/reply
     */
    public function toggleLike(User $user, string $type, int $id): array
    {
        $model = match($type) {
            'post' => CommunityPost::findOrFail($id),
            'comment' => CommunityComment::findOrFail($id),
            'reply' => CommunityReply::findOrFail($id),
            default => throw new \Exception('Invalid likeable type'),
        };

        $like = CommunityLike::where('user_id', $user->id)
            ->where('likeable_type', get_class($model))
            ->where('likeable_id', $id)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            CommunityLike::create([
                'user_id' => $user->id,
                'likeable_type' => get_class($model),
                'likeable_id' => $id,
            ]);
            $liked = true;

            // Notify author if not the same user
            if ($model->user_id !== $user->id) {
                $this->notifyLike($model, $user, $type);
            }
        }

        $likesCount = $model->likes()->count();

        return [
            'liked' => $liked,
            'likes_count' => $likesCount,
        ];
    }

    /**
     * Report content
     */
    public function reportContent(User $user, string $type, int $id, string $reason): CommunityReport
    {
        $model = match($type) {
            'post' => CommunityPost::findOrFail($id),
            'comment' => CommunityComment::findOrFail($id),
            'reply' => CommunityReply::findOrFail($id),
            default => throw new \Exception('Invalid reportable type'),
        };

        // Check if user already reported this content
        $existing = CommunityReport::where('user_id', $user->id)
            ->where('reportable_type', get_class($model))
            ->where('reportable_id', $id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            throw new \Exception('You have already reported this content');
        }

        return CommunityReport::create([
            'user_id' => $user->id,
            'reportable_type' => get_class($model),
            'reportable_id' => $id,
            'reason' => $reason,
            'status' => 'pending',
        ]);
    }

    /**
     * Pin/unpin a post (admin/instructor only)
     */
    public function togglePin(int $postId, bool $pin = true): CommunityPost
    {
        $post = CommunityPost::findOrFail($postId);
        $post->update(['is_pinned' => $pin]);

        if ($pin) {
            // Notify post author
            $this->notifyPostPinned($post);
        }

        return $post->fresh();
    }

    /**
     * Lock/unlock a post (admin only)
     */
    public function toggleLock(int $postId, bool $lock = true): CommunityPost
    {
        $post = CommunityPost::findOrFail($postId);
        $post->update(['is_locked' => $lock]);
        return $post->fresh();
    }

    /**
     * Get posts with filters
     */
    public function getPosts(array $filters = [], int $perPage = 20)
    {
        $query = CommunityPost::with(['user', 'tags', 'program', 'batch', 'group'])
            ->withCount(['comments', 'likes']);

        // Filters
        if (isset($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        if (isset($filters['batch_id'])) {
            $query->where('batch_id', $filters['batch_id']);
        }

        if (isset($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['tag'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('slug', $filters['tag']);
            });
        }

        // Sort
        $sort = $filters['sort'] ?? 'latest';
        switch ($sort) {
            case 'trending':
                // Trending = most likes + comments in last 7 days
                $query->where('created_at', '>=', now()->subDays(7))
                    ->orderByRaw('(likes_count + comments_count) DESC')
                    ->orderBy('created_at', 'desc');
                break;
            case 'most_liked':
                $query->orderBy('likes_count', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('is_pinned', 'desc')
                    ->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Get trending posts
     */
    public function getTrendingPosts(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return CommunityPost::with(['user', 'tags'])
            ->withCount(['comments', 'likes'])
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByRaw('(likes_count + comments_count) DESC')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get post with comments and replies
     */
    public function getPostWithThread(int $postId): CommunityPost
    {
        return CommunityPost::with([
            'user',
            'tags',
            'program',
            'batch',
            'group',
            'comments.user',
            'comments.replies.user',
            'comments.likes',
            'likes',
        ])
        ->withCount(['comments', 'likes'])
        ->findOrFail($postId);
    }

    /**
     * Attach tags to a post
     */
    protected function attachTags(CommunityPost $post, array $tagNames): void
    {
        $tagIds = [];
        foreach ($tagNames as $tagName) {
            $tag = CommunityTag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
            $tagIds[] = $tag->id;
        }
        $post->tags()->sync($tagIds);
    }

    /**
     * Delete or hide a post (moderation)
     */
    public function deletePost(int $postId, bool $softDelete = true): void
    {
        $post = CommunityPost::findOrFail($postId);
        
        if ($softDelete) {
            // For now, just delete (can implement soft deletes later)
            $post->delete();
        } else {
            $post->delete();
        }
    }

    /**
     * Notify post author of new comment
     */
    protected function notifyPostAuthor(CommunityPost $post, User $commenter, string $type): void
    {
        try {
            if (class_exists(\Modules\Core\Notification\Services\NotificationService::class)) {
                $notificationService = app(\Modules\Core\Notification\Services\NotificationService::class);
                $notificationService->create([
                    'user_id' => $post->user_id,
                    'type' => 'community_comment',
                    'title' => 'New comment on your post',
                    'message' => $commenter->name . ' commented on your post: ' . $post->title,
                    'data' => [
                        'post_id' => $post->id,
                        'commenter_id' => $commenter->id,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notify comment author of new reply
     */
    protected function notifyCommentAuthor(CommunityComment $comment, User $replier, string $type): void
    {
        try {
            if (class_exists(\Modules\Core\Notification\Services\NotificationService::class)) {
                $notificationService = app(\Modules\Core\Notification\Services\NotificationService::class);
                $notificationService->create([
                    'user_id' => $comment->user_id,
                    'type' => 'community_reply',
                    'title' => 'New reply to your comment',
                    'message' => $replier->name . ' replied to your comment',
                    'data' => [
                        'comment_id' => $comment->id,
                        'post_id' => $comment->post_id,
                        'replier_id' => $replier->id,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notify user of like
     */
    protected function notifyLike($likeable, User $liker, string $type): void
    {
        try {
            if (class_exists(\Modules\Core\Notification\Services\NotificationService::class)) {
                $notificationService = app(\Modules\Core\Notification\Services\NotificationService::class);
                $notificationService->create([
                    'user_id' => $likeable->user_id,
                    'type' => 'community_like',
                    'title' => 'Someone liked your ' . $type,
                    'message' => $liker->name . ' liked your ' . $type,
                    'data' => [
                        'likeable_type' => $type,
                        'likeable_id' => $likeable->id,
                        'liker_id' => $liker->id,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notify user of post being pinned
     */
    protected function notifyPostPinned(CommunityPost $post): void
    {
        try {
            if (class_exists(\Modules\Core\Notification\Services\NotificationService::class)) {
                $notificationService = app(\Modules\Core\Notification\Services\NotificationService::class);
                $notificationService->create([
                    'user_id' => $post->user_id,
                    'type' => 'community_post_pinned',
                    'title' => 'Your post was pinned',
                    'message' => 'Your post "' . $post->title . '" was pinned by an admin',
                    'data' => [
                        'post_id' => $post->id,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification', ['error' => $e->getMessage()]);
        }
    }
}

