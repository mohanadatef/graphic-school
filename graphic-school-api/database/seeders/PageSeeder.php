<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * CHANGE-002: CMS Page Builder - Seed initial pages
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'home',
                'title' => 'الصفحة الرئيسية',
                'content' => '<h1>مرحباً بكم في مدرسة الجرافيك</h1><p>نقدم لكم أفضل الدورات التدريبية في مجال التصميم الجرافيكي والبرمجة.</p>',
                'template' => 'home',
                'sections' => [
                    'slider' => true,
                    'testimonials' => true,
                    'featured_courses' => true,
                    'statistics' => true,
                    'faq' => true,
                ],
                'meta_title' => 'مدرسة الجرافيك - الصفحة الرئيسية',
                'meta_description' => 'مدرسة الجرافيك - أفضل الدورات التدريبية في التصميم الجرافيكي والبرمجة',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'about',
                'title' => 'من نحن',
                'content' => '<h1>من نحن</h1><p>مدرسة الجرافيك هي مؤسسة تعليمية متخصصة في تقديم الدورات التدريبية في مجال التصميم الجرافيكي والبرمجة.</p><p>نهدف إلى تطوير مهارات الطلاب وإعدادهم لسوق العمل.</p>',
                'template' => 'about',
                'sections' => [
                    'slider' => false,
                    'testimonials' => true,
                    'featured_courses' => false,
                    'statistics' => true,
                    'faq' => false,
                ],
                'meta_title' => 'من نحن - مدرسة الجرافيك',
                'meta_description' => 'تعرف على مدرسة الجرافيك ورسالتنا وأهدافنا',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'courses',
                'title' => 'الدورات التدريبية',
                'content' => '<h1>الدورات التدريبية</h1><p>اكتشف مجموعة واسعة من الدورات التدريبية في مختلف المجالات.</p>',
                'template' => 'default',
                'meta_title' => 'الدورات التدريبية - مدرسة الجرافيك',
                'meta_description' => 'تصفح جميع الدورات التدريبية المتاحة في مدرسة الجرافيك',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'slug' => 'instructors',
                'title' => 'المدربين',
                'content' => '<h1>مدربونا المتميزون</h1><p>تعرف على فريقنا من المدربين المحترفين ذوي الخبرة الواسعة.</p>',
                'template' => 'default',
                'meta_title' => 'المدربين - مدرسة الجرافيك',
                'meta_description' => 'تعرف على فريق المدربين المحترفين في مدرسة الجرافيك',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'slug' => 'contact',
                'title' => 'تواصل معنا',
                'content' => '<h1>تواصل معنا</h1><p>نحن هنا للإجابة على جميع استفساراتك. لا تتردد في التواصل معنا.</p>',
                'template' => 'contact',
                'meta_title' => 'تواصل معنا - مدرسة الجرافيك',
                'meta_description' => 'تواصل معنا للحصول على المزيد من المعلومات حول دوراتنا التدريبية',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }

        $this->command->info('Pages seeded successfully!');
    }
}

