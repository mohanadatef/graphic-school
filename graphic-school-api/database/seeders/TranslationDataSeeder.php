<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Curriculum\Models\CourseModule;
use Modules\LMS\Curriculum\Models\Lesson;
use Modules\LMS\Sessions\Models\Session;
use App\Models\Page;
use App\Models\FAQ;
use Modules\CMS\Testimonials\Models\Testimonial;
use Modules\CMS\Sliders\Models\Slider;
use App\Services\EntityTranslationService;
use Illuminate\Support\Facades\DB;

class TranslationDataSeeder extends Seeder
{
    protected EntityTranslationService $translationService;

    public function __construct()
    {
        $this->translationService = app(EntityTranslationService::class);
    }

    public function run(): void
    {
        $this->command->info('Seeding translations for existing entities...');

        $this->seedCourseTranslations();
        $this->seedModuleTranslations();
        $this->seedLessonTranslations();
        $this->seedSessionTranslations();
        $this->seedPageTranslations();
        $this->seedFAQTranslations();
        $this->seedTestimonialTranslations();
        $this->seedSliderTranslations();

        $this->command->info('Translations seeded successfully!');
    }

    protected function seedCourseTranslations(): void
    {
        $courses = Course::take(2)->get();
        
        if ($courses->isEmpty()) {
            $this->command->warn('No courses found to seed translations for.');
            return;
        }
        
        $translations = [
            [
                'en' => [
                    'title' => 'Professional Branding Bootcamp',
                    'description' => 'Master the art of professional branding and identity design. Learn to create memorable brand identities that resonate with audiences.',
                    'meta_title' => 'Professional Branding Bootcamp - Graphic School',
                    'meta_description' => 'Learn professional branding and identity design in this comprehensive bootcamp.',
                ],
                'ar' => [
                    'title' => 'معسكر البراندنج الاحترافي',
                    'description' => 'أتقن فن البراندنج والهوية البصرية الاحترافية. تعلم إنشاء هويات علامات تجارية لا تُنسى تتردد صداها مع الجماهير.',
                    'meta_title' => 'معسكر البراندنج الاحترافي - مدرسة التصميم الجرافيكي',
                    'meta_description' => 'تعلم البراندنج والهوية البصرية الاحترافية في هذا المعسكر الشامل.',
                ],
            ],
            [
                'en' => [
                    'title' => 'Advanced Illustration Lab',
                    'description' => 'Explore advanced illustration techniques and digital art creation. Perfect for artists looking to elevate their skills.',
                    'meta_title' => 'Advanced Illustration Lab - Graphic School',
                    'meta_description' => 'Master advanced illustration techniques in this intensive lab course.',
                ],
                'ar' => [
                    'title' => 'مختبر الرسم التوضيحي المتقدم',
                    'description' => 'استكشف تقنيات الرسم التوضيحي المتقدمة وإنشاء الفن الرقمي. مثالي للفنانين الذين يتطلعون إلى رفع مهاراتهم.',
                    'meta_title' => 'مختبر الرسم التوضيحي المتقدم - مدرسة التصميم الجرافيكي',
                    'meta_description' => 'أتقن تقنيات الرسم التوضيحي المتقدمة في هذه الدورة المكثفة.',
                ],
            ],
        ];

        foreach ($courses as $index => $course) {
            if (isset($translations[$index])) {
                try {
                    $this->translationService->saveTranslations($course, [
                        ['locale' => 'en'] + $translations[$index]['en'],
                        ['locale' => 'ar'] + $translations[$index]['ar'],
                    ]);
                    $this->command->info("Added translations for course: {$course->id}");
                } catch (\Exception $e) {
                    $this->command->error("Failed to add translations for course {$course->id}: " . $e->getMessage());
                }
            }
        }
    }

    protected function seedModuleTranslations(): void
    {
        $modules = CourseModule::take(4)->get();
        
        if ($modules->isEmpty()) {
            $this->command->warn('No modules found to seed translations for.');
            return;
        }
        
        $moduleTranslations = [
            [
                'en' => ['title' => 'Introduction to the Course', 'description' => 'This module provides an overview of the course and fundamental concepts.'],
                'ar' => ['title' => 'مقدمة في الكورس', 'description' => 'هذه الوحدة تقدم نظرة عامة على الكورس والمفاهيم الأساسية.'],
            ],
            [
                'en' => ['title' => 'Core Concepts', 'description' => 'Learn the core concepts and principles that form the foundation of this field.'],
                'ar' => ['title' => 'المفاهيم الأساسية', 'description' => 'تعلم المفاهيم والمبادئ الأساسية التي تشكل أساس هذا المجال.'],
            ],
            [
                'en' => ['title' => 'Practical Applications', 'description' => 'Apply what you\'ve learned through hands-on projects and real-world scenarios.'],
                'ar' => ['title' => 'التطبيقات العملية', 'description' => 'طبق ما تعلمته من خلال المشاريع العملية وسيناريوهات العالم الحقيقي.'],
            ],
            [
                'en' => ['title' => 'Advanced Techniques', 'description' => 'Master advanced techniques and take your skills to the next level.'],
                'ar' => ['title' => 'التقنيات المتقدمة', 'description' => 'أتقن التقنيات المتقدمة وارفع مهاراتك إلى المستوى التالي.'],
            ],
        ];

        foreach ($modules as $index => $module) {
            $translationIndex = $index % count($moduleTranslations);
            if (isset($moduleTranslations[$translationIndex])) {
                try {
                    $this->translationService->saveTranslations($module, [
                        ['locale' => 'en'] + $moduleTranslations[$translationIndex]['en'],
                        ['locale' => 'ar'] + $moduleTranslations[$translationIndex]['ar'],
                    ]);
                } catch (\Exception $e) {
                    $this->command->error("Failed to add translations for module {$module->id}: " . $e->getMessage());
                }
            }
        }
    }

    protected function seedLessonTranslations(): void
    {
        $lessons = Lesson::take(10)->get();
        
        $lessonTranslations = [
            [
                'en' => [
                    'title' => 'Welcome to the Course',
                    'description' => 'Get to know the course and learning objectives.',
                    'content' => '<p>Welcome to this exceptional course. Together we will learn everything you need to master this field.</p>',
                ],
                'ar' => [
                    'title' => 'مرحباً بك في الكورس',
                    'description' => 'تعرف على الكورس والأهداف التعليمية.',
                    'content' => '<p>مرحباً بك في هذا الكورس المميز. سنتعلم معاً كل ما تحتاجه لإتقان هذا المجال.</p>',
                ],
            ],
            [
                'en' => [
                    'title' => 'Getting Started',
                    'description' => 'Learn the basics and set up your workspace.',
                    'content' => '<p>In this lesson, we will cover the basics and help you set up your workspace for success.</p>',
                ],
                'ar' => [
                    'title' => 'البدء',
                    'description' => 'تعلم الأساسيات وإعداد مساحة العمل الخاصة بك.',
                    'content' => '<p>في هذا الدرس، سنغطي الأساسيات ونساعدك في إعداد مساحة العمل الخاصة بك للنجاح.</p>',
                ],
            ],
        ];

        foreach ($lessons as $index => $lesson) {
            $translationIndex = $index % count($lessonTranslations);
            if (isset($lessonTranslations[$translationIndex])) {
                $this->translationService->saveTranslations($lesson, [
                    ['locale' => 'en'] + $lessonTranslations[$translationIndex]['en'],
                    ['locale' => 'ar'] + $lessonTranslations[$translationIndex]['ar'],
                ]);
            }
        }
    }

    protected function seedSessionTranslations(): void
    {
        $sessions = Session::take(3)->get();
        
        $sessionTranslations = [
            [
                'en' => ['title' => 'Introduction Session', 'note' => 'This session covers the introduction and course overview.'],
                'ar' => ['title' => 'جلسة المقدمة', 'note' => 'تغطي هذه الجلسة المقدمة ونظرة عامة على الكورس.'],
            ],
            [
                'en' => ['title' => 'Core Concepts Session', 'note' => 'We will explore the core concepts in detail.'],
                'ar' => ['title' => 'جلسة المفاهيم الأساسية', 'note' => 'سنتعرف على المفاهيم الأساسية بالتفصيل.'],
            ],
            [
                'en' => ['title' => 'Practical Workshop', 'note' => 'Hands-on practice session with real-world examples.'],
                'ar' => ['title' => 'ورشة عمل عملية', 'note' => 'جلسة ممارسة عملية مع أمثلة من العالم الحقيقي.'],
            ],
        ];

        foreach ($sessions as $index => $session) {
            $translationIndex = $index % count($sessionTranslations);
            if (isset($sessionTranslations[$translationIndex])) {
                $this->translationService->saveTranslations($session, [
                    ['locale' => 'en'] + $sessionTranslations[$translationIndex]['en'],
                    ['locale' => 'ar'] + $sessionTranslations[$translationIndex]['ar'],
                ]);
            }
        }
    }

    protected function seedPageTranslations(): void
    {
        $pages = Page::take(2)->get();
        
        if ($pages->isEmpty()) {
            $this->command->warn('No pages found to seed translations for.');
            return;
        }
        
        $pageTranslations = [
            [
                'en' => [
                    'title' => 'Home',
                    'content' => '<h1>Welcome to Our Academy</h1><p>Discover world-class graphic design courses and transform your creative skills.</p>',
                    'meta_title' => 'Home - Graphic School',
                    'meta_description' => 'Welcome to Graphic School, your gateway to professional graphic design education.',
                    'sections' => [
                        'slider' => true,
                        'testimonials' => true,
                        'featured_courses' => true,
                        'statistics' => true,
                        'faq' => true,
                    ],
                ],
                'ar' => [
                    'title' => 'الرئيسية',
                    'content' => '<h1>مرحباً بك في أكاديميتنا</h1><p>اكتشف دورات التصميم الجرافيكي ذات المستوى العالمي وحول مهاراتك الإبداعية.</p>',
                    'meta_title' => 'الرئيسية - مدرسة التصميم الجرافيكي',
                    'meta_description' => 'مرحباً بك في مدرسة التصميم الجرافيكي، بوابتك إلى التعليم الاحترافي في التصميم الجرافيكي.',
                    'sections' => [
                        'slider' => true,
                        'testimonials' => true,
                        'featured_courses' => true,
                        'statistics' => true,
                        'faq' => true,
                    ],
                ],
            ],
            [
                'en' => [
                    'title' => 'About Us',
                    'content' => '<h1>About Our Academy</h1><p>We are dedicated to providing the best graphic design education and helping students achieve their creative goals.</p>',
                    'meta_title' => 'About Us - Graphic School',
                    'meta_description' => 'Learn about our mission, values, and commitment to excellence in graphic design education.',
                    'sections' => [
                        'testimonials' => true,
                        'statistics' => true,
                    ],
                ],
                'ar' => [
                    'title' => 'من نحن',
                    'content' => '<h1>عن أكاديميتنا</h1><p>نحن ملتزمون بتقديم أفضل تعليم في التصميم الجرافيكي ومساعدة الطلاب على تحقيق أهدافهم الإبداعية.</p>',
                    'meta_title' => 'من نحن - مدرسة التصميم الجرافيكي',
                    'meta_description' => 'تعرف على مهمتنا وقيمنا والتزامنا بالتميز في تعليم التصميم الجرافيكي.',
                    'sections' => [
                        'testimonials' => true,
                        'statistics' => true,
                    ],
                ],
            ],
        ];

        foreach ($pages as $index => $page) {
            if (isset($pageTranslations[$index])) {
                try {
                    $this->translationService->saveTranslations($page, [
                        ['locale' => 'en'] + $pageTranslations[$index]['en'],
                        ['locale' => 'ar'] + $pageTranslations[$index]['ar'],
                    ]);
                } catch (\Exception $e) {
                    $this->command->error("Failed to add translations for page {$page->id}: " . $e->getMessage());
                }
            }
        }
    }

    protected function seedFAQTranslations(): void
    {
        $faqs = FAQ::take(2)->get();
        
        if ($faqs->isEmpty()) {
            $this->command->warn('No FAQs found to seed translations for.');
            return;
        }
        
        $faqTranslations = [
            [
                'en' => [
                    'question' => 'How do I enroll in a course?',
                    'answer' => 'You can enroll in a course by browsing our course catalog, selecting a course, and completing the enrollment form. Our team will review your application and notify you of the status.',
                ],
                'ar' => [
                    'question' => 'كيف يمكنني التسجيل في دورة؟',
                    'answer' => 'يمكنك التسجيل في دورة من خلال تصفح كتالوج الدورات، واختيار دورة، وإكمال نموذج التسجيل. سيراجع فريقنا طلبك ويخطرك بالحالة.',
                ],
            ],
            [
                'en' => [
                    'question' => 'What payment methods do you accept?',
                    'answer' => 'We accept various payment methods including credit cards, bank transfers, and installment plans. Please contact our support team for more information.',
                ],
                'ar' => [
                    'question' => 'ما هي طرق الدفع التي تقبلونها؟',
                    'answer' => 'نقبل طرق دفع متنوعة بما في ذلك البطاقات الائتمانية والتحويلات البنكية وخطط التقسيط. يرجى الاتصال بفريق الدعم لمزيد من المعلومات.',
                ],
            ],
        ];

        foreach ($faqs as $index => $faq) {
            if (isset($faqTranslations[$index])) {
                try {
                    $this->translationService->saveTranslations($faq, [
                        ['locale' => 'en'] + $faqTranslations[$index]['en'],
                        ['locale' => 'ar'] + $faqTranslations[$index]['ar'],
                    ]);
                } catch (\Exception $e) {
                    $this->command->error("Failed to add translations for FAQ {$faq->id}: " . $e->getMessage());
                }
            }
        }
    }

    protected function seedTestimonialTranslations(): void
    {
        $testimonials = Testimonial::take(2)->get();
        
        if ($testimonials->isEmpty()) {
            $this->command->warn('No testimonials found to seed translations for.');
            return;
        }
        
        $testimonialTranslations = [
            [
                'en' => ['comment' => 'This course completely transformed my design skills. The instructors are excellent and the content is top-notch. Highly recommended!'],
                'ar' => ['comment' => 'هذه الدورة غيرت مهاراتي في التصميم تماماً. المدربون ممتازون والمحتوى من الطراز الأول. أنصح بها بشدة!'],
            ],
            [
                'en' => ['comment' => 'I learned so much in a short time. The practical approach and real-world projects made all the difference. Thank you!'],
                'ar' => ['comment' => 'تعلمت الكثير في وقت قصير. النهج العملي والمشاريع الواقعية أحدثا كل الفرق. شكراً لكم!'],
            ],
        ];

        foreach ($testimonials as $index => $testimonial) {
            if (isset($testimonialTranslations[$index])) {
                try {
                    $this->translationService->saveTranslations($testimonial, [
                        ['locale' => 'en'] + $testimonialTranslations[$index]['en'],
                        ['locale' => 'ar'] + $testimonialTranslations[$index]['ar'],
                    ]);
                } catch (\Exception $e) {
                    $this->command->error("Failed to add translations for testimonial {$testimonial->id}: " . $e->getMessage());
                }
            }
        }
    }

    protected function seedSliderTranslations(): void
    {
        $sliders = Slider::take(3)->get();
        
        if ($sliders->isEmpty()) {
            $this->command->warn('No sliders found to seed translations for.');
            return;
        }
        
        $sliderTranslations = [
            [
                'en' => [
                    'title' => 'Transform Your Creative Skills',
                    'subtitle' => 'Join thousands of students learning professional graphic design',
                    'button_text' => 'Explore Courses',
                ],
                'ar' => [
                    'title' => 'حول مهاراتك الإبداعية',
                    'subtitle' => 'انضم إلى آلاف الطلاب الذين يتعلمون التصميم الجرافيكي الاحترافي',
                    'button_text' => 'استكشف الدورات',
                ],
            ],
            [
                'en' => [
                    'title' => 'Learn from Industry Experts',
                    'subtitle' => 'Get hands-on experience with real-world projects',
                    'button_text' => 'Start Learning',
                ],
                'ar' => [
                    'title' => 'تعلم من خبراء الصناعة',
                    'subtitle' => 'احصل على خبرة عملية مع مشاريع من العالم الحقيقي',
                    'button_text' => 'ابدأ التعلم',
                ],
            ],
            [
                'en' => [
                    'title' => 'Build Your Portfolio',
                    'subtitle' => 'Create stunning designs and showcase your talent',
                    'button_text' => 'View Programs',
                ],
                'ar' => [
                    'title' => 'ابني محفظتك',
                    'subtitle' => 'أنشئ تصاميم مذهلة وعرض موهبتك',
                    'button_text' => 'عرض البرامج',
                ],
            ],
        ];

        foreach ($sliders as $index => $slider) {
            if (isset($sliderTranslations[$index])) {
                try {
                    $this->translationService->saveTranslations($slider, [
                        ['locale' => 'en'] + $sliderTranslations[$index]['en'],
                        ['locale' => 'ar'] + $sliderTranslations[$index]['ar'],
                    ]);
                } catch (\Exception $e) {
                    $this->command->error("Failed to add translations for slider {$slider->id}: " . $e->getMessage());
                }
            }
        }
    }
}

