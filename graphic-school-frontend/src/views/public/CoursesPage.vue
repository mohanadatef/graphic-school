<template>
  <div class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="text-3xl font-bold text-slate-900">{{ $t('courses.allCourses') }}</h2>
        <p class="text-slate-500 text-sm">{{ $t('courses.selectCategory') }}</p>
      </div>
      <select
        v-model="selectedCategory"
        class="border border-slate-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
        :aria-label="$t('courses.category')"
      >
        <option value="">{{ $t('courses.allCategories') }}</option>
        <option v-for="category in categoryStore.categories" :key="category.id" :value="category.id">
          {{ category.name }}
        </option>
      </select>
    </div>

    <div v-if="courseStore.loading" class="text-center py-12">
      <p class="text-slate-500">{{ $t('common.loading') }}</p>
    </div>

    <div v-else-if="filteredCourses.length === 0" class="text-center py-12">
      <p class="text-slate-500">{{ $t('common.noData') }}</p>
    </div>

    <div v-else class="grid md:grid-cols-3 gap-6">
      <article
        v-for="course in filteredCourses"
        :key="course.id"
        class="bg-white rounded-xl border border-slate-100 shadow-sm flex flex-col overflow-hidden hover:shadow-md transition-shadow"
      >
        <div class="h-40 bg-slate-100 flex items-center justify-center text-slate-400">
          {{ course.category?.name || $t('courses.noCategory') }}
        </div>
        <div class="p-4 flex-1 flex flex-col">
          <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ course.title }}</h3>
          <p class="text-sm text-slate-600 line-clamp-3 flex-1">{{ course.description }}</p>
          <div class="text-sm text-slate-500 mt-3">
            {{ $t('courses.instructorCount') }}: {{ course.instructors?.length || 0 }}
          </div>
          <div class="mt-4 flex items-center justify-between">
            <span class="font-semibold text-primary">{{ formatCurrency(course.price) }}</span>
            <RouterLink
              :to="`/courses/${course.id}`"
              class="text-sm text-primary hover:underline"
            >
              {{ $t('courses.details') }}
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
import { useI18n } from 'vue-i18n';

const courseStore = useCourseStore();
const categoryStore = useCategoryStore();
const { t, locale } = useI18n();

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
