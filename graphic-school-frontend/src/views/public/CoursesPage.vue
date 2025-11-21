<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative">
    <!-- Background Decoration -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-secondary/5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    
    <div class="mb-12 relative z-10">
      <div class="flex flex-wrap items-center justify-between gap-6 mb-8 animate-fade-in-up">
        <div>
          <p class="text-sm font-bold text-primary mb-3 uppercase tracking-wider">{{ $t('courses.allCourses') }}</p>
          <h2 class="text-5xl md:text-6xl font-black text-slate-900 dark:text-white mb-3">
            <span class="block">اكتشف</span>
            <span class="gradient-text">كورساتنا</span>
          </h2>
          <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl">{{ $t('courses.selectCategory') || 'اختر من بين مجموعة متنوعة من الكورسات المتخصصة' }}</p>
        </div>
        <div class="relative">
          <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
          <select
            v-model="selectedCategory"
            class="appearance-none pl-5 pr-12 py-3.5 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-primary/20 focus:border-primary transition-all duration-300 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-lg hover:shadow-xl hover:scale-105 cursor-pointer min-w-[220px] animate-fade-in-up"
            style="animation-delay: 0.1s;"
            :aria-label="$t('courses.category')"
          >
            <option value="">{{ $t('courses.allCategories') }}</option>
            <option v-for="category in categoryStore.categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <div v-if="courseStore.loading" class="text-center py-12">
      <p class="text-slate-500">{{ $t('common.loading') }}</p>
    </div>

    <div v-else-if="filteredCourses.length === 0" class="text-center py-12">
      <p class="text-slate-500">{{ $t('common.noData') }}</p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 relative z-10">
      <article
        v-for="(course, index) in filteredCourses"
        :key="course.id"
        class="group card-premium p-0 overflow-hidden hover-lift animate-fade-in-up"
        :style="{ animationDelay: `${index * 0.1}s` }"
      >
        <div class="relative h-48 bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden">
          <div v-if="course.image_path" class="absolute inset-0">
            <img :src="course.image_path" :alt="course.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
          </div>
          <div v-else class="h-full flex items-center justify-center">
            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-2 rounded-xl bg-gradient-to-br from-primary/20 to-primary/10 flex items-center justify-center">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <p class="text-xs font-medium text-slate-500">{{ course.category?.name || $t('courses.noCategory') }}</p>
            </div>
          </div>
          <div class="absolute top-4 right-4">
            <span class="px-3 py-1 rounded-full bg-white/90 backdrop-blur-sm text-xs font-semibold text-primary shadow-sm">
              {{ course.category?.name || 'عام' }}
            </span>
          </div>
        </div>
        <div class="p-6 flex-1 flex flex-col">
          <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-primary transition-colors duration-200">
            {{ course.title }}
          </h3>
          <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-3 flex-1 leading-relaxed mb-4">
            {{ course.description || 'كورس شامل لتعلم أساسيات التصميم الجرافيكي' }}
          </p>
          <div class="flex items-center gap-4 text-xs text-slate-500 mb-4">
            <span class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              {{ course.instructors?.length || 0 }} مدرب
            </span>
            <span class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ course.session_count || 0 }} جلسة
            </span>
          </div>
          <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-2xl font-bold text-primary">{{ formatCurrency(course.price) }}</span>
            <RouterLink
              :to="`/courses/${course.id}`"
              class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 hover:shadow-lg transition-all duration-200 group"
            >
              {{ $t('courses.details') || 'التفاصيل' }}
              <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </RouterLink>
          </div>
        </div>
      </article>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import { useCourseStore } from '../../stores/course';
import { useCategoryStore } from '../../stores/category';
import { useI18n } from '../../composables/useI18n';
import { useLocale } from '../../composables/useLocale';

const courseStore = useCourseStore();
const categoryStore = useCategoryStore();
const { t } = useI18n();
const { locale } = useLocale();

const selectedCategory = ref('');

const filteredCourses = computed(() => {
  if (!selectedCategory.value) return courseStore.courses;
  return courseStore.courses.filter(
    (course) => course.category_id === Number(selectedCategory.value)
  );
});

function formatCurrency(value) {
  return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-US', {
    style: 'currency',
    currency: 'EGP',
  }).format(value);
}

async function loadData() {
  try {
    await Promise.all([
      courseStore.fetchAll({
        category_id: selectedCategory.value || undefined,
      }),
      categoryStore.fetchAll(),
    ]);
  } catch (error) {
    // Error handled in store
  }
}

watch(selectedCategory, () => {
  loadData();
});

onMounted(loadData);
</script>
