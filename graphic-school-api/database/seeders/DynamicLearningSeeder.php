<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Batch;
use App\Models\Group;
use App\Models\BatchSchedule;
use App\Services\EntityTranslationService;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Sessions\Models\Session;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Categories\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DynamicLearningSeeder extends Seeder
{
    protected EntityTranslationService $translationService;

    public function __construct()
    {
        $this->translationService = app(EntityTranslationService::class);
    }

    public function run(): void
    {
        $this->command->info('Seeding Dynamic Learning Structure (Programs → Batches → Groups → Sessions)...');

        // Get instructors and students
        $instructors = User::whereHas('role', fn($q) => $q->where('name', 'instructor'))
            ->take(4)
            ->get();
        
        $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))
            ->take(20)
            ->get();

        if ($instructors->isEmpty() || $students->isEmpty()) {
            $this->command->warn('Insufficient instructors or students. Please seed users first.');
            return;
        }

        // Program 1: Graphic Design Bootcamp
        $program1 = $this->createProgram([
            'type' => 'bootcamp',
            'duration_weeks' => 12,
            'price' => 5000,
            'level' => 'beginner',
            'is_active' => true,
            'sort_order' => 1,
            'translations' => [
                [
                    'locale' => 'en',
                    'title' => 'Graphic Design Bootcamp',
                    'description' => 'A comprehensive 12-week bootcamp covering all aspects of graphic design, from fundamentals to advanced techniques. Perfect for beginners looking to start a career in design.',
                    'meta_title' => 'Graphic Design Bootcamp - Professional Training',
                    'meta_description' => 'Join our intensive 12-week Graphic Design Bootcamp and master the skills needed for a successful design career.',
                ],
                [
                    'locale' => 'ar',
                    'title' => 'معسكر التصميم الجرافيكي',
                    'description' => 'معسكر شامل لمدة 12 أسبوعاً يغطي جميع جوانب التصميم الجرافيكي، من الأساسيات إلى التقنيات المتقدمة. مثالي للمبتدئين الذين يتطلعون لبدء مسيرة مهنية في التصميم.',
                    'meta_title' => 'معسكر التصميم الجرافيكي - تدريب احترافي',
                    'meta_description' => 'انضم إلى معسكر التصميم الجرافيكي المكثف لمدة 12 أسبوعاً وأتقن المهارات اللازمة لمسيرة مهنية ناجحة في التصميم.',
                ],
            ],
        ]);

        // Batch 1: Jan-Mar
        $batch1 = $this->createBatch($program1, [
            'code' => 'GDB-2025-Q1',
            'start_date' => Carbon::parse('2025-01-15'),
            'end_date' => Carbon::parse('2025-03-31'),
            'max_students' => 40,
            'is_active' => true,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'January - March 2025',
                    'description' => 'First quarter batch starting in January 2025.',
                ],
                [
                    'locale' => 'ar',
                    'name' => 'يناير - مارس 2025',
                    'description' => 'دفعة الربع الأول تبدأ في يناير 2025.',
                ],
            ],
        ]);

        // Batch 2: Apr-Jun
        $batch2 = $this->createBatch($program1, [
            'code' => 'GDB-2025-Q2',
            'start_date' => Carbon::parse('2025-04-01'),
            'end_date' => Carbon::parse('2025-06-30'),
            'max_students' => 40,
            'is_active' => true,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'April - June 2025',
                    'description' => 'Second quarter batch starting in April 2025.',
                ],
                [
                    'locale' => 'ar',
                    'name' => 'أبريل - يونيو 2025',
                    'description' => 'دفعة الربع الثاني تبدأ في أبريل 2025.',
                ],
            ],
        ]);

        // Create groups for Batch 1
        $group1A = $this->createGroup($batch1, [
            'code' => 'GDB-Q1-A',
            'capacity' => 20,
            'room' => 'Room 101',
            'instructor_id' => $instructors[0]->id,
            'is_active' => true,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'Group A',
                    'description' => 'Morning session group',
                ],
                [
                    'locale' => 'ar',
                    'name' => 'المجموعة أ',
                    'description' => 'مجموعة الجلسة الصباحية',
                ],
            ],
        ]);

        $group1B = $this->createGroup($batch1, [
            'code' => 'GDB-Q1-B',
            'capacity' => 20,
            'room' => 'Room 102',
            'instructor_id' => $instructors[1]->id,
            'is_active' => true,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'Group B',
                    'description' => 'Evening session group',
                ],
                [
                    'locale' => 'ar',
                    'name' => 'المجموعة ب',
                    'description' => 'مجموعة الجلسة المسائية',
                ],
            ],
        ]);

        // Create groups for Batch 2
        $group2A = $this->createGroup($batch2, [
            'code' => 'GDB-Q2-A',
            'capacity' => 20,
            'room' => 'Room 101',
            'instructor_id' => $instructors[0]->id,
            'is_active' => true,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'Group A',
                    'description' => 'Morning session group',
                ],
                [
                    'locale' => 'ar',
                    'name' => 'المجموعة أ',
                    'description' => 'مجموعة الجلسة الصباحية',
                ],
            ],
        ]);

        $group2B = $this->createGroup($batch2, [
            'code' => 'GDB-Q2-B',
            'capacity' => 20,
            'room' => 'Room 102',
            'instructor_id' => $instructors[1]->id,
            'is_active' => true,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'Group B',
                    'description' => 'Evening session group',
                ],
                [
                    'locale' => 'ar',
                    'name' => 'المجموعة ب',
                    'description' => 'مجموعة الجلسة المسائية',
                ],
            ],
        ]);

        // Assign students to groups (10 per group)
        $this->assignStudentsToGroup($group1A, $students->take(10));
        $this->assignStudentsToGroup($group1B, $students->skip(10)->take(10));
        $this->assignStudentsToGroup($group2A, $students->take(10));
        $this->assignStudentsToGroup($group2B, $students->skip(10)->take(10));

        // Create sessions for groups (5 sessions each)
        $this->createSessionsForGroup($group1A, $batch1->start_date, 5);
        $this->createSessionsForGroup($group1B, $batch1->start_date, 5);
        $this->createSessionsForGroup($group2A, $batch2->start_date, 5);
        $this->createSessionsForGroup($group2B, $batch2->start_date, 5);

        // Create batch schedules
        $this->createBatchSchedule($batch1, 'mon', '09:00', '12:00', 'Room 101');
        $this->createBatchSchedule($batch1, 'wed', '09:00', '12:00', 'Room 101');
        $this->createBatchSchedule($batch2, 'tue', '18:00', '21:00', 'Room 102');
        $this->createBatchSchedule($batch2, 'thu', '18:00', '21:00', 'Room 102');

        // Program 2: 3D & Animation Track
        $program2 = $this->createProgram([
            'type' => 'track',
            'duration_weeks' => 16,
            'price' => 6000,
            'level' => 'intermediate',
            'is_active' => true,
            'sort_order' => 2,
            'translations' => [
                [
                    'locale' => 'en',
                    'title' => '3D & Animation Track',
                    'description' => 'An advanced 16-week track focused on 3D modeling, animation, and visual effects. Designed for students with prior design experience.',
                    'meta_title' => '3D & Animation Track - Advanced Training',
                    'meta_description' => 'Master 3D modeling and animation in our comprehensive 16-week track program.',
                ],
                [
                    'locale' => 'ar',
                    'title' => 'مسار الثلاثي الأبعاد والرسوم المتحركة',
                    'description' => 'مسار متقدم لمدة 16 أسبوعاً يركز على النمذجة ثلاثية الأبعاد والرسوم المتحركة والمؤثرات البصرية. مصمم للطلاب ذوي الخبرة السابقة في التصميم.',
                    'meta_title' => 'مسار الثلاثي الأبعاد والرسوم المتحركة - تدريب متقدم',
                    'meta_description' => 'أتقن النمذجة ثلاثية الأبعاد والرسوم المتحركة في برنامجنا الشامل لمدة 16 أسبوعاً.',
                ],
            ],
        ]);

        // Batch for Program 2
        $batch3 = $this->createBatch($program2, [
            'code' => '3DA-2025-Q1',
            'start_date' => Carbon::parse('2025-02-01'),
            'end_date' => Carbon::parse('2025-05-31'),
            'max_students' => 30,
            'is_active' => true,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'February - May 2025',
                    'description' => 'First batch for 3D & Animation Track.',
                ],
                [
                    'locale' => 'ar',
                    'name' => 'فبراير - مايو 2025',
                    'description' => 'الدفعة الأولى لمسار الثلاثي الأبعاد والرسوم المتحركة.',
                ],
            ],
        ]);

        // Groups for Program 2
        $group3A = $this->createGroup($batch3, [
            'code' => '3DA-Q1-A',
            'capacity' => 15,
            'room' => 'Room 201',
            'instructor_id' => $instructors[2]->id,
            'is_active' => true,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'Group A',
                    'description' => 'Advanced 3D modeling group',
                ],
                [
                    'locale' => 'ar',
                    'name' => 'المجموعة أ',
                    'description' => 'مجموعة النمذجة ثلاثية الأبعاد المتقدمة',
                ],
            ],
        ]);

        $group3B = $this->createGroup($batch3, [
            'code' => '3DA-Q1-B',
            'capacity' => 15,
            'room' => 'Room 202',
            'instructor_id' => $instructors[3]->id,
            'is_active' => true,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'Group B',
                    'description' => 'Animation and VFX group',
                ],
                [
                    'locale' => 'ar',
                    'name' => 'المجموعة ب',
                    'description' => 'مجموعة الرسوم المتحركة والمؤثرات البصرية',
                ],
            ],
        ]);

        // Assign students
        $this->assignStudentsToGroup($group3A, $students->take(15));
        $this->assignStudentsToGroup($group3B, $students->skip(5)->take(15));

        // Create sessions
        $this->createSessionsForGroup($group3A, $batch3->start_date, 5);
        $this->createSessionsForGroup($group3B, $batch3->start_date, 5);

        // Create schedules
        $this->createBatchSchedule($batch3, 'sat', '10:00', '14:00', 'Room 201');
        $this->createBatchSchedule($batch3, 'sun', '10:00', '14:00', 'Room 202');

        $this->command->info('Dynamic Learning Structure seeded successfully!');
        $this->command->info("Created: 2 Programs, 3 Batches, 6 Groups, 30 Sessions");
    }

    protected function createProgram(array $data): Program
    {
        $translations = $data['translations'];
        unset($data['translations']);

        $data['slug'] = Str::slug($translations[0]['title'] ?? 'program-' . time());
        $program = Program::create($data);
        $this->translationService->saveTranslations($program, $translations);

        return $program;
    }

    protected function createBatch(Program $program, array $data): Batch
    {
        $translations = $data['translations'];
        unset($data['translations']);

        $data['program_id'] = $program->id;
        $batch = Batch::create($data);
        $this->translationService->saveTranslations($batch, $translations);

        return $batch;
    }

    protected function createGroup(Batch $batch, array $data): Group
    {
        $translations = $data['translations'];
        unset($data['translations']);

        $data['batch_id'] = $batch->id;
        $group = Group::create($data);
        $this->translationService->saveTranslations($group, $translations);

        return $group;
    }

    protected function assignStudentsToGroup(Group $group, $students): void
    {
        $syncData = [];
        foreach ($students as $student) {
            $syncData[$student->id] = ['enrolled_at' => now()];
        }
        $group->students()->sync($syncData);
    }

    protected function createSessionsForGroup(Group $group, Carbon $startDate, int $count): void
    {
        // Get or create a course for this program
        $program = $group->batch->program;
        $category = Category::first();
        
        if (!$category) {
            $category = Category::factory()->create();
        }
        
        // Find or create a course for this program
        $course = Course::where('slug', Str::slug($program->getTranslated('title', 'en') ?? 'program-course'))->first();
        
        if (!$course) {
            $course = Course::create([
                'title' => $program->getTranslated('title', 'en') ?? 'Program Course',
                'slug' => Str::slug($program->getTranslated('title', 'en') ?? 'program-course'),
                'code' => 'PROG-' . $program->id,
                'category_id' => $category->id,
                'price' => $program->price ?? 0,
                'session_count' => $count,
                'is_published' => true,
            ]);
        }
        
        for ($i = 1; $i <= $count; $i++) {
            $sessionDate = $startDate->copy()->addWeeks($i - 1);
            
            $sessionData = [
                'course_id' => $course->id,
                'title' => "Session {$i}",
                'session_order' => $i,
                'session_date' => $sessionDate,
                'start_time' => '09:00',
                'end_time' => '12:00',
                'status' => 'scheduled',
            ];
            
            // Only add group_id if the column exists
            if (\Illuminate\Support\Facades\Schema::hasColumn('sessions', 'group_id')) {
                $sessionData['group_id'] = $group->id;
            }
            
            Session::create($sessionData);
        }
    }

    protected function createBatchSchedule(Batch $batch, string $dayOfWeek, string $startTime, string $endTime, ?string $room = null): void
    {
        BatchSchedule::create([
            'batch_id' => $batch->id,
            'day_of_week' => $dayOfWeek,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'room' => $room,
        ]);
    }
}

