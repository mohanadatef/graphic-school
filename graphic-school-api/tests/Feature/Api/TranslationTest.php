<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Curriculum\Models\CourseModule;
use Modules\LMS\Curriculum\Models\Lesson;
use Modules\LMS\Sessions\Models\Session;
use App\Models\Page;
use App\Models\FAQ;
use Modules\CMS\Testimonials\Models\Testimonial;
use Modules\CMS\Sliders\Models\Slider;
use App\Models\CourseTranslation;
use App\Models\PageTranslation;
use App\Models\FAQTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $adminToken;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::factory()->create(['name' => 'admin']);
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);
        $this->adminToken = $this->admin->createToken('test-token')->plainTextToken;
        $this->category = Category::factory()->create();
    }

    // ==================== Course Translation Tests ====================

    public function test_can_create_course_with_translations(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/courses', [
                'category_id' => $this->category->id,
                'code' => 'TC001',
                'price' => 1000,
                'session_count' => 10,
                'translations' => [
                    [
                        'locale' => 'en',
                        'title' => 'Test Course',
                        'description' => 'Test Description in English',
                        'meta_title' => 'Test Course - Meta Title',
                        'meta_description' => 'Test Course - Meta Description',
                    ],
                    [
                        'locale' => 'ar',
                        'title' => 'دورة تجريبية',
                        'description' => 'وصف تجريبي بالعربية',
                        'meta_title' => 'دورة تجريبية - العنوان',
                        'meta_description' => 'دورة تجريبية - الوصف',
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'title', 'description'],
            ]);

        $courseId = $response->json('data.id');
        
        // Verify translations were saved
        $this->assertDatabaseHas('course_translations', [
            'course_id' => $courseId,
            'locale' => 'en',
            'title' => 'Test Course',
        ]);
        
        $this->assertDatabaseHas('course_translations', [
            'course_id' => $courseId,
            'locale' => 'ar',
            'title' => 'دورة تجريبية',
        ]);
    }

    public function test_can_update_course_with_translations(): void
    {
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'code' => 'TC001',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->putJson("/api/admin/courses/{$course->id}", [
                'translations' => [
                    [
                        'locale' => 'en',
                        'title' => 'Updated Course Title',
                        'description' => 'Updated Description',
                    ],
                    [
                        'locale' => 'ar',
                        'title' => 'عنوان محدث',
                        'description' => 'وصف محدث',
                    ],
                ],
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('course_translations', [
            'course_id' => $course->id,
            'locale' => 'en',
            'title' => 'Updated Course Title',
        ]);
    }

    public function test_course_api_returns_translated_content_based_on_locale(): void
    {
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'code' => 'TC001',
        ]);

        CourseTranslation::create([
            'course_id' => $course->id,
            'locale' => 'en',
            'title' => 'English Title',
            'description' => 'English Description',
        ]);

        CourseTranslation::create([
            'course_id' => $course->id,
            'locale' => 'ar',
            'title' => 'العنوان بالعربية',
            'description' => 'الوصف بالعربية',
        ]);

        // Test English locale
        $responseEn = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->withHeader('Accept-Language', 'en')
            ->getJson("/api/admin/courses/{$course->id}");

        $responseEn->assertStatus(200)
            ->assertJsonPath('data.title', 'English Title');

        // Test Arabic locale
        $responseAr = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->withHeader('Accept-Language', 'ar')
            ->getJson("/api/admin/courses/{$course->id}");

        $responseAr->assertStatus(200)
            ->assertJsonPath('data.title', 'العنوان بالعربية');
    }

    public function test_course_api_falls_back_to_default_locale_when_translation_missing(): void
    {
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'code' => 'TC001',
        ]);

        // Only create English translation
        CourseTranslation::create([
            'course_id' => $course->id,
            'locale' => 'en',
            'title' => 'English Title',
            'description' => 'English Description',
        ]);

        // Request Arabic, should fallback to English
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->withHeader('Accept-Language', 'ar')
            ->getJson("/api/admin/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'English Title');
    }

    // ==================== Page Translation Tests ====================

    public function test_can_create_page_with_translations(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/pages', [
                'slug' => 'test-page',
                'is_active' => true,
                'translations' => [
                    [
                        'locale' => 'en',
                        'title' => 'Test Page',
                        'content' => '<p>English content</p>',
                        'meta_title' => 'Test Page - Meta',
                    ],
                    [
                        'locale' => 'ar',
                        'title' => 'صفحة تجريبية',
                        'content' => '<p>المحتوى بالعربية</p>',
                        'meta_title' => 'صفحة تجريبية - العنوان',
                    ],
                ],
            ]);

        $response->assertStatus(201);

        $pageId = $response->json('data.id');
        
        $this->assertDatabaseHas('page_translations', [
            'page_id' => $pageId,
            'locale' => 'en',
            'title' => 'Test Page',
        ]);
    }

    public function test_page_api_returns_translated_content(): void
    {
        $page = Page::factory()->create(['slug' => 'test-page']);

        PageTranslation::create([
            'page_id' => $page->id,
            'locale' => 'en',
            'title' => 'English Page Title',
            'content' => 'English content',
        ]);

        PageTranslation::create([
            'page_id' => $page->id,
            'locale' => 'ar',
            'title' => 'عنوان الصفحة بالعربية',
            'content' => 'المحتوى بالعربية',
        ]);

        $response = $this->withHeader('Accept-Language', 'ar')
            ->getJson("/api/pages/test-page");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'عنوان الصفحة بالعربية');
    }

    // ==================== FAQ Translation Tests ====================

    public function test_can_create_faq_with_translations(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/faqs', [
                'is_active' => true,
                'translations' => [
                    [
                        'locale' => 'en',
                        'question' => 'What is this?',
                        'answer' => 'This is a test answer.',
                    ],
                    [
                        'locale' => 'ar',
                        'question' => 'ما هذا؟',
                        'answer' => 'هذه إجابة تجريبية.',
                    ],
                ],
            ]);

        $response->assertStatus(201);

        $faqId = $response->json('data.id');
        
        $this->assertDatabaseHas('faq_translations', [
            'faq_id' => $faqId,
            'locale' => 'en',
            'question' => 'What is this?',
        ]);
    }

    public function test_faq_api_returns_translated_content(): void
    {
        $faq = FAQ::factory()->create();

        FAQTranslation::create([
            'faq_id' => $faq->id,
            'locale' => 'en',
            'question' => 'English Question?',
            'answer' => 'English Answer',
        ]);

        FAQTranslation::create([
            'faq_id' => $faq->id,
            'locale' => 'ar',
            'question' => 'سؤال بالعربية؟',
            'answer' => 'إجابة بالعربية',
        ]);

        $response = $this->withHeader('Accept-Language', 'ar')
            ->getJson('/api/faqs');

        $response->assertStatus(200);
        $faqs = $response->json('data');
        $this->assertNotEmpty($faqs);
        $this->assertEquals('سؤال بالعربية؟', $faqs[0]['question']);
    }

    // ==================== Module Translation Tests ====================

    public function test_can_create_module_with_translations(): void
    {
        $course = Course::factory()->create(['category_id' => $this->category->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson("/api/admin/courses/{$course->id}/modules", [
                'order' => 1,
                'is_published' => true,
                'translations' => [
                    [
                        'locale' => 'en',
                        'title' => 'Module Title',
                        'description' => 'Module Description',
                    ],
                    [
                        'locale' => 'ar',
                        'title' => 'عنوان الوحدة',
                        'description' => 'وصف الوحدة',
                    ],
                ],
            ]);

        $response->assertStatus(201);

        $moduleId = $response->json('data.id');
        
        $this->assertDatabaseHas('course_module_translations', [
            'course_module_id' => $moduleId,
            'locale' => 'en',
            'title' => 'Module Title',
        ]);
    }

    // ==================== Lesson Translation Tests ====================

    public function test_can_create_lesson_with_translations(): void
    {
        $course = Course::factory()->create(['category_id' => $this->category->id]);
        $module = CourseModule::factory()->create(['course_id' => $course->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson("/api/admin/modules/{$module->id}/lessons", [
                'order' => 1,
                'is_published' => true,
                'translations' => [
                    [
                        'locale' => 'en',
                        'title' => 'Lesson Title',
                        'description' => 'Lesson Description',
                        'content' => 'Lesson Content',
                    ],
                    [
                        'locale' => 'ar',
                        'title' => 'عنوان الدرس',
                        'description' => 'وصف الدرس',
                        'content' => 'محتوى الدرس',
                    ],
                ],
            ]);

        $response->assertStatus(201);

        $lessonId = $response->json('data.id');
        
        $this->assertDatabaseHas('lesson_translations', [
            'lesson_id' => $lessonId,
            'locale' => 'en',
            'title' => 'Lesson Title',
        ]);
    }

    // ==================== Locale Detection Tests ====================

    public function test_locale_detection_from_header(): void
    {
        $course = Course::factory()->create(['category_id' => $this->category->id]);

        CourseTranslation::create([
            'course_id' => $course->id,
            'locale' => 'ar',
            'title' => 'العنوان',
        ]);

        $response = $this->withHeader('Accept-Language', 'ar')
            ->getJson("/api/courses/{$course->id}");

        $response->assertStatus(200);
    }

    public function test_locale_detection_from_query_param(): void
    {
        $course = Course::factory()->create(['category_id' => $this->category->id]);

        CourseTranslation::create([
            'course_id' => $course->id,
            'locale' => 'ar',
            'title' => 'العنوان',
        ]);

        $response = $this->getJson("/api/courses/{$course->id}?locale=ar");

        $response->assertStatus(200);
    }

    // ==================== Translation Validation Tests ====================

    public function test_validation_requires_translations_on_create(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/courses', [
                'category_id' => $this->category->id,
                'code' => 'TC001',
                // Missing translations
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['translations']);
    }

    public function test_validation_requires_valid_locale(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/courses', [
                'category_id' => $this->category->id,
                'code' => 'TC001',
                'translations' => [
                    [
                        'locale' => 'invalid', // Invalid locale
                        'title' => 'Test',
                    ],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['translations.0.locale']);
    }
}

