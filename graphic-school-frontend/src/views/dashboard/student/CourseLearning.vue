<template>
  <div class="space-y-8">
    <!-- Course Header -->
    <div v-if="course" class="bg-gradient-to-r from-primary to-primary/80 rounded-2xl p-8 text-white">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <h1 class="text-3xl font-black mb-2">{{ course.title }}</h1>
          <p class="text-white/90 mb-4">{{ course.description }}</p>
          <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <span>{{ course.total_lessons || 0 }} درس</span>
            </div>
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ course.estimated_duration || 0 }} ساعة</span>
            </div>
          </div>
        </div>
        <div class="text-right">
          <div class="text-4xl font-black mb-2">{{ enrollment?.progress_percentage || 0 }}%</div>
          <p class="text-white/80">التقدم</p>
        </div>
      </div>
    </div>

    <div class="grid lg:grid-cols-4 gap-8">
      <!-- Sidebar: Modules List -->
      <div class="lg:col-span-1">
        <div class="card-premium p-6 sticky top-4">
          <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">المنهج الدراسي</h3>
          <div v-if="loading" class="text-center py-8">
            <div class="spinner mx-auto mb-2"></div>
          </div>
          <div v-else-if="modules.length === 0" class="text-center py-8 text-slate-500 dark:text-slate-400">
            لا توجد وحدات
          </div>
          <div v-else class="space-y-2">
            <div
              v-for="module in modules"
              :key="module.id"
              class="p-3 rounded-lg cursor-pointer transition-colors"
              :class="selectedModule?.id === module.id
                ? 'bg-primary text-white'
                : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600'"
              @click="selectModule(module)"
            >
              <div class="flex items-center justify-between">
                <span class="font-semibold">{{ module.title }}</span>
                <span class="text-xs">{{ module.lessons?.length || 0 }} درس</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content: Lessons -->
      <div class="lg:col-span-3">
        <div v-if="selectedModule">
          <div class="card-premium p-6 mb-6">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ selectedModule.title }}</h2>
            <p class="text-slate-600 dark:text-slate-400">{{ selectedModule.description }}</p>
          </div>

          <div class="space-y-4">
            <div
              v-for="lesson in selectedModule.lessons"
              :key="lesson.id"
              class="card-premium p-6 cursor-pointer hover-lift"
              @click="openLesson(lesson)"
            >
              <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                  <div
                    class="w-12 h-12 rounded-lg flex items-center justify-center"
                    :class="isLessonCompleted(lesson.id)
                      ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                      : lesson.is_preview
                      ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                      : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400'"
                  >
                    <svg v-if="isLessonCompleted(lesson.id)" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg v-else-if="lesson.lesson_type === 'video'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                      <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ lesson.title }}</h3>
                      <div class="flex items-center gap-2 mt-1">
                        <span v-if="lesson.is_preview" class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                          معاينة مجانية
                        </span>
                        <span v-if="lesson.lesson_type === 'quiz'" class="px-2 py-0.5 text-xs font-semibold rounded bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">
                          اختبار
                        </span>
                        <span v-if="lesson.lesson_type === 'project'" class="px-2 py-0.5 text-xs font-semibold rounded bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400">
                          مشروع
                        </span>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <span
                        v-if="lesson.is_preview"
                        class="px-2 py-1 text-xs rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400"
                      >
                        معاينة مجانية
                      </span>
                      <span
                        v-if="lesson.video_duration"
                        class="text-xs text-slate-500 dark:text-slate-400"
                      >
                        {{ lesson.video_duration }}
                      </span>
                    </div>
                  </div>
                  <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">{{ lesson.description }}</p>
                  <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
                    <div v-if="lesson.resources && lesson.resources.length > 0" class="flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                      </svg>
                      <span>{{ lesson.resources.length }} ملف مرفق</span>
                    </div>
                    <div v-if="lesson.video_duration" class="flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>{{ lesson.video_duration }}</span>
                    </div>
                  </div>
                </div>
                <div class="flex-shrink-0">
                  <button
                    v-if="lesson.lesson_type === 'quiz'"
                    @click.stop="openQuiz(lesson)"
                    class="btn-primary text-sm"
                  >
                    بدء الاختبار
                  </button>
                  <button
                    v-else
                    @click.stop="openLesson(lesson)"
                    class="btn-primary text-sm"
                  >
                    فتح الدرس
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-20">
          <p class="text-slate-500 dark:text-slate-400">اختر وحدة لعرض الدروس</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useApi } from '../../../composables/useApi';

const route = useRoute();
const router = useRouter();
const { get } = useApi();

const loading = ref(false);
const course = ref(null);
const enrollment = ref(null);
const curriculum = ref(null);
const selectedModule = ref(null);
const completedLessons = ref([]);

const modules = computed(() => {
  if (!curriculum.value) return [];
  return curriculum.value.modules || curriculum.value.data?.modules || [];
});

async function loadCourse() {
  try {
    loading.value = true;
    const courseId = route.params.id;
    const enrollmentId = route.query.enrollment;

    // Load course curriculum
    const curriculumResponse = await get(`/student/courses/${courseId}/curriculum`);
    const curriculumData = curriculumResponse?.data || curriculumResponse;
    curriculum.value = {
      course: curriculumData?.course || {},
      modules: curriculumData?.modules || curriculumData?.data?.modules || [],
    };
    course.value = curriculumData?.course || curriculumData;

    // Load enrollment
    if (enrollmentId) {
      try {
        const enrollmentResponse = await get(`/enrollments/${enrollmentId}`);
        enrollment.value = enrollmentResponse?.data || enrollmentResponse;
      } catch (enrollErr) {
        console.warn('Could not load enrollment:', enrollErr);
      }
    }

    // Load progress
    if (enrollmentId) {
      try {
        const progressResponse = await get(`/student/enrollments/${enrollmentId}/progress`);
        const progress = progressResponse?.data || progressResponse;
        completedLessons.value = (progress.progress_by_module || [])
          .flatMap(m => {
            // Handle both array of lesson objects and array of IDs
            if (m.lessons && Array.isArray(m.lessons)) {
              return m.lessons.map(l => typeof l === 'object' ? l.lesson_id : l);
            }
            return [];
          })
          .filter(Boolean);
      } catch (progressErr) {
        console.warn('Could not load progress:', progressErr);
        completedLessons.value = [];
      }
    }

    // Select first module
    if (modules.value.length > 0) {
      selectedModule.value = modules.value[0];
    }
  } catch (err) {
    console.error('Error loading course:', err);
  } finally {
    loading.value = false;
  }
}

function selectModule(module) {
  selectedModule.value = module;
}

function isLessonCompleted(lessonId) {
  return completedLessons.value.includes(parseInt(lessonId));
}

function openLesson(lesson) {
  router.push(`/dashboard/student/lessons/${lesson.id}?enrollment=${enrollment.value?.id}&course=${course.value?.id}`);
}

function openQuiz(lesson) {
  if (lesson.quiz?.id) {
    router.push(`/dashboard/student/quizzes/${lesson.quiz.id}/attempt`);
  } else {
    // Try to find quiz by lesson_id
    router.push(`/dashboard/student/quizzes?lesson=${lesson.id}`);
  }
}

onMounted(loadCourse);
</script>

