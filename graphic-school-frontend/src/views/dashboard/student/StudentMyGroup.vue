<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
        {{ $t('student.myGroup.title') || 'My Group' }}
      </h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        {{ $t('student.myGroup.subtitle') || 'View your group information' }}
      </p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="!group" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">
        {{ $t('student.myGroup.noGroup') || 'No group assigned yet' }}
      </p>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 space-y-6">
      <!-- Group Info -->
      <div>
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
          {{ $t('student.myGroup.groupInfo') || 'Group Information' }}
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.myGroup.code') || 'Code' }}</label>
            <p class="text-lg font-medium text-slate-900 dark:text-white">{{ group.code }}</p>
          </div>
          <div>
            <label class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.myGroup.name') || 'Name' }}</label>
            <p class="text-lg font-medium text-slate-900 dark:text-white">{{ group.name }}</p>
          </div>
          <div>
            <label class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.myGroup.capacity') || 'Capacity' }}</label>
            <p class="text-lg font-medium text-slate-900 dark:text-white">
              {{ group.students_count }} / {{ group.capacity }}
            </p>
          </div>
          <div v-if="group.room">
            <label class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.myGroup.room') || 'Room' }}</label>
            <p class="text-lg font-medium text-slate-900 dark:text-white">{{ group.room }}</p>
          </div>
        </div>
      </div>

      <!-- Course Info -->
      <div v-if="group.course">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
          {{ $t('student.myGroup.course') || 'Course' }}
        </h3>
        <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-lg">
          <p class="font-medium text-slate-900 dark:text-white">{{ group.course.title }}</p>
          <p v-if="group.course.description" class="text-sm text-slate-600 dark:text-slate-400 mt-2">
            {{ group.course.description }}
          </p>
        </div>
      </div>

      <!-- Instructor -->
      <div v-if="group.instructor">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
          {{ $t('student.myGroup.instructor') || 'Instructor' }}
        </h3>
        <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-900 rounded-lg">
          <div v-if="group.instructor.avatar_path" class="w-12 h-12 rounded-full overflow-hidden">
            <img :src="group.instructor.avatar_path" :alt="group.instructor.name" class="w-full h-full object-cover" />
          </div>
          <div>
            <p class="font-medium text-slate-900 dark:text-white">{{ group.instructor.name }}</p>
            <p v-if="group.instructor.email" class="text-sm text-slate-600 dark:text-slate-400">
              {{ group.instructor.email }}
            </p>
          </div>
        </div>
      </div>

      <!-- Students -->
      <div v-if="group.students && group.students.length">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
          {{ $t('student.myGroup.students') || 'Group Members' }} ({{ group.students.length }})
        </h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
          <div
            v-for="student in group.students"
            :key="student.id"
            class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-900 rounded-lg"
          >
            <div v-if="student.avatar_path" class="w-10 h-10 rounded-full overflow-hidden">
              <img :src="student.avatar_path" :alt="student.name" class="w-full h-full object-cover" />
            </div>
            <div>
              <p class="font-medium text-slate-900 dark:text-white">{{ student.name }}</p>
              <p v-if="student.email" class="text-xs text-slate-600 dark:text-slate-400">
                {{ student.email }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useI18n } from '../../../composables/useI18n';

const route = useRoute();
const { get } = useApi();
const { t } = useI18n();

const group = ref(null);
const loading = ref(false);

async function loadGroup() {
  try {
    loading.value = true;
    const params = route.query.course_id ? { course_id: route.query.course_id } : {};
    const response = await get('/student/my-group', { params });
    group.value = response.data || response;
  } catch (err) {
    if (err.response?.status !== 404) {
      console.error('Error loading group:', err);
    }
    group.value = null;
  } finally {
    loading.value = false;
  }
}

onMounted(loadGroup);
</script>

