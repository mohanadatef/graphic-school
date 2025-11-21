<?php

namespace Modules\CMS\PublicSite\Services;

use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Sessions\Models\Session;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Categories\Repositories\Interfaces\CategoryRepositoryInterface;
use Modules\CMS\Contacts\Repositories\Interfaces\ContactMessageRepositoryInterface;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use Modules\LMS\CourseReviews\Repositories\Interfaces\CourseReviewRepositoryInterface;
use Modules\LMS\Enrollments\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Modules\LMS\Sessions\Repositories\Interfaces\SessionRepositoryInterface;
use Modules\CMS\Settings\Repositories\Interfaces\SettingRepositoryInterface;
use Modules\CMS\Sliders\Repositories\Interfaces\SliderRepositoryInterface;
use Modules\CMS\Testimonials\Repositories\Interfaces\TestimonialRepositoryInterface;
use Modules\ACL\Users\Repositories\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicSiteService
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository,
        private EnrollmentRepositoryInterface $enrollmentRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private UserRepositoryInterface $userRepository,
        private SliderRepositoryInterface $sliderRepository,
        private TestimonialRepositoryInterface $testimonialRepository,
        private SessionRepositoryInterface $sessionRepository,
        private SettingRepositoryInterface $settingRepository,
        private ContactMessageRepositoryInterface $contactMessageRepository,
        private CourseReviewRepositoryInterface $courseReviewRepository
    ) {
    }

    public function listCourses(array $filters): Collection
    {
        return $this->courseRepository->publicListing($filters);
    }

    public function courseDetails(Course $course, ?User $viewer = null): Course
    {
        abort_if($course->is_hidden, 404);

        /** @var Course $course */
        $course = $this->courseRepository->loadRelations($course, [
            'category',
            'instructors',
            'sessions' => fn ($q) => $q->orderBy('session_order'),
            'testimonials',
        ]);

        $course->setAttribute('reviews_summary', [
            'count' => $course->testimonials->count(),
            'average' => (float) number_format($course->testimonials->avg('rating_course'), 2),
        ]);

        if ($viewer) {
            $status = $this->enrollmentRepository->getStatusForStudentCourse($viewer->id, $course->id);
            $course->setAttribute('enrollment_status', $status);
        }

        return $course;
    }

    public function categories(): Collection
    {
        return $this->categoryRepository->activeOrdered();
    }

    public function instructors(): Collection
    {
        return $this->userRepository->query()
            ->whereHas('role', fn ($q) => $q->where('name', 'instructor'))
            ->where('is_active', true)
            ->get()
            ->map(function ($instructor) {
                $reviewsQuery = $this->courseReviewRepository->query()->where('instructor_id', $instructor->id);
                $instructor->average_rating = (float) number_format((clone $reviewsQuery)->avg('rating_instructor') ?? 0, 2);
                $instructor->reviews_count = (clone $reviewsQuery)->count();

                return $instructor;
            });
    }

    public function instructorDetails(User $instructor): User
    {
        abort_if($instructor->role->name !== 'instructor', 404);
        abort_if(!$instructor->is_active, 404);

        // Load relationships
        $instructor->load([
            'instructorCourses' => fn ($q) => $q->where('is_published', true)->where('is_hidden', false),
            'instructorCourses.category',
        ]);

        // Calculate ratings
        $reviewsQuery = $this->courseReviewRepository->query()->where('instructor_id', $instructor->id);
        $instructor->average_rating = (float) number_format($reviewsQuery->avg('rating_instructor') ?? 0, 2);
        $instructor->reviews_count = $reviewsQuery->count();

        // Get courses count
        $instructor->courses_count = $instructor->instructorCourses->count();

        // Get total students taught
        $instructor->students_count = $this->enrollmentRepository->query()
            ->whereIn('course_id', $instructor->instructorCourses->pluck('id'))
            ->where('status', 'approved')
            ->distinct('student_id')
            ->count('student_id');

        return $instructor;
    }

    public function sliders(): Collection
    {
        return $this->sliderRepository->activeSorted();
    }

    public function testimonials(int $limit = 10): Collection
    {
        return $this->testimonialRepository->latestApproved($limit);
    }

    /**
     * @return array<string, mixed>
     */
    public function homeSummary(): array
    {
        $sliders = $this->sliders();

        $courses = $this->courseRepository->homeListing(6);

        $testimonials = $this->testimonials(6);

        $stats = [
            'learners' => $this->userRepository->query()
                ->whereHas('role', fn ($q) => $q->where('name', 'student'))
                ->count(),
            'live_sessions' => $this->sessionRepository->query()
                ->whereDate('session_date', '>=', Carbon::today())
                ->count(),
            'projects' => $this->courseRepository->query()
                ->where('is_published', true)
                ->count(),
            'reviews' => $this->testimonialRepository->query()->where('is_approved', true)->count()
                + $this->courseReviewRepository->query()->count(),
        ];

        $sessionsThisWeek = $this->sessionRepository->query()
            ->whereBetween('session_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $resourcesCount = max($this->courseRepository->query()->sum('session_count'), 0);
        $tracksCount = $this->categoryRepository->query()->where('is_active', true)->count();

        $highlightCards = [
            [
                'title' => 'ورش مباشرة',
                'value' => $sessionsThisWeek,
                'badge' => 'Live',
                'icon' => '🎬',
                'trend' => __(':count جلسة هذا الأسبوع', ['count' => $sessionsThisWeek]),
                'description' => 'جلسات تفاعلية يمكن من خلالها مشاركة الشاشات والملفات.',
            ],
            [
                'title' => 'ملفات جاهزة',
                'value' => $resourcesCount,
                'badge' => 'Resources',
                'icon' => '📂',
                'trend' => __('إجمالي المواد التدريبية المتاحة'),
                'description' => 'عروض، قوالب، وملفات محدثة بعد كل جلسة.',
            ],
            [
                'title' => 'مسارات معتمدة',
                'value' => $tracksCount,
                'badge' => 'Tracks',
                'icon' => '🚀',
                'trend' => __('مسارات نشطة حالياً'),
                'description' => 'مسارات تجمع بين براندنج، واجهات، وموشن مع تقارير أداء.',
            ],
        ];

        $learningPillars = [
            ['title' => 'مشاريع أسبوعية', 'description' => 'تنفيذ مشروع قصير كل أسبوع مع متابعة مشرفين.'],
            ['title' => 'تغذية راجعة فورية', 'description' => 'تعليقات صوتية ومكتوبة على كل ملف يتم رفعه.'],
            ['title' => 'مجتمع خاص', 'description' => 'قنوات نقاش مغلقة وتحديات يتم مشاركتها يومياً.'],
            ['title' => 'أرشيف دروس', 'description' => 'تسجيلات تبقى متاحة لثلاثين يوماً بعد نهاية المسار.'],
        ];

        $communityFeatures = [
            ['icon' => '💬', 'title' => 'قنوات نقاش متخصصة', 'description' => 'غرف براندنج، موشن، واجهات مع مدربين متواجدين.'],
            ['icon' => '🎯', 'title' => 'تحديات أسبوعية', 'description' => 'مهام قصيرة تبني عادة التصميم وتحفز على التجربة.'],
            ['icon' => '🧑‍🏫', 'title' => 'مراجعات فردية', 'description' => 'جلسات تقويم فردية قبل تسليم المشاريع النهائية.'],
        ];

        $upcomingSessions = $this->sessionRepository->upcomingForHome(4)
            ->map(function (Session $session) {
                $dateLabel = $session->session_date
                    ? $session->session_date->copy()->locale('ar')->translatedFormat('l d F')
                    : null;

                $timeLabel = $session->start_time
                    ? Carbon::parse($session->start_time)->format('H:i')
                    : null;

                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'course_title' => $session->course?->title,
                    'date_label' => $dateLabel,
                    'time_label' => $timeLabel,
                    'status' => $session->status,
                    'focus' => Str::limit($session->note ?? $session->course?->title, 80),
                ];
            })
            ->values();

        return [
            'sliders' => $sliders,
            'courses' => $courses,
            'testimonials' => $testimonials,
            'stats' => $stats,
            'highlight_cards' => $highlightCards,
            'learning_pillars' => $learningPillars,
            'community_features' => $communityFeatures,
            'upcoming_sessions' => $upcomingSessions,
        ];
    }

    public function settings(array $keys): array
    {
        $settings = $this->settingRepository->getManyByKeys($keys);

        if (! empty($settings['logo'])) {
            $settings['logo'] = Storage::disk('public')->url($settings['logo']);
        }

        return $settings;
    }

    public function storeContactMessage(array $data)
    {
        return $this->contactMessageRepository->create($data);
    }
}

