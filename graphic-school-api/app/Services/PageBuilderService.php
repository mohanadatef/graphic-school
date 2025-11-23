<?php

namespace App\Services;

use App\Models\PageBuilderPage;
use App\Models\PageBuilderStructure;
use App\Models\PageBuilderTemplate;
use Modules\ACL\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PageBuilderService
{
    protected $subscriptionService;

    public function __construct()
    {
        $this->subscriptionService = app(\App\Services\SubscriptionService::class);
    }

    /**
     * Create a new page
     */
    public function createPage(User $academy, array $data): PageBuilderPage
    {
        return DB::transaction(function () use ($academy, $data) {
            // Check page limit
            $this->checkPageLimit($academy);

            $page = PageBuilderPage::create([
                'academy_id' => $academy->id,
                'slug' => $data['slug'] ?? Str::slug($data['title']),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'language' => $data['language'] ?? 'en',
                'status' => $data['status'] ?? 'draft',
            ]);

            // Create empty structure
            PageBuilderStructure::create([
                'page_id' => $page->id,
                'structure' => $data['structure'] ?? [],
            ]);

            // Increment usage
            $this->subscriptionService->incrementUsage($academy, 'pages');

            return $page->load('structure');
        });
    }

    /**
     * Update page metadata
     */
    public function updatePage(int $pageId, array $data): PageBuilderPage
    {
        $page = PageBuilderPage::findOrFail($pageId);

        $page->update([
            'title' => $data['title'] ?? $page->title,
            'slug' => $data['slug'] ?? $page->slug,
            'description' => $data['description'] ?? $page->description,
            'language' => $data['language'] ?? $page->language,
            'status' => $data['status'] ?? $page->status,
        ]);

        return $page->fresh();
    }

    /**
     * Save page structure
     */
    public function saveStructure(int $pageId, array $structure): PageBuilderStructure
    {
        $page = PageBuilderPage::findOrFail($pageId);
        
        // Validate structure
        $this->validateStructure($structure);

        $pageStructure = PageBuilderStructure::updateOrCreate(
            ['page_id' => $pageId],
            ['structure' => $structure]
        );

        return $pageStructure;
    }

    /**
     * Publish page
     */
    public function publishPage(int $pageId): PageBuilderPage
    {
        $page = PageBuilderPage::findOrFail($pageId);
        $page->update(['status' => 'published']);
        return $page->fresh();
    }

    /**
     * Duplicate page
     */
    public function duplicatePage(int $pageId, User $academy): PageBuilderPage
    {
        return DB::transaction(function () use ($pageId, $academy) {
            // Check page limit
            $this->checkPageLimit($academy);

            $originalPage = PageBuilderPage::with('structure')->findOrFail($pageId);

            $newPage = PageBuilderPage::create([
                'academy_id' => $academy->id,
                'slug' => $originalPage->slug . '-copy-' . time(),
                'title' => $originalPage->title . ' (Copy)',
                'description' => $originalPage->description,
                'language' => $originalPage->language,
                'status' => 'draft',
            ]);

            if ($originalPage->structure) {
                PageBuilderStructure::create([
                    'page_id' => $newPage->id,
                    'structure' => $originalPage->structure->structure,
                ]);
            }

            // Increment usage
            $this->subscriptionService->incrementUsage($academy, 'pages');

            return $newPage->load('structure');
        });
    }

    /**
     * Apply template to page
     */
    public function applyTemplate(int $pageId, int $templateId): PageBuilderStructure
    {
        $page = PageBuilderPage::findOrFail($pageId);
        $template = PageBuilderTemplate::findOrFail($templateId);

        $pageStructure = PageBuilderStructure::updateOrCreate(
            ['page_id' => $pageId],
            ['structure' => $template->structure]
        );

        return $pageStructure;
    }

    /**
     * Get page for public rendering
     */
    public function getPublicPage(string $academySlug, string $pageSlug, string $language = 'en'): ?PageBuilderPage
    {
        // Find academy by slug (try email, name, or ID)
        $academy = User::whereHas('role', fn($q) => $q->where('name', 'admin'))
            ->where(function($q) use ($academySlug) {
                $q->where('email', 'like', "%{$academySlug}%")
                  ->orWhere('name', 'like', "%{$academySlug}%")
                  ->orWhere('id', $academySlug);
            })
            ->first();

        if (!$academy) {
            return null;
        }

        return PageBuilderPage::where('academy_id', $academy->id)
            ->where('slug', $pageSlug)
            ->where('language', $language)
            ->where('status', 'published')
            ->with('structure')
            ->first();
    }

    /**
     * Auto-create homepage for new academy
     */
    public function createHomepageForAcademy(User $academy): PageBuilderPage
    {
        // Check if homepage already exists
        $existing = PageBuilderPage::where('academy_id', $academy->id)
            ->where('slug', 'home')
            ->first();

        if ($existing) {
            return $existing;
        }

        // Get default template or create basic structure
        $defaultTemplate = PageBuilderTemplate::where('is_default', true)->first();
        
        $structure = $defaultTemplate ? $defaultTemplate->structure : $this->getDefaultHomepageStructure();

        $page = $this->createPage($academy, [
            'title' => 'Home',
            'slug' => 'home',
            'description' => 'Homepage',
            'language' => 'en',
            'status' => 'draft',
            'structure' => $structure,
        ]);

        return $page;
    }

    /**
     * Get default homepage structure
     */
    protected function getDefaultHomepageStructure(): array
    {
        return [
            [
                'type' => 'hero',
                'id' => uniqid('block_'),
                'config' => [
                    'title' => 'Welcome to Our Academy',
                    'subtitle' => 'Learn and grow with us',
                    'background_image' => null,
                    'button_text' => 'Get Started',
                    'button_link' => '/courses',
                ],
            ],
            [
                'type' => 'features',
                'id' => uniqid('block_'),
                'config' => [
                    'title' => 'Why Choose Us',
                    'features' => [
                        ['icon' => 'fas fa-graduation-cap', 'title' => 'Expert Instructors', 'description' => 'Learn from industry experts'],
                        ['icon' => 'fas fa-certificate', 'title' => 'Certified Programs', 'description' => 'Get recognized certificates'],
                        ['icon' => 'fas fa-users', 'title' => 'Community Support', 'description' => 'Join our active community'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Validate structure
     */
    protected function validateStructure(array $structure): void
    {
        if (!is_array($structure)) {
            throw new \Exception('Structure must be an array');
        }

        foreach ($structure as $block) {
            if (!isset($block['type'])) {
                throw new \Exception('Each block must have a type');
            }

            $validTypes = ['hero', 'features', 'testimonials', 'programs', 'video', 'gallery', 'faq', 'html', 'contact', 'cta'];
            if (!in_array($block['type'], $validTypes)) {
                throw new \Exception("Invalid block type: {$block['type']}");
            }
        }
    }

    /**
     * Check page limit
     */
    protected function checkPageLimit(User $academy): void
    {
        try {
            $this->subscriptionService->blockIfOverLimit($academy, 'pages');
        } catch (\Exception $e) {
            throw new \Exception('Page limit exceeded for your current plan. Upgrade to create more pages.');
        }
    }

    /**
     * Get pages for academy
     */
    public function getPages(User $academy, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = PageBuilderPage::where('academy_id', $academy->id);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('slug', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }
}

