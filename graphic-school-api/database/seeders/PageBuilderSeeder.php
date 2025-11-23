<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PageBuilderTemplate;
use App\Models\PageBuilderPage;
use App\Models\PageBuilderStructure;
use App\Services\PageBuilderService;
use Modules\ACL\Users\Models\User;

class PageBuilderSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTemplates();
        $this->seedHomepage();
    }

    protected function seedTemplates(): void
    {
        // Landing Page Template
        PageBuilderTemplate::updateOrCreate(
            ['name' => 'Landing Page'],
            [
                'description' => 'A complete landing page with hero, features, and CTA',
                'structure' => [
                    [
                        'type' => 'hero',
                        'id' => 'hero_1',
                        'config' => [
                            'title' => 'Welcome to Our Academy',
                            'subtitle' => 'Transform your future with our expert-led programs',
                            'background_image' => null,
                            'button_text' => 'Explore Programs',
                            'button_link' => '/programs',
                        ],
                    ],
                    [
                        'type' => 'features',
                        'id' => 'features_1',
                        'config' => [
                            'title' => 'Why Choose Us',
                            'features' => [
                                [
                                    'icon' => 'fas fa-graduation-cap',
                                    'title' => 'Expert Instructors',
                                    'description' => 'Learn from industry professionals',
                                ],
                                [
                                    'icon' => 'fas fa-certificate',
                                    'title' => 'Certified Programs',
                                    'description' => 'Get recognized certificates upon completion',
                                ],
                                [
                                    'icon' => 'fas fa-users',
                                    'title' => 'Community Support',
                                    'description' => 'Join our active learning community',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'cta',
                        'id' => 'cta_1',
                        'config' => [
                            'title' => 'Ready to Start Your Journey?',
                            'description' => 'Join thousands of students learning with us',
                            'button_text' => 'Get Started Now',
                            'button_link' => '/register',
                        ],
                    ],
                ],
                'is_default' => true,
            ]
        );

        // About Page Template
        PageBuilderTemplate::updateOrCreate(
            ['name' => 'About Page'],
            [
                'description' => 'About page with hero, content, and testimonials',
                'structure' => [
                    [
                        'type' => 'hero',
                        'id' => 'hero_2',
                        'config' => [
                            'title' => 'About Our Academy',
                            'subtitle' => 'Empowering students since 2020',
                            'background_image' => null,
                            'button_text' => null,
                            'button_link' => null,
                        ],
                    ],
                    [
                        'type' => 'html',
                        'id' => 'html_1',
                        'config' => [
                            'content' => '<p>We are dedicated to providing high-quality education and training programs that help students achieve their career goals.</p>',
                        ],
                    ],
                    [
                        'type' => 'testimonials',
                        'id' => 'testimonials_1',
                        'config' => [
                            'title' => 'What Our Students Say',
                            'source' => 'dynamic', // or 'static'
                        ],
                    ],
                ],
                'is_default' => false,
            ]
        );

        $this->command->info('Page builder templates seeded successfully!');
    }

    protected function seedHomepage(): void
    {
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))
            ->take(1)
            ->get();

        if ($admins->isEmpty()) {
            $this->command->warn('No admin users found. Skipping homepage creation.');
            return;
        }

        $pageBuilderService = app(PageBuilderService::class);
        
        foreach ($admins as $admin) {
            // Check if homepage already exists
            $existing = PageBuilderPage::where('academy_id', $admin->id)
                ->where('slug', 'home')
                ->first();

            if ($existing) {
                continue;
            }

            try {
                $homepage = $pageBuilderService->createHomepageForAcademy($admin);
                $this->command->info("Homepage created for academy: {$admin->name}");
            } catch (\Exception $e) {
                $this->command->warn("Failed to create homepage for academy {$admin->id}: " . $e->getMessage());
            }
        }

        $this->command->info('Homepages seeded successfully!');
    }
}

