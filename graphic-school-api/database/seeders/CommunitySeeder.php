<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\CommunityReply;
use App\Models\CommunityLike;
use App\Models\CommunityTag;
use App\Models\CommunityReport;
use App\Services\GamificationService;
use Modules\ACL\Users\Models\User;
use App\Models\Program;
use App\Models\Batch;
use App\Models\Group;
use Illuminate\Support\Str;

class CommunitySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTags();
        $this->seedPosts();
        $this->seedComments();
        $this->seedReplies();
        $this->seedLikes();
        $this->seedReports();
    }

    protected function seedTags(): void
    {
        $tags = [
            'question',
            'help',
            'discussion',
            'announcement',
            'tutorial',
            'resources',
            'feedback',
            'project',
            'assignment',
            'general',
        ];

        foreach ($tags as $tagName) {
            CommunityTag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
        }

        $this->command->info('Community tags seeded successfully!');
    }

    protected function seedPosts(): void
    {
        $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))
            ->take(10)
            ->get();

        $programs = Program::take(3)->get();
        $batches = Batch::take(5)->get();
        $groups = Group::take(5)->get();
        
        if ($programs->isEmpty() || $batches->isEmpty() || $groups->isEmpty()) {
            $this->command->warn('Programs, batches, or groups not found. Creating posts without program/batch/group associations.');
        }

        $titles = [
            'How to submit assignments?',
            'Best practices for graphic design',
            'Question about the project deadline',
            'Sharing some useful resources',
            'Discussion about the latest session',
            'Need help with Photoshop',
            'Feedback on the course structure',
            'Announcement: New workshop available',
            'Tutorial: Creating vector graphics',
            'General discussion thread',
            'Assignment clarification needed',
            'Tips for better presentations',
            'Resource sharing: Free fonts',
            'Course feedback and suggestions',
            'Project collaboration ideas',
            'Question about certificates',
            'Study group formation',
            'Technical support needed',
            'Success story sharing',
            'Community guidelines discussion',
        ];

        $bodies = [
            'I have a question about how to properly submit assignments. Can someone help?',
            'Let\'s discuss the best practices we learned in the last session.',
            'I need clarification on the project deadline. Is it flexible?',
            'I found some great resources that might help everyone. Sharing here!',
            'What did everyone think about the latest session? Let\'s discuss.',
            'I\'m having trouble with Photoshop. Any tips?',
            'I have some feedback on the course structure. What do you think?',
            'There\'s a new workshop available. Check it out!',
            'Here\'s a tutorial I created for creating vector graphics.',
            'General discussion thread for anything related to the course.',
        ];

        $posts = [];
        foreach ($titles as $index => $title) {
            if ($students->isEmpty()) {
                $this->command->warn('No students found. Skipping post creation.');
                break;
            }
            
            $student = $students->random();
            
            $post = CommunityPost::create([
                'user_id' => $student->id,
                'program_id' => $programs->isNotEmpty() ? $programs->random()->id : null,
                'batch_id' => $batches->isNotEmpty() ? $batches->random()->id : null,
                'group_id' => $groups->isNotEmpty() ? $groups->random()->id : null,
                'title' => $title,
                'body' => $bodies[$index % count($bodies)],
                'is_pinned' => $index < 2, // First 2 posts are pinned
                'is_locked' => false,
            ]);

            // Attach random tags
            $tags = CommunityTag::inRandomOrder()->take(rand(1, 3))->get();
            $post->tags()->attach($tags->pluck('id'));

            $posts[] = $post;

            // Award gamification points (simulate)
            try {
                $gamificationService = app(GamificationService::class);
                $gamificationService->awardPointsForEvent(
                    $student,
                    'community_post',
                    'community_posts',
                    $post->id,
                    ['title' => $post->title]
                );
            } catch (\Exception $e) {
                // Skip if already awarded
            }
        }

        $this->command->info('Community posts seeded successfully!');
    }

    protected function seedComments(): void
    {
        $posts = CommunityPost::take(20)->get();
        $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))
            ->take(15)
            ->get();

        $commentBodies = [
            'Great question! I had the same issue.',
            'Thanks for sharing this!',
            'This is very helpful.',
            'I agree with your point.',
            'Let me add my thoughts...',
            'Can you provide more details?',
            'I found this useful too.',
            'Good discussion topic!',
            'I have a different perspective.',
            'Thanks for the clarification.',
        ];

        $comments = [];
        foreach ($posts as $post) {
            if ($students->isEmpty()) break;
            $commentCount = rand(1, 3);
            for ($i = 0; $i < $commentCount; $i++) {
                $student = $students->random();
                $comment = CommunityComment::create([
                    'post_id' => $post->id,
                    'user_id' => $student->id,
                    'body' => $commentBodies[array_rand($commentBodies)],
                ]);

                $comments[] = $comment;

                // Award gamification points
                try {
                    $gamificationService = app(GamificationService::class);
                    $gamificationService->awardPointsForEvent(
                        $student,
                        'community_comment',
                        'community_comments',
                        $comment->id,
                        ['post_id' => $post->id]
                    );
                } catch (\Exception $e) {
                    // Skip if already awarded
                }
            }
        }

        $this->command->info('Community comments seeded successfully!');
    }

    protected function seedReplies(): void
    {
        $comments = CommunityComment::take(20)->get();
        $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))
            ->take(10)
            ->get();

        $replyBodies = [
            'I see what you mean.',
            'Thanks for the response!',
            'That makes sense.',
            'I\'ll try that.',
            'Good point!',
            'I agree.',
            'Let me clarify...',
            'That\'s helpful.',
            'I think so too.',
            'Thanks!',
        ];

        foreach ($comments as $comment) {
            if ($students->isEmpty()) break;
            if (rand(1, 2) === 1) { // 50% chance of having a reply
                $student = $students->random();
                $reply = CommunityReply::create([
                    'comment_id' => $comment->id,
                    'user_id' => $student->id,
                    'body' => $replyBodies[array_rand($replyBodies)],
                ]);

                // Award gamification points
                try {
                    $gamificationService = app(GamificationService::class);
                    $gamificationService->awardPointsForEvent(
                        $student,
                        'community_reply',
                        'community_replies',
                        $reply->id,
                        ['comment_id' => $comment->id]
                    );
                } catch (\Exception $e) {
                    // Skip if already awarded
                }
            }
        }

        $this->command->info('Community replies seeded successfully!');
    }

    protected function seedLikes(): void
    {
        $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))
            ->take(20)
            ->get();

        // Like posts
        $posts = CommunityPost::take(20)->get();
        foreach ($posts as $post) {
            if ($students->isEmpty()) break;
            $likeCount = min(rand(2, 8), $students->count());
            $likers = $students->random($likeCount);
            foreach ($likers as $liker) {
                CommunityLike::firstOrCreate([
                    'user_id' => $liker->id,
                    'likeable_type' => CommunityPost::class,
                    'likeable_id' => $post->id,
                ]);
            }
        }

        // Like comments
        $comments = CommunityComment::take(30)->get();
        foreach ($comments as $comment) {
            if ($students->isEmpty()) break;
            if (rand(1, 3) === 1) { // 33% chance
                $liker = $students->random();
                CommunityLike::firstOrCreate([
                    'user_id' => $liker->id,
                    'likeable_type' => CommunityComment::class,
                    'likeable_id' => $comment->id,
                ]);
            }
        }

        $this->command->info('Community likes seeded successfully!');
    }

    protected function seedReports(): void
    {
        $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))
            ->take(5)
            ->get();

        $posts = CommunityPost::take(5)->get();
        $comments = CommunityComment::take(3)->get();

        $reasons = [
            'Inappropriate content',
            'Spam',
            'Off-topic',
            'Harassment',
            'False information',
        ];

        // Report some posts
        foreach ($posts as $post) {
            if ($students->isEmpty()) break;
            $reporter = $students->random();
            CommunityReport::create([
                'user_id' => $reporter->id,
                'reportable_type' => CommunityPost::class,
                'reportable_id' => $post->id,
                'reason' => $reasons[array_rand($reasons)],
                'status' => 'pending',
            ]);
        }

        // Report some comments
        foreach ($comments as $comment) {
            if ($students->isEmpty()) break;
            $reporter = $students->random();
            CommunityReport::create([
                'user_id' => $reporter->id,
                'reportable_type' => CommunityComment::class,
                'reportable_id' => $comment->id,
                'reason' => $reasons[array_rand($reasons)],
                'status' => 'pending',
            ]);
        }

        $this->command->info('Community reports seeded successfully!');
    }
}

