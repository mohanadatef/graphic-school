<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\Session;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    public function courses(Request $request)
    {
        $query = Course::with(['category', 'instructors'])
            ->where('is_hidden', false)
            ->where('is_published', true);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->boolean('only_upcoming', true)) {
            $query->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '>=', Carbon::today());
            });
        }

        $courses = $query->orderBy('start_date')->get();

        return response()->json($courses);
    }

    public function courseShow(Course $course)
    {
        abort_if($course->is_hidden, 404);

        $course->load(['category', 'instructors', 'sessions' => fn ($q) => $q->orderBy('session_order'), 'testimonials']);

        $course->reviews_summary = [
            'count' => $course->testimonials->count(),
            'average' => (float) number_format($course->testimonials->avg('rating_course'), 2),
        ];

        if ($user = auth('api')->user()) {
            $course->enrollment_status = Enrollment::where('student_id', $user->id)
                ->where('course_id', $course->id)
                ->value('status');
        }

        return response()->json($course);
    }

    public function categories()
    {
        return response()->json(Category::where('is_active', true)->orderBy('name')->get());
    }

    public function instructors()
    {
        $instructors = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))
            ->where('is_active', true)
            ->get()
            ->map(function ($instructor) {
                $reviews = CourseReview::where('instructor_id', $instructor->id);
                $instructor->average_rating = (float) number_format($reviews->avg('rating_instructor') ?? 0, 2);
                $instructor->reviews_count = $reviews->count();

                return $instructor;
            });

        return response()->json($instructors);
    }

    public function sliders()
    {
        return response()->json(
            Slider::where('is_active', true)->orderBy('sort_order')->get()
        );
    }

    public function testimonials()
    {
        return response()->json(
            Testimonial::where('is_approved', true)->latest()->take(10)->get()
        );
    }

    public function homeSummary()
    {
        $sliders = Slider::where('is_active', true)->orderBy('sort_order')->get();

        $courses = Course::with('category')
            ->where('is_hidden', false)
            ->where('is_published', true)
            ->orderBy('start_date')
            ->take(6)
            ->get();

        $testimonials = Testimonial::where('is_approved', true)->latest()->take(6)->get();

        $stats = [
            'learners' => User::whereHas('role', fn ($q) => $q->where('name', 'student'))->count(),
            'live_sessions' => Session::whereDate('session_date', '>=', Carbon::today())->count(),
            'projects' => Course::where('is_published', true)->count(),
            'reviews' => Testimonial::where('is_approved', true)->count() + CourseReview::count(),
        ];

        $sessionsThisWeek = Session::whereBetween('session_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->count();

        $resourcesCount = max(Course::sum('session_count'), 0);
        $tracksCount = Category::where('is_active', true)->count();

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

        $upcomingSessions = Session::with('course')
            ->whereDate('session_date', '>=', Carbon::today())
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->take(4)
            ->get()
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

        return response()->json([
            'sliders' => $sliders,
            'courses' => $courses,
            'testimonials' => $testimonials,
            'stats' => $stats,
            'highlight_cards' => $highlightCards,
            'learning_pillars' => $learningPillars,
            'community_features' => $communityFeatures,
            'upcoming_sessions' => $upcomingSessions,
        ]);
    }

    public function settings()
    {
        $keys = [
            'site_name',
            'primary_color',
            'secondary_color',
            'email',
            'phone',
            'address',
            'about_us',
            'logo',
        ];

        $settings = Setting::getMany($keys);

        if (! empty($settings['logo'])) {
            $settings['logo'] = Storage::disk('public')->url($settings['logo']);
        }

        return response()->json($settings);
    }

    public function contact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string'],
        ]);

        ContactMessage::create($data);

        return response()->json(['message' => 'شكراً لتواصلك معنا']);
    }
}
