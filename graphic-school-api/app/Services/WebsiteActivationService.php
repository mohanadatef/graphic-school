<?php

namespace App\Services;

use App\Models\WebsiteSetting;
use App\Models\PageBuilderPage;
use App\Models\PageBuilderStructure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebsiteActivationService
{
    /**
     * Check if website is activated
     */
    public function isActivated(): bool
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('website_settings')) {
                return false;
            }
            $settings = WebsiteSetting::getDefault();
            return $settings->isActivated();
        } catch (\Exception $e) {
            // If there's any error, assume not activated
            return false;
        }
    }

    /**
     * Check if setup wizard should run
     */
    public function shouldRunSetup(): bool
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('website_settings')) {
                return true; // Table doesn't exist, need setup
            }
            
            $settings = WebsiteSetting::getDefault();
            
            // If already activated, don't run setup
            if ($settings->isActivated()) {
                return false;
            }

            // Check if required settings are missing
            $hasBranding = !empty($settings->branding);
            $hasLanguage = !empty($settings->default_language);
            $hasCurrency = !empty($settings->default_currency);
            $hasGeneralInfo = !empty($settings->general_info);

            // If any required setting is missing, run setup
            return !($hasBranding && $hasLanguage && $hasCurrency && $hasGeneralInfo);
        } catch (\Exception $e) {
            // On error, assume setup is needed
            return true;
        }
    }

    /**
     * Activate default website (skip setup)
     */
    public function activateDefaultWebsite(): WebsiteSetting
    {
        try {
            return DB::transaction(function () {
                $settings = WebsiteSetting::getDefault();
                
                // Ensure settings are saved to database if they're new
                if (!$settings->exists) {
                    $settings->save();
                }
                
                // Set default branding if not set
                if (empty($settings->branding)) {
                    $settings->branding = [
                        'logo' => null,
                        'primary_color' => '#3b82f6',
                        'secondary_color' => '#6366f1',
                        'font_main' => 'Cairo',
                        'font_headings' => 'Poppins',
                        'default_theme' => 'light',
                    ];
                }

                // Set default language if not set
                if (empty($settings->default_language)) {
                    $settings->default_language = 'en';
                }

                // Set default currency if not set
                if (empty($settings->default_currency)) {
                    $settings->default_currency = 'USD';
                }

                // Set default general info if not set
                if (empty($settings->general_info)) {
                    $settings->general_info = [
                        'academy_name' => 'Graphic School',
                        'country' => null,
                    ];
                }

                // Set default enabled pages if not set
                if (empty($settings->enabled_pages)) {
                    $settings->enabled_pages = [
                        'home' => true,
                        'about' => true,
                        'contact' => true,
                        'programs' => true,
                        'community' => true,
                        'faq' => false,
                    ];
                }

                // Create default homepage if not exists
                if (empty($settings->homepage_id)) {
                    try {
                        $homepage = $this->createDefaultHomepage();
                        if ($homepage && $homepage->id) {
                            $settings->homepage_id = $homepage->id;
                        }
                    } catch (\Exception $e) {
                        // If homepage creation fails, log but continue without it
                        // This allows activation to proceed even if homepage can't be created
                        \Log::warning('Failed to create default homepage: ' . $e->getMessage(), [
                            'exception' => $e,
                        ]);
                        // Continue without homepage_id - it's optional
                    }
                }

                // Save settings before activating
                $settings->save();

                // Activate (this will save automatically)
                $settings->activate();

                return $settings;
            });
        } catch (\Exception $e) {
            \Log::error('Error in activateDefaultWebsite: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Complete setup wizard
     */
    public function completeSetup(array $data): WebsiteSetting
    {
        try {
            return DB::transaction(function () use ($data) {
                $settings = WebsiteSetting::getDefault();
                
                // Ensure settings are saved to database if they're new
                if (!$settings->exists) {
                    $settings->save();
                }

            // Update general info
            if (isset($data['general_info'])) {
                $settings->general_info = array_merge(
                    $settings->general_info ?? [],
                    $data['general_info']
                );
            }

            // Update branding
            if (isset($data['branding'])) {
                $settings->branding = array_merge(
                    $settings->branding ?? [],
                    $data['branding']
                );
            }

            // Update language
            if (isset($data['default_language'])) {
                $settings->default_language = $data['default_language'];
            }

            // Update currency
            if (isset($data['default_currency'])) {
                $settings->default_currency = $data['default_currency'];
            }

            // Update timezone
            if (isset($data['timezone'])) {
                $settings->timezone = $data['timezone'];
            }

            // Update enabled pages
            if (isset($data['enabled_pages'])) {
                $settings->enabled_pages = $data['enabled_pages'];
            }

            // Update email settings
            if (isset($data['email_settings'])) {
                $settings->email_settings = $data['email_settings'];
            }

            // Update payment settings
            if (isset($data['payment_settings'])) {
                $settings->payment_settings = $data['payment_settings'];
            }

            // Create homepage if specified
            if (isset($data['homepage_template']) && empty($settings->homepage_id)) {
                try {
                    $homepage = $this->createHomepageFromTemplate($data['homepage_template']);
                    if ($homepage && $homepage->id) {
                        $settings->homepage_id = $homepage->id;
                    }
                } catch (\Exception $e) {
                    // If homepage creation fails, log but continue without it
                    \Log::warning('Failed to create homepage from template: ' . $e->getMessage(), [
                        'exception' => $e,
                    ]);
                    // Continue without homepage_id - it's optional
                }
            }

                // Save settings before activating
                $settings->save();

                // Activate (this will save automatically)
                $settings->activate();

                return $settings;
            });
        } catch (\Exception $e) {
            \Log::error('Error in completeSetup: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Save setup step (partial save)
     */
    public function saveStep(int $step, array $data): WebsiteSetting
    {
        $settings = WebsiteSetting::getDefault();

        switch ($step) {
            case 1: // General Information
                $settings->general_info = array_merge(
                    $settings->general_info ?? [],
                    $data
                );
                if (isset($data['default_language'])) {
                    $settings->default_language = $data['default_language'];
                }
                if (isset($data['default_currency'])) {
                    $settings->default_currency = $data['default_currency'];
                }
                if (isset($data['timezone'])) {
                    $settings->timezone = $data['timezone'];
                }
                break;

            case 2: // Branding
                $settings->branding = array_merge(
                    $settings->branding ?? [],
                    $data
                );
                break;

            case 3: // Website Pages
                if (isset($data['enabled_pages'])) {
                    $settings->enabled_pages = $data['enabled_pages'];
                }
                if (isset($data['homepage_template'])) {
                    // Create homepage if not exists
                    if (empty($settings->homepage_id)) {
                        $homepage = $this->createHomepageFromTemplate($data['homepage_template']);
                        $settings->homepage_id = $homepage->id;
                    }
                }
                break;

            case 4: // Email Setup
                $settings->email_settings = array_merge(
                    $settings->email_settings ?? [],
                    $data
                );
                break;

            case 5: // Payment Setup
                $settings->payment_settings = array_merge(
                    $settings->payment_settings ?? [],
                    $data
                );
                break;
        }

        $settings->save();
        return $settings;
    }

    /**
     * Get public settings
     */
    public function getPublicSettings(): array
    {
        $settings = WebsiteSetting::getDefault();
        return $settings->getPublicSettings();
    }

    /**
     * Create default homepage
     */
    protected function createDefaultHomepage(): PageBuilderPage
    {
        try {
            // Get the first admin user for academy_id (required by migration)
            $adminUser = \Modules\ACL\Users\Models\User::whereHas('role', function ($q) {
                $q->where('name', 'admin');
            })->first();

            // If no admin exists, try to get any user
            if (!$adminUser) {
                $adminUser = \Modules\ACL\Users\Models\User::first();
            }

            // Check if homepage already exists
            $existingPage = PageBuilderPage::where('slug', 'home')
                ->when($adminUser, fn($q) => $q->where('academy_id', $adminUser->id))
                ->first();
            
            if ($existingPage) {
                return $existingPage;
            }

            // If no user exists, we can't create a page (migration requires academy_id)
            if (!$adminUser) {
                throw new \Exception('Cannot create homepage: No admin user found. Please create an admin user first.');
            }

            $page = PageBuilderPage::create([
                'academy_id' => $adminUser->id,
                'title' => 'Home',
                'slug' => 'home',
                'status' => 'published',
            ]);

            // Create structure if page was created successfully
            if ($page && $page->id) {
                try {
                    $page->structure()->create([
                        'structure' => [
                            'blocks' => [
                                [
                                    'type' => 'hero',
                                    'data' => [
                                        'title' => 'Welcome to Graphic School',
                                        'subtitle' => 'Learn graphic design and creative skills',
                                        'cta_text' => 'Explore Programs',
                                        'cta_link' => '/programs',
                                    ],
                                ],
                                [
                                    'type' => 'features',
                                    'data' => [
                                        'title' => 'Why Choose Us',
                                        'features' => [
                                            ['title' => 'Expert Instructors', 'description' => 'Learn from industry professionals'],
                                            ['title' => 'Hands-on Projects', 'description' => 'Build real-world portfolios'],
                                            ['title' => 'Flexible Learning', 'description' => 'Study at your own pace'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ]);
                } catch (\Exception $e) {
                    // If structure creation fails, log but return the page anyway
                    \Log::warning('Failed to create homepage structure: ' . $e->getMessage());
                }
            }

            return $page;
        } catch (\Exception $e) {
            \Log::error('Error creating default homepage: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Create homepage from template
     */
    protected function createHomepageFromTemplate(string $template): PageBuilderPage
    {
        // Template A: Simple
        if ($template === 'template-a') {
            return $this->createDefaultHomepage();
        }

        // Template B: Advanced (with more sections)
        if ($template === 'template-b') {
            // Get the first admin user for academy_id (required by migration)
            $adminUser = \Modules\ACL\Users\Models\User::whereHas('role', function ($q) {
                $q->where('name', 'admin');
            })->first();

            // If no admin exists, try to get any user
            if (!$adminUser) {
                $adminUser = \Modules\ACL\Users\Models\User::first();
            }

            if (!$adminUser) {
                throw new \Exception('Cannot create homepage: No admin user found. Please create an admin user first.');
            }

            $page = PageBuilderPage::create([
                'academy_id' => $adminUser->id,
                'title' => 'Home',
                'slug' => 'home',
                'status' => 'published',
            ]);

            // Create structure
            $page->structure()->create([
                'structure' => [
                    'blocks' => [
                        [
                            'type' => 'hero',
                            'data' => [
                                'title' => 'Welcome to Graphic School',
                                'subtitle' => 'Transform your creative career',
                                'cta_text' => 'Get Started',
                                'cta_link' => '/programs',
                            ],
                        ],
                        [
                            'type' => 'features',
                            'data' => [
                                'title' => 'Our Features',
                                'features' => [
                                    ['title' => 'Expert Instructors', 'description' => 'Learn from industry professionals'],
                                    ['title' => 'Hands-on Projects', 'description' => 'Build real-world portfolios'],
                                    ['title' => 'Flexible Learning', 'description' => 'Study at your own pace'],
                                    ['title' => 'Career Support', 'description' => 'Get help finding your dream job'],
                                ],
                            ],
                        ],
                        [
                            'type' => 'programs',
                            'data' => [
                                'title' => 'Featured Programs',
                                'limit' => 3,
                            ],
                        ],
                        [
                            'type' => 'testimonials',
                            'data' => [
                                'title' => 'What Our Students Say',
                                'limit' => 3,
                            ],
                        ],
                        [
                            'type' => 'cta',
                            'data' => [
                                'title' => 'Ready to Start?',
                                'subtitle' => 'Join thousands of students learning with us',
                                'cta_text' => 'Enroll Now',
                                'cta_link' => '/programs',
                            ],
                        ],
                    ],
                ],
            ]);

            return $page;
        }

        // Default to template A
        return $this->createDefaultHomepage();
    }

    /**
     * Reset website to default
     */
    public function resetToDefault(): WebsiteSetting
    {
        return DB::transaction(function () {
            $settings = WebsiteSetting::getDefault();

            // Delete custom homepage
            if ($settings->homepage_id) {
                PageBuilderPage::where('id', $settings->homepage_id)
                    ->where('slug', '!=', 'home')
                    ->delete();
            }

            // Reset to defaults
            $settings->update([
                'branding' => [
                    'logo' => null,
                    'primary_color' => '#3b82f6',
                    'secondary_color' => '#6366f1',
                    'font_main' => 'Cairo',
                    'font_headings' => 'Poppins',
                    'default_theme' => 'light',
                ],
                'default_language' => 'en',
                'default_currency' => 'USD',
                'timezone' => 'UTC',
                'enabled_pages' => [
                    'home' => true,
                    'about' => true,
                    'contact' => true,
                    'programs' => true,
                    'community' => true,
                    'faq' => false,
                ],
                'homepage_id' => null,
            ]);

            // Create new default homepage
            $homepage = $this->createDefaultHomepage();
            $settings->homepage_id = $homepage->id;
            $settings->save();

            return $settings;
        });
    }
}

