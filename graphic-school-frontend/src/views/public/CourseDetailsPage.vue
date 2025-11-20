<template>
  <div class="max-w-5xl mx-auto px-4 py-10" v-if="course">
    <div class="bg-white rounded-2xl shadow p-6 mb-8 space-y-6">
        <p class="text-sm text-primary">{{ course.category?.name }}</p>
        <h1 class="text-3xl font-bold text-slate-900 mt-2 mb-4">{{ course.title }}</h1>
        <p class="text-slate-600 whitespace-pre-line mb-4">{{ course.description }}</p>
        <div class="grid md:grid-cols-3 gap-4 text-sm text-slate-600">
          <div>عدد المحاضرات: {{ course.session_count }}</div>
          <div>يبدأ في: {{ formatDate(course.start_date) || 'يحدد لاحقاً' }}</div>
          <div>السعر: {{ course.price }} ج.م</div>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
          <span
            v-for="day in course.days_of_week || []"
            :key="day"
            class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs"
          >
            {{ daysMap[day] }}
          </span>
        </div>
      </div>

      <section class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow p-5">
          <h2 class="text-xl font-semibold text-slate-900 mb-4">المدربون</h2>
          <ul class="space-y-3">
            <li v-for="instructor in course.instructors" :key="instructor.id" class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-sm font-bold">
                {{ instructor.name?.charAt(0) }}
              </div>
              <div>
                <p class="font-medium text-slate-800">{{ instructor.name }}</p>
                <p class="text-xs text-slate-500">{{ instructor.email }}</p>
              </div>
            </li>
          </ul>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
          <h2 class="text-xl font-semibold text-slate-900 mb-4">الجدول</h2>
          <ol class="space-y-3 text-sm text-slate-600 max-h-64 overflow-y-auto">
            <li v-for="session in course.sessions" :key="session.id" class="flex items-center justify-between">
              <span>{{ session.title }}</span>
              <span>{{ formatDate(session.session_date) }}</span>
            </li>
          </ol>
        </div>
      </section>

      <section class="bg-white rounded-xl shadow p-5 mt-8">
        <h2 class="text-xl font-semibold text-slate-900 mb-3">الاشتراك في الكورس</h2>
        <p v-if="enrollmentStatus" class="text-sm mb-4">
          حالتك الحالية: 
          <span class="font-semibold text-primary">
            {{ enrollmentStatus === 'approved' ? 'مشترك' : enrollmentStatus === 'pending' ? 'بانتظار الموافقة' : enrollmentStatus }}
          </span>
        </p>
        <p v-if="enrollMessage" class="text-sm text-emerald-600 mb-4">{{ enrollMessage }}</p>
        <p v-if="enrollError" class="text-sm text-red-500 mb-4">{{ enrollError }}</p>

        <div v-if="auth.isStudent.value">
          <button
            class="px-5 py-3 bg-primary text-white rounded-xl font-medium disabled:opacity-60"
            :disabled="enrolling || !canEnroll"
            @click="enroll"
          >
            {{ enrolling ? 'جاري الإرسال...' : enrollmentStatus ? 'تم تقديم الطلب' : 'اشترك الآن' }}
          </button>
          <p v-if="!canEnroll && !enrollmentStatus" class="text-sm text-slate-500 mt-2">
            غير متاح حالياً للاشتراك.
          </p>
        </div>
        <div v-else>
          <p class="text-sm text-slate-600">
            <RouterLink to="/login" class="text-primary underline">سجّل دخولك</RouterLink>
            كطالب لتتمكن من الاشتراك في الكورسات.
          </p>
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

