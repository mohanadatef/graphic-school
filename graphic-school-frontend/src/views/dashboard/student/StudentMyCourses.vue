<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
        {{ $t('student.myCourses.title') || 'My Courses' }}
      </h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        {{ $t('student.myCourses.subtitle') || 'View all your enrolled courses' }}
      </p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="courses.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">
        {{ $t('student.myCourses.noCourses') || 'No courses enrolled yet' }}
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
          <div class="flex items-start justify-between mb-2">
            <span v-if="course.category" class="text-xs font-medium text-primary">
              {{ course.category.name }}
            </span>
            <span
              :class="{
                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': course.enrollment.status === 'approved',
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': course.enrollment.status === 'pending',
              }"
              class="px-2 py-1 text-xs font-semibold rounded-full"
            >
              {{ course.enrollment.status }}
            </span>
          </div>
          <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">
            {{ course.title }}
          </h3>
          <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 mb-4">
            {{ course.description }}
          </p>
          <div v-if="course.instructors && course.instructors.length" class="mb-4">
            <p class="text-xs text-slate-500 dark:text-slate-500 mb-1">
              {{ $t('student.myCourses.instructors') || 'Instructors' }}:
            </p>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="instructor in course.instructors"
                :key="instructor.id"
                class="text-xs px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded"
              >
                {{ instructor.name }}
              </span>
            </div>
          </div>
          <div v-if="course.enrollment.group" class="mb-4">
            <p class="text-xs text-slate-500 dark:text-slate-500">
              {{ $t('student.myCourses.group') || 'Group' }}: {{ course.enrollment.group.name || course.enrollment.group.code }}
            </p>
          </div>
          <RouterLink
            :to="`/dashboard/student/courses/${course.id}`"
            class="block w-full text-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors"
          >
            {{ $t('student.myCourses.viewCourse') || 'View Course' }}
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useI18n } from '../../../composables/useI18n';

const { get } = useApi();
const { t } = useI18n();

const courses = ref([]);
const loading = ref(false);

async function loadCourses() {
  try {
    loading.value = true;
    const response = await get('/student/my-courses');
    courses.value = response.data || response || [];
  } catch (err) {
    console.error('Error loading courses:', err);
    courses.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(loadCourses);
</script>

