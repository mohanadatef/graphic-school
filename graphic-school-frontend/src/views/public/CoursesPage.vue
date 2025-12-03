<template>
  <div class="space-y-8">
    <!-- CMS Content -->
    <CMSPageRenderer slug="courses" />

    <!-- Courses List -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div v-if="loadingCourses" class="text-center py-20">
        <div class="spinner-lg mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
      </div>

      <div v-else-if="courses.length === 0" class="text-center py-20">
        <p class="text-slate-500 dark:text-slate-400 text-lg">
          {{ $t('courses.noCourses') || 'No courses available' }}
        </p>
      </div>

      <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="course in courses"
          :key="course.id"
          class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-shadow"
        >
          <div v-if="course.image_path" class="h-48 bg-slate-200 dark:bg-slate-700 overflow-hidden">
            <img :src="course.image_path" :alt="course.title" class="w-full h-full object-cover" />
          </div>
          <div class="p-6">
            <div v-if="course.category" class="text-xs font-medium text-primary mb-2">
              {{ course.category.name }}
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">
              {{ course.title }}
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 mb-4">
              {{ course.description }}
            </p>
            <div class="flex items-center justify-between">
              <span v-if="course.price" class="text-lg font-bold text-slate-900 dark:text-white">
                {{ formatPrice(course.price) }}
              </span>
              <RouterLink
                :to="`/courses/${course.id}`"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm"
              >
                {{ $t('courses.viewDetails') || 'View Details' }}
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { useApi } from '../../composables/useApi';
import { useI18n } from '../../composables/useI18n';
import { useCurrencyStore } from '../../stores/currency';
import CMSPageRenderer from '../../components/public/CMSPageRenderer.vue';

const { get } = useApi();
const { t, locale } = useI18n();
const currencyStore = useCurrencyStore();

const courses = ref([]);
const loadingCourses = ref(false);

onMounted(async () => {
  await currencyStore.init();
  await loadCourses();
});

async function loadCourses() {
  try {
    loadingCourses.value = true;
    const response = await get('/public/courses', {
      params: { locale: locale.value },
    });
    courses.value = response.data || response || [];
  } catch (err) {
    console.error('Error loading courses:', err);
    courses.value = [];
  } finally {
    loadingCourses.value = false;
  }
}

function formatPrice(price) {
  return currencyStore.formatAmount(price);
}
</script>
