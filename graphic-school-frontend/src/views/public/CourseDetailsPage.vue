<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12" v-if="course">
    <!-- Hero Section -->
    <div class="relative rounded-3xl overflow-hidden mb-10 shadow-2xl">
      <div v-if="course.image_path" class="absolute inset-0">
        <img :src="course.image_path" :alt="course.title" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-slate-900/60 to-slate-900/90"></div>
      </div>
      <div v-else class="absolute inset-0 bg-gradient-to-br from-primary via-primary/80 to-primary/60"></div>
      <div class="relative px-8 py-16 text-white">
        <div class="max-w-3xl">
          <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-sm text-sm font-medium mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            {{ course.category?.name }}
          </span>
          <h1 class="text-4xl md:text-5xl font-black mb-6 leading-tight">{{ course.title }}</h1>
          <p class="text-lg text-white/90 leading-relaxed mb-8">{{ course.description }}</p>
          <div class="flex flex-wrap items-center gap-6">
            <div class="flex items-center gap-2">
              <div class="p-2 rounded-lg bg-white/20 backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-white/70">عدد الجلسات</p>
                <p class="font-bold">{{ course.session_count || 0 }}</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <div class="p-2 rounded-lg bg-white/20 backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-white/70">تاريخ البدء</p>
                <p class="font-bold">{{ formatDate(course.start_date) || 'يحدد لاحقاً' }}</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <div class="p-2 rounded-lg bg-white/20 backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-white/70">السعر</p>
                <p class="text-2xl font-black">{{ course.price || 0 }} ج.م</p>
              </div>
            </div>
          </div>
          <div class="mt-6 flex flex-wrap gap-2">
            <span
              v-for="day in course.days_of_week || []"
              :key="day"
              class="px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm text-sm font-medium"
            >
              {{ daysMap[day] }}
            </span>
          </div>
        </div>
      </div>
    </div>

      <section class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 p-6 hover:shadow-xl transition-shadow duration-300">
          <div class="flex items-center gap-3 mb-6">
            <div class="p-3 rounded-xl bg-gradient-to-br from-primary/10 to-primary/5">
              <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">المدربون</h2>
          </div>
          <ul class="space-y-4">
            <li v-for="instructor in course.instructors" :key="instructor.id" class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors duration-200">
              <div class="h-14 w-14 rounded-full bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center text-white text-lg font-bold shadow-md">
                {{ instructor.name?.charAt(0) }}
              </div>
              <div class="flex-1">
                <RouterLink
                  :to="`/instructors/${instructor.id}`"
                  class="font-semibold text-slate-900 hover:text-primary transition-colors duration-200 block"
                >
                  {{ instructor.name }}
                </RouterLink>
                <p class="text-sm text-slate-500">{{ instructor.email }}</p>
              </div>
            </li>
          </ul>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 p-6 hover:shadow-xl transition-shadow duration-300">
          <div class="flex items-center gap-3 mb-6">
            <div class="p-3 rounded-xl bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 dark:from-emerald-500/20 dark:to-emerald-500/10">
              <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">جدول الجلسات</h2>
          </div>
          <div class="max-h-80 overflow-y-auto space-y-3 pr-2">
            <div
              v-for="(session, index) in course.sessions"
              :key="session.id"
              class="flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-primary/50 hover:bg-primary/5 transition-all duration-200"
            >
              <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-primary/10 text-primary font-bold flex items-center justify-center">
                {{ index + 1 }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-900 truncate">{{ session.title }}</p>
                <p class="text-sm text-slate-500">{{ formatDate(session.session_date) || 'يحدد لاحقاً' }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="bg-gradient-to-br from-primary/5 via-white dark:via-slate-800 to-primary/5 dark:from-primary/10 dark:to-primary/10 rounded-2xl shadow-lg border-2 border-primary/20 dark:border-primary/30 p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="p-3 rounded-xl bg-primary text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-slate-900 dark:text-white">الاشتراك في الكورس</h2>
        </div>
        
        <div v-if="enrollmentStatus" class="mb-6 p-4 rounded-xl" :class="enrollmentStatus === 'approved' ? 'bg-emerald-50 border-2 border-emerald-200' : 'bg-orange-50 border-2 border-orange-200'">
          <div class="flex items-center gap-2">
            <svg v-if="enrollmentStatus === 'approved'" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="font-semibold" :class="enrollmentStatus === 'approved' ? 'text-emerald-700' : 'text-orange-700'">
              حالتك الحالية: {{ enrollmentStatus === 'approved' ? 'مشترك' : enrollmentStatus === 'pending' ? 'بانتظار الموافقة' : enrollmentStatus }}
            </p>
          </div>
        </div>
        
        <div v-if="enrollMessage" class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200">
          <p class="text-sm font-medium text-emerald-700">{{ enrollMessage }}</p>
        </div>
        <div v-if="enrollError" class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200">
          <p class="text-sm font-medium text-red-700">{{ enrollError }}</p>
        </div>

        <div v-if="auth.isStudent.value">
          <button
            class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-primary to-primary/90 text-white rounded-xl font-bold text-lg hover:shadow-2xl hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 transition-all duration-300"
            :disabled="enrolling || !canEnroll"
            @click="enroll"
          >
            <span v-if="enrolling" class="spinner"></span>
            <svg v-else-if="!enrollmentStatus" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>{{ enrolling ? 'جاري الإرسال...' : enrollmentStatus ? 'تم تقديم الطلب' : 'اشترك الآن' }}</span>
          </button>
          <p v-if="!canEnroll && !enrollmentStatus" class="text-sm text-slate-500 mt-4 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            غير متاح حالياً للاشتراك.
          </p>
        </div>
        <div v-else class="p-6 rounded-xl bg-slate-50 border border-slate-200">
          <p class="text-slate-700 mb-4">
            سجّل دخولك كطالب لتتمكن من الاشتراك في الكورسات.
          </p>
          <RouterLink
            to="/login"
            class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 hover:shadow-lg transition-all duration-200"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            تسجيل الدخول
          </RouterLink>
        </div>
      </section>
    </div>
  <div v-else-if="loading" class="py-20 text-center text-slate-500">جاري تحميل بيانات الكورس ...</div>
  <div v-else-if="enrollError" class="py-20 text-center">
    <p class="text-red-500 mb-4">{{ enrollError }}</p>
    <RouterLink to="/courses" class="text-primary underline">العودة إلى قائمة الكورسات</RouterLink>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { RouterLink, useRoute, useRouter } from 'vue-router';
import api from '../../api';
import { useAuth } from '../../composables/useAuth';

const route = useRoute();
const router = useRouter();
const course = ref(null);
const auth = useAuth();
const enrolling = ref(false);
const enrollMessage = ref('');
const enrollError = ref('');
const loading = ref(true);

const daysMap = {
  mon: 'الاثنين',
  tue: 'الثلاثاء',
  wed: 'الأربعاء',
  thu: 'الخميس',
  fri: 'الجمعة',
  sat: 'السبت',
  sun: 'الأحد',
};

function formatDate(date) {
  if (!date) return null;
  return new Date(date).toLocaleDateString('ar-EG', { weekday: 'long', month: 'long', day: 'numeric' });
}

const enrollmentStatus = computed(() => course.value?.enrollment_status || null);
const canEnroll = computed(() => auth.isStudent.value && !['approved', 'pending'].includes(enrollmentStatus.value ?? ''));

async function enroll() {
  if (!course.value || !canEnroll.value) return;
  enrolling.value = true;
  enrollMessage.value = '';
  enrollError.value = '';
  try {
    await api.post(`/student/courses/${course.value.id}/enroll`);
    course.value.enrollment_status = 'pending';
    enrollMessage.value = 'تم إرسال طلب الاشتراك، سيتم إشعارك بعد الموافقة.';
  } catch (error) {
    enrollError.value = error.response?.data?.message || 'تعذر إرسال الطلب، حاول لاحقاً.';
  } finally {
    enrolling.value = false;
  }
}

onMounted(async () => {
  try {
    loading.value = true;
    const response = await api.get(`/courses/${route.params.id}`);
    
    // Handle unified API response format: { success, message, data, ... }
    if (response.data) {
      // Check if it's unified format
      if (response.data.data !== undefined) {
        course.value = response.data.data;
      } else if (Array.isArray(response.data) || typeof response.data === 'object') {
        course.value = response.data;
      }
    }
    
    if (!course.value) {
      throw new Error('Course data not found');
    }
  } catch (error) {
    console.error('Error loading course:', error);
    enrollError.value = error.response?.data?.message || error.message || 'تعذر تحميل بيانات الكورس، حاول لاحقاً.';
    
    // Redirect to courses page if course not found
    if (error.response?.status === 404) {
      setTimeout(() => {
        router.push('/courses');
      }, 2000);
    }
  } finally {
    loading.value = false;
  }
});
</script>

