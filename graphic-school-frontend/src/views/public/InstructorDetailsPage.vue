<template>
  <div class="max-w-6xl mx-auto px-4 py-10" v-if="instructor">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
      <div class="flex flex-col md:flex-row gap-6 items-start md:items-center">
        <!-- Avatar -->
        <div class="flex-shrink-0">
          <div
            v-if="!instructor.avatar_path"
            class="h-32 w-32 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-4xl font-bold text-white"
          >
            {{ instructor.name?.charAt(0) }}
          </div>
          <img
            v-else
            :src="instructor.avatar_path"
            :alt="instructor.name"
            class="h-32 w-32 rounded-full object-cover border-4 border-primary/20"
          />
        </div>

        <!-- Info -->
        <div class="flex-1">
          <h1 class="text-4xl font-bold text-slate-900 mb-2">{{ instructor.name }}</h1>
          <p class="text-lg text-slate-600 mb-4">{{ instructor.email }}</p>
          <p v-if="instructor.phone" class="text-sm text-slate-500 mb-4">
            <span class="font-medium">الهاتف:</span> {{ instructor.phone }}
          </p>
          <p v-if="instructor.address" class="text-sm text-slate-500 mb-4">
            <span class="font-medium">العنوان:</span> {{ instructor.address }}
          </p>

          <!-- Stats -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <div class="text-2xl font-bold text-primary">{{ instructor.courses_count || 0 }}</div>
              <div class="text-xs text-slate-600 mt-1">الكورسات</div>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <div class="text-2xl font-bold text-primary">{{ instructor.students_count || 0 }}</div>
              <div class="text-xs text-slate-600 mt-1">الطلاب</div>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <div class="text-2xl font-bold text-primary">{{ instructor.average_rating || 0 }}</div>
              <div class="text-xs text-slate-600 mt-1">التقييم</div>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <div class="text-2xl font-bold text-primary">{{ instructor.reviews_count || 0 }}</div>
              <div class="text-xs text-slate-600 mt-1">التقييمات</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bio Section -->
    <div v-if="instructor.bio" class="bg-white rounded-xl shadow p-6 mb-8">
      <h2 class="text-2xl font-semibold text-slate-900 mb-4">نبذة عن المدرب</h2>
      <p class="text-slate-600 whitespace-pre-line leading-relaxed">{{ instructor.bio }}</p>
    </div>

    <!-- Courses Section -->
    <div v-if="instructor.instructor_courses && instructor.instructor_courses.length > 0" class="bg-white rounded-xl shadow p-6">
      <h2 class="text-2xl font-semibold text-slate-900 mb-6">الكورسات التي يدرسها</h2>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article
          v-for="course in instructor.instructor_courses"
          :key="course.id"
          class="border border-slate-200 rounded-lg p-4 hover:shadow-md transition-shadow"
        >
          <div class="mb-3">
            <span v-if="course.category" class="text-xs text-primary font-medium">
              {{ course.category.name }}
            </span>
          </div>
          <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ course.title }}</h3>
          <div class="flex items-center justify-between text-sm text-slate-600 mb-3">
            <span>عدد المحاضرات: {{ course.session_count }}</span>
            <span v-if="course.start_date" class="text-xs">{{ formatDate(course.start_date) }}</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="font-bold text-primary">{{ formatCurrency(course.price) }}</span>
            <RouterLink
              :to="`/courses/${course.id}`"
              class="text-sm text-primary hover:underline"
            >
              عرض التفاصيل
            </RouterLink>
          </div>
        </article>
      </div>
    </div>
    <div v-else class="bg-white rounded-xl shadow p-6 text-center text-slate-500">
      لا توجد كورسات متاحة حالياً
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

