<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-black text-slate-900 dark:text-white">كورساتي</h1>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">جاري التحميل...</p>
    </div>

    <div v-else-if="courses.length === 0" class="text-center py-20">
      <svg class="w-24 h-24 mx-auto text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>
      <p class="text-slate-500 dark:text-slate-400 text-lg">لا توجد كورسات مسجلة</p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="course in courses"
        :key="course.id"
        class="card-premium p-6 hover-lift cursor-pointer"
        @click="openCourse(course)"
      >
        <div class="mb-4">
          <img
            v-if="course.image_path"
            :src="course.image_path"
            :alt="course.title"
            class="w-full h-48 object-cover rounded-lg"
          />
          <div v-else class="w-full h-48 bg-slate-200 dark:bg-slate-700 rounded-lg flex items-center justify-center">
            <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
        </div>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ course.title }}</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 line-clamp-2">{{ course.description }}</p>
        
        <div class="mb-4">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-slate-600 dark:text-slate-400">التقدم</span>
            <span class="text-sm font-semibold text-primary">{{ course.progress_percentage || 0 }}%</span>
          </div>
          <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3">
            <div
              class="bg-primary h-3 rounded-full transition-all duration-300"
              :style="{ width: `${course.progress_percentage || 0}%` }"
            ></div>
          </div>
        </div>

        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-4 text-slate-600 dark:text-slate-400">
            <span>{{ course.lessons_completed || 0 }} / {{ course.total_lessons || 0 }} درس</span>
          </div>
          <div>
            <span
              v-if="course.progress_percentage >= 100"
              class="px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-semibold text-xs"
            >
              مكتمل
            </span>
            <span
              v-else
              class="px-3 py-1 rounded-full bg-primary/10 text-primary font-semibold text-xs"
            >
              مستمر
            </span>
          </div>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
          <button
            @click.stop="openCourse(course)"
            class="w-full btn-primary"
          >
            {{ course.progress_percentage >= 100 ? 'مراجعة الكورس' : 'متابعة التعلم' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useApi } from '../../../composables/useApi';

const router = useRouter();
const { get } = useApi();

const loading = ref(false);
const courses = ref([]);

async function loadCourses() {
  try {
    loading.value = true;
    const enrollmentsResponse = await get('/enrollments?status=approved');
    const enrollmentsData = enrollmentsResponse?.data || enrollmentsResponse || [];
    
    courses.value = enrollmentsData.map(enrollment => ({
      ...enrollment.course,
      progress_percentage: enrollment.progress_percentage || 0,
      lessons_completed: enrollment.lessons_completed || 0,
      total_lessons: enrollment.total_lessons || 0,
      enrollment_id: enrollment.id,
    }));
  } catch (err) {
    console.error('Error loading courses:', err);
    courses.value = [];
  } finally {
    loading.value = false;
  }
}

function openCourse(course) {
  router.push(`/dashboard/student/courses/${course.id}?enrollment=${course.enrollment_id}`);
}

onMounted(loadCourses);
</script>

