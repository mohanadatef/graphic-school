<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create default CMS pages with blocks
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'home',
                'title' => [
                    'en' => 'Home',
                    'ar' => 'الرئيسية',
                ],
                'content' => [
                    'en' => '<h1>Welcome to Graphic School</h1><p>We offer the best training courses in graphic design and programming.</p>',
                    'ar' => '<h1>مرحباً بكم في مدرسة الجرافيك</h1><p>نقدم لكم أفضل الدورات التدريبية في مجال التصميم الجرافيكي والبرمجة.</p>',
                ],
                'meta_description' => [
                    'en' => 'Graphic School - Best training courses in graphic design and programming',
                    'ar' => 'مدرسة الجرافيك - أفضل الدورات التدريبية في التصميم الجرافيكي والبرمجة',
                ],
                'is_active' => true,
                'sort_order' => 1,
                'blocks' => [
                    [
                        'type' => 'hero',
                        'title' => ['en' => 'Welcome to Graphic School', 'ar' => 'مرحباً بكم في مدرسة الجرافيك'],
                        'content' => ['en' => 'Transform your creative skills', 'ar' => 'طور مهاراتك الإبداعية'],
                        'config' => ['button_text' => 'Explore Courses', 'button_link' => '/courses'],
                        'sort_order' => 1,
                    ],
                    [
                        'type' => 'features',
                        'title' => ['en' => 'Why Choose Us', 'ar' => 'لماذا تختارنا'],
                        'content' => ['en' => 'We offer the best learning experience', 'ar' => 'نوفر أفضل تجربة تعليمية'],
                        'config' => [],
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'slug' => 'about',
                'title' => [
                    'en' => 'About Us',
                    'ar' => 'من نحن',
                ],
                'content' => [
                    'en' => '<h1>About Us</h1><p>Graphic School is an educational institution specialized in providing training courses in graphic design and programming.</p><p>We aim to develop students\' skills and prepare them for the job market.</p>',
                    'ar' => '<h1>من نحن</h1><p>مدرسة الجرافيك هي مؤسسة تعليمية متخصصة في تقديم الدورات التدريبية في مجال التصميم الجرافيكي والبرمجة.</p><p>نهدف إلى تطوير مهارات الطلاب وإعدادهم لسوق العمل.</p>',
                ],
                'meta_description' => [
                    'en' => 'Learn about Graphic School, our mission and goals',
                    'ar' => 'تعرف على مدرسة الجرافيك ورسالتنا وأهدافنا',
                ],
                'is_active' => true,
                'sort_order' => 2,
                'blocks' => [
                    [
                        'type' => 'content',
                        'title' => ['en' => 'Our Story', 'ar' => 'قصتنا'],
                        'content' => ['en' => 'We have been providing quality education for years', 'ar' => 'نقدم التعليم الجيد منذ سنوات'],
                        'config' => [],
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'slug' => 'contact',
                'title' => [
                    'en' => 'Contact Us',
                    'ar' => 'اتصل بنا',
                ],
                'content' => [
                    'en' => '<h1>Contact Us</h1><p>We are here to answer all your inquiries. Feel free to contact us.</p>',
                    'ar' => '<h1>تواصل معنا</h1><p>نحن هنا للإجابة على جميع استفساراتك. لا تتردد في التواصل معنا.</p>',
                ],
                'meta_description' => [
                    'en' => 'Contact us for more information about our training courses',
                    'ar' => 'تواصل معنا للحصول على المزيد من المعلومات حول دوراتنا التدريبية',
                ],
                'is_active' => true,
                'sort_order' => 3,
                'blocks' => [
                    [
                        'type' => 'contact',
                        'title' => ['en' => 'Get in Touch', 'ar' => 'تواصل معنا'],
                        'content' => ['en' => 'Fill out the form below', 'ar' => 'املأ النموذج أدناه'],
                        'config' => [],
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'slug' => 'faq',
                'title' => [
                    'en' => 'Frequently Asked Questions',
                    'ar' => 'الأسئلة الشائعة',
                ],
                'content' => [
                    'en' => '<h1>Frequently Asked Questions</h1><p>Find answers to the most common questions about our courses and services.</p>',
                    'ar' => '<h1>الأسئلة الشائعة</h1><p>ابحث عن إجابات لأكثر الأسئلة شيوعاً حول دوراتنا وخدماتنا.</p>',
                ],
                'meta_description' => [
                    'en' => 'Find answers to frequently asked questions about Graphic School',
                    'ar' => 'ابحث عن إجابات للأسئلة الشائعة حول مدرسة الجرافيك',
                ],
                'is_active' => true,
                'sort_order' => 4,
                'blocks' => [
                    [
                        'type' => 'faq',
                        'title' => ['en' => 'Common Questions', 'ar' => 'الأسئلة الشائعة'],
                        'content' => ['en' => 'Answers to your questions', 'ar' => 'إجابات على أسئلتك'],
                        'config' => [],
                        'sort_order' => 1,
                    ],
                ],
            ],
        ];

        foreach ($pages as $pageData) {
            $blocks = $pageData['blocks'] ?? [];
            unset($pageData['blocks']);

            $page = Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );

            // Create blocks for the page
            foreach ($blocks as $index => $blockData) {
                PageBlock::updateOrCreate(
                    [
                        'page_id' => $page->id,
                        'type' => $blockData['type'],
                        'sort_order' => $blockData['sort_order'],
                    ],
                    [
                        'title' => $blockData['title'] ?? [],
                        'content' => $blockData['content'] ?? [],
                        'config' => $blockData['config'] ?? [],
                        'is_enabled' => true,
                    ]
                );
            }
        }

        $this->command->info('✓ Default CMS pages seeded successfully!');
    }
}

