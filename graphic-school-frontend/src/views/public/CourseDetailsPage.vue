<template>
  <div class="space-y-8">
    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="!course" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">
        {{ $t('courses.notFound') || 'Course not found' }}
      </p>
    </div>

    <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Course Header -->
      <div class="mb-8">
        <div v-if="course.image_path" class="h-96 bg-slate-200 dark:bg-slate-700 rounded-2xl overflow-hidden mb-6">
          <img :src="course.image_path" :alt="course.title" class="w-full h-full object-cover" />
        </div>
        <div class="flex items-start justify-between flex-wrap gap-4">
          <div class="flex-1">
            <div v-if="course.category" class="text-sm font-medium text-primary mb-2">
              {{ course.category.name }}
            </div>
            <h1 class="text-4xl font-bold text-slate-900 dark:text-white mb-4">
              {{ course.title }}
            </h1>
            <p v-if="course.description" class="text-lg text-slate-600 dark:text-slate-400 mb-6">
              {{ course.description }}
            </p>
          </div>
          <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 min-w-[250px]">
            <div v-if="course.price" class="text-3xl font-bold text-slate-900 dark:text-white mb-4">
              {{ formatPrice(course.price) }}
            </div>
            <button
              @click="enroll"
              class="w-full px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-semibold"
            >
              {{ $t('courses.enroll') || 'Enroll Now' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Course Details -->
      <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
          <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">
            {{ $t('courses.startDate') || 'Start Date' }}
          </h3>
          <p class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ formatDate(course.start_date) }}
          </p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
          <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">
            {{ $t('courses.duration') || 'Duration' }}
          </h3>
          <p class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ course.duration_weeks || course.session_count }} {{ $t('courses.weeks') || 'weeks' }}
          </p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
          <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">
            {{ $t('courses.sessions') || 'Sessions' }}
          </h3>
          <p class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ course.session_count || 0 }}
          </p>
        </div>
      </div>

      <!-- Instructors -->
      <div v-if="course.instructors && course.instructors.length" class="mb-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">
          {{ $t('courses.instructors') || 'Instructors' }}
        </h2>
        <div class="grid md:grid-cols-3 gap-6">
          <div
            v-for="instructor in course.instructors"
            :key="instructor.id"
            class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 text-center"
          >
            <div v-if="instructor.avatar_path" class="w-20 h-20 rounded-full overflow-hidden mx-auto mb-4">
              <img :src="instructor.avatar_path" :alt="instructor.name" class="w-full h-full object-cover" />
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">
              {{ instructor.name }}
            </h3>
            <p v-if="instructor.bio" class="text-sm text-slate-600 dark:text-slate-400">
              {{ instructor.bio }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useApi } from '../../composables/useApi';
import { useI18n } from '../../composables/useI18n';
import { useCurrencyStore } from '../../stores/currency';

const route = useRoute();
const router = useRouter();
const { get } = useApi();
const { t, locale } = useI18n();
const currencyStore = useCurrencyStore();

const course = ref(null);
const loading = ref(false);

onMounted(async () => {
  await currencyStore.init();
  await loadCourse();
});

async function loadCourse() {
  try {
    loading.value = true;
    const response = await get(`/public/courses/${route.params.id}`, {
      params: { locale: locale.value },
    });
    course.value = response.data || response;
  } catch (err) {
    console.error('Error loading course:', err);
    course.value = null;
  } finally {
    loading.value = false;
  }
}

function formatPrice(price) {
  return currencyStore.formatAmount(price);
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString(locale.value, {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

function enroll() {
  router.push(`/enroll?course_id=${course.value.id}`);
}
</script>
