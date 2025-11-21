<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" v-if="instructor">
    <!-- Header Section -->
    <div class="relative rounded-3xl overflow-hidden mb-10 shadow-2xl">
      <div class="absolute inset-0 bg-gradient-to-br from-primary via-primary/90 to-primary/80"></div>
      <div class="relative px-8 py-12 md:py-16">
        <div class="flex flex-col md:flex-row gap-8 items-start md:items-center">
          <!-- Avatar -->
          <div class="flex-shrink-0 relative">
            <div
              v-if="!instructor.avatar_path"
              class="h-32 w-32 md:h-40 md:w-40 rounded-2xl bg-white/20 backdrop-blur-sm border-4 border-white/30 flex items-center justify-center text-5xl font-black text-white shadow-2xl"
            >
              {{ instructor.name?.charAt(0) }}
            </div>
            <img
              v-else
              :src="instructor.avatar_path"
              :alt="instructor.name"
              class="h-32 w-32 md:h-40 md:w-40 rounded-2xl object-cover border-4 border-white/30 shadow-2xl"
            />
            <div class="absolute -bottom-2 -right-2 w-10 h-10 rounded-xl bg-emerald-500 border-4 border-white flex items-center justify-center shadow-lg">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>

          <!-- Info -->
          <div class="flex-1 text-white">
            <h1 class="text-4xl md:text-5xl font-black mb-3">{{ instructor.name }}</h1>
            <div class="flex items-center gap-2 mb-4">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <p class="text-lg text-white/90">{{ instructor.email }}</p>
            </div>
            <div class="flex flex-wrap gap-4 text-sm text-white/80 mb-6">
              <p v-if="instructor.phone" class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                {{ instructor.phone }}
              </p>
              <p v-if="instructor.address" class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ instructor.address }}
              </p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
              <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/20">
                <div class="text-3xl font-black text-white mb-1">{{ instructor.courses_count || 0 }}</div>
                <div class="text-xs text-white/80 font-medium">الكورسات</div>
              </div>
              <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/20">
                <div class="text-3xl font-black text-white mb-1">{{ instructor.students_count || 0 }}</div>
                <div class="text-xs text-white/80 font-medium">الطلاب</div>
              </div>
              <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/20">
                <div class="text-3xl font-black text-white mb-1">{{ instructor.average_rating || '0.0' }}</div>
                <div class="text-xs text-white/80 font-medium">التقييم</div>
              </div>
              <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center border border-white/20">
                <div class="text-3xl font-black text-white mb-1">{{ instructor.reviews_count || 0 }}</div>
                <div class="text-xs text-white/80 font-medium">التقييمات</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bio Section -->
    <div v-if="instructor.bio" class="bg-white rounded-2xl shadow-lg border-2 border-slate-200 p-8 mb-8">
      <div class="flex items-center gap-3 mb-6">
        <div class="p-3 rounded-xl bg-gradient-to-br from-primary/10 to-primary/5">
          <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-slate-900">نبذة عن المدرب</h2>
      </div>
      <p class="text-slate-700 whitespace-pre-line leading-relaxed text-lg">{{ instructor.bio }}</p>
    </div>

    <!-- Courses Section -->
    <div v-if="instructor.instructor_courses && instructor.instructor_courses.length > 0" class="bg-white rounded-2xl shadow-lg border-2 border-slate-200 p-8">
      <div class="flex items-center gap-3 mb-8">
        <div class="p-3 rounded-xl bg-gradient-to-br from-emerald-500/10 to-emerald-500/5">
          <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-slate-900">الكورسات التي يدرسها</h2>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article
          v-for="course in instructor.instructor_courses"
          :key="course.id"
          class="group border-2 border-slate-200 rounded-xl p-6 hover:shadow-xl hover:-translate-y-2 hover:border-primary/50 transition-all duration-300"
        >
          <div class="mb-4">
            <span v-if="course.category" class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-semibold">
              {{ course.category.name }}
            </span>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-primary transition-colors duration-200">{{ course.title }}</h3>
          <div class="flex items-center justify-between text-sm text-slate-600 mb-4 pb-4 border-b border-slate-200">
            <span class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ course.session_count }} جلسة
            </span>
            <span v-if="course.start_date" class="text-xs">{{ formatDate(course.start_date) }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-2xl font-black text-primary">{{ formatCurrency(course.price) }}</span>
            <RouterLink
              :to="`/courses/${course.id}`"
              class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90 hover:shadow-lg transition-all duration-200 text-sm"
            >
              التفاصيل
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </RouterLink>
          </div>
        </article>
      </div>
    </div>
    <div v-else class="bg-white rounded-2xl shadow-lg border-2 border-slate-200 p-12 text-center">
      <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
      </div>
      <p class="text-lg text-slate-500">لا توجد كورسات متاحة حالياً</p>
    </div>
  </div>

  <!-- Loading State -->
  <div v-else-if="loading" class="py-20 text-center text-slate-500">
    جاري تحميل بيانات المدرب...
  </div>

  <!-- Error State -->
  <div v-else-if="error" class="py-20 text-center">
    <p class="text-red-500 mb-4">{{ error }}</p>
    <RouterLink to="/instructors" class="text-primary underline">
      العودة إلى قائمة المدربين
    </RouterLink>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink, useRoute, useRouter } from 'vue-router';
import api from '../../api';

const route = useRoute();
const router = useRouter();
const instructor = ref(null);
const loading = ref(true);
const error = ref('');

function formatDate(date) {
  if (!date) return null;
  return new Date(date).toLocaleDateString('ar-EG', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
}

function formatCurrency(amount) {
  if (!amount) return '0 ج.م';
  return new Intl.NumberFormat('ar-EG', {
    style: 'currency',
    currency: 'EGP',
    minimumFractionDigits: 0,
  }).format(amount);
}

onMounted(async () => {
  try {
    loading.value = true;
    
    // Get instructor ID from route params
    const instructorId = route.params.id;
    
    // Validate instructor ID
    if (!instructorId || instructorId === 'undefined') {
      throw new Error('معرف المدرب غير صحيح');
    }
    
    console.log('Loading instructor with ID:', instructorId);
    
    const response = await api.get(`/instructors/${instructorId}`);
    
    // Handle unified API response format
    if (response.data) {
      if (response.data.data !== undefined) {
        instructor.value = response.data.data;
      } else if (typeof response.data === 'object') {
        instructor.value = response.data;
      }
    }
    
    if (!instructor.value) {
      throw new Error('Instructor data not found');
    }
  } catch (err) {
    console.error('Error loading instructor:', err);
    error.value = err.response?.data?.message || err.message || 'تعذر تحميل بيانات المدرب، حاول لاحقاً.';
    
    if (err.response?.status === 404 || !route.params.id || route.params.id === 'undefined') {
      setTimeout(() => {
        router.push('/instructors');
      }, 2000);
    }
  } finally {
    loading.value = false;
  }
});
</script>

