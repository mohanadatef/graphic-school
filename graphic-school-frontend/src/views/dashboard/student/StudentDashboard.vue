<template>
  <div class="space-y-8">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-primary to-primary/80 rounded-2xl p-8 text-white">
      <h1 class="text-3xl font-black mb-2">مرحباً {{ user?.name }}</h1>
      <p class="text-white/90">استمر في التعلم وطور مهاراتك</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid md:grid-cols-4 gap-6">
      <div class="card-premium p-6">
        <div class="flex items-center justify-between mb-3">
          <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">الكورسات المسجلة</p>
        <p class="text-3xl font-black text-slate-900 dark:text-white">{{ stats.enrolled_courses || 0 }}</p>
      </div>

      <div class="card-premium p-6">
        <div class="flex items-center justify-between mb-3">
          <div class="p-3 rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">الكورسات المكتملة</p>
        <p class="text-3xl font-black text-slate-900 dark:text-white">{{ stats.completed_courses || 0 }}</p>
      </div>

      <div class="card-premium p-6">
        <div class="flex items-center justify-between mb-3">
          <div class="p-3 rounded-xl bg-purple-100 dark:bg-purple-900/30">
            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">ساعات التعلم</p>
        <p class="text-3xl font-black text-slate-900 dark:text-white">{{ formatTime(stats.total_time_spent || 0) }}</p>
      </div>

      <div class="card-premium p-6">
        <div class="flex items-center justify-between mb-3">
          <div class="p-3 rounded-xl bg-yellow-100 dark:bg-yellow-900/30">
            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
          </div>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">الشهادات</p>
        <p class="text-3xl font-black text-slate-900 dark:text-white">{{ stats.certificates_count || 0 }}</p>
      </div>
    </div>

    <!-- My Courses -->
    <div>
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">كورساتي</h2>
        <RouterLink to="/dashboard/student/courses" class="text-primary hover:underline">
          عرض الكل
        </RouterLink>
      </div>

      <div v-if="loading" class="text-center py-12">
        <div class="spinner-lg mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">جاري التحميل...</p>
      </div>

      <div v-else-if="courses.length === 0" class="text-center py-12">
        <p class="text-slate-500 dark:text-slate-400">لا توجد كورسات مسجلة</p>
      </div>

      <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="course in courses"
          :key="course.id"
          class="card-premium p-6 hover-lift cursor-pointer"
          @click="$router.push(`/dashboard/student/courses/${course.id}?enrollment=${course.enrollment_id}`)"
        >
          <div class="mb-4">
            <img
              v-if="course.image_path"
              :src="course.image_path"
              :alt="course.title"
              class="w-full h-40 object-cover rounded-lg"
            />
            <div v-else class="w-full h-40 bg-slate-200 dark:bg-slate-700 rounded-lg flex items-center justify-center">
              <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
          </div>
          <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ course.title }}</h3>
          <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm text-slate-600 dark:text-slate-400">التقدم</span>
              <span class="text-sm font-semibold text-primary">{{ course.progress_percentage || 0 }}%</span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
              <div
                class="bg-primary h-2 rounded-full transition-all duration-300"
                :style="{ width: `${course.progress_percentage || 0}%` }"
              ></div>
            </div>
          </div>
          <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
            <span>{{ course.lessons_completed || 0 }} / {{ course.total_lessons || 0 }} درس</span>
            <span v-if="course.progress_percentage >= 100" class="text-emerald-600 font-semibold">مكتمل</span>
            <span v-else class="text-primary font-semibold">مستمر</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">النشاط الأخير</h2>
      <div class="card-premium p-6">
        <div v-if="recentActivity.length === 0" class="text-center py-8 text-slate-500 dark:text-slate-400">
          لا يوجد نشاط حديث
        </div>
        <div v-else class="space-y-4">
          <div
            v-for="activity in recentActivity"
            :key="activity.id"
            class="flex items-start gap-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg"
          >
            <div class="p-2 rounded-lg bg-primary/10">
              <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-semibold text-slate-900 dark:text-white">{{ activity.title }}</p>
              <p class="text-sm text-slate-600 dark:text-slate-400">{{ activity.description }}</p>
              <p class="text-xs text-slate-500 dark:text-slate-500 mt-1">{{ formatDate(activity.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '../../../composables/useApi';
import { useAuthStore } from '../../../stores/auth';

const { get } = useApi();
const authStore = useAuthStore();
const router = useRouter();

const user = ref(authStore.user);
const loading = ref(false);
const stats = ref({
  enrolled_courses: 0,
  completed_courses: 0,
  total_time_spent: 0,
  certificates_count: 0,
});
const courses = ref([]);
const recentActivity = ref([]);

async function loadDashboard() {
  try {
    loading.value = true;
    
    // Load enrollments with progress
    const enrollmentsResponse = await get('/enrollments?status=approved');
    const enrollmentsData = enrollmentsResponse?.data || enrollmentsResponse || [];
    
    stats.value.enrolled_courses = enrollmentsData.length;
    stats.value.completed_courses = enrollmentsData.filter(e => e.progress_percentage >= 100).length;
    stats.value.total_time_spent = enrollmentsData.reduce((sum, e) => sum + (e.time_spent || 0), 0);
    
    // Load courses with progress
    courses.value = enrollmentsData.slice(0, 6).map(enrollment => ({
      ...enrollment.course,
      progress_percentage: enrollment.progress_percentage || 0,
      lessons_completed: enrollment.lessons_completed || 0,
      total_lessons: enrollment.total_lessons || 0,
      enrollment_id: enrollment.id,
    }));
    
    // Load certificates
    try {
      const certificatesResponse = await get('/student/certificates');
      const certificatesData = certificatesResponse?.data || certificatesResponse || [];
      stats.value.certificates_count = Array.isArray(certificatesData) ? certificatesData.length : 0;
    } catch (certErr) {
      console.warn('Could not load certificates:', certErr);
      stats.value.certificates_count = 0;
    }
    
    // Load recent activity (simplified)
    recentActivity.value = enrollmentsData
      .filter(e => e.last_accessed_at)
      .sort((a, b) => new Date(b.last_accessed_at) - new Date(a.last_accessed_at))
      .slice(0, 5)
      .map(enrollment => ({
        id: enrollment.id,
        title: `استمر في كورس: ${enrollment.course?.title || 'غير معروف'}`,
        description: `التقدم: ${enrollment.progress_percentage || 0}%`,
        created_at: enrollment.last_accessed_at,
      }));
  } catch (err) {
    console.error('Error loading dashboard:', err);
  } finally {
    loading.value = false;
  }
}

function formatTime(seconds) {
  const hours = Math.floor(seconds / 3600);
  const minutes = Math.floor((seconds % 3600) / 60);
  
  if (hours > 0) {
    return `${hours} ساعة`;
  }
  return `${minutes} دقيقة`;
}

function formatDate(dateString) {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

onMounted(loadDashboard);
</script>

