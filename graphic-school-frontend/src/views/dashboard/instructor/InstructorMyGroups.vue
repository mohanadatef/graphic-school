<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
        {{ $t('instructor.myGroups.title') || 'My Groups' }}
      </h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        {{ $t('instructor.myGroups.subtitle') || 'View all groups assigned to you' }}
      </p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="groups.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">
        {{ $t('instructor.myGroups.noGroups') || 'No groups assigned yet' }}
      </p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="group in groups"
        :key="group.id"
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-shadow"
      >
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ group.name || group.code }}</h3>
              <p class="text-sm text-slate-500 dark:text-slate-400">{{ group.code }}</p>
            </div>
            <span
              :class="group.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'"
              class="px-2 py-1 text-xs font-semibold rounded-full"
            >
              {{ group.is_active ? ($t('common.active') || 'Active') : ($t('common.inactive') || 'Inactive') }}
            </span>
          </div>

          <div v-if="group.course" class="mb-4">
            <p class="text-xs text-slate-500 dark:text-slate-500 mb-1">
              {{ $t('instructor.myGroups.course') || 'Course' }}:
            </p>
            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ group.course.title }}</p>
            <p v-if="group.course.category" class="text-xs text-slate-500 dark:text-slate-400">
              {{ group.course.category.name }}
            </p>
          </div>

          <div class="space-y-2 text-sm text-slate-600 dark:text-slate-400 mb-4">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span>{{ $t('instructor.myGroups.students') || 'Students' }}: {{ group.students_count }} / {{ group.capacity }}</span>
            </div>
            <div v-if="group.room" class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span>{{ group.room }}</span>
            </div>
            <div v-if="group.available_spots !== undefined" class="flex items-center gap-2">
              <span>{{ $t('instructor.myGroups.availableSpots') || 'Available Spots' }}: {{ group.available_spots }}</span>
            </div>
          </div>

          <div class="flex gap-2">
            <RouterLink
              :to="`/dashboard/instructor/groups/${group.id}/sessions`"
              class="flex-1 text-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm"
            >
              {{ $t('instructor.myGroups.viewSessions') || 'Sessions' }}
            </RouterLink>
            <RouterLink
              :to="`/dashboard/instructor/groups/${group.id}/students`"
              class="flex-1 text-center px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors text-sm"
            >
              {{ $t('instructor.myGroups.students') || 'Students' }}
            </RouterLink>
          </div>
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

const groups = ref([]);
const loading = ref(false);

async function loadGroups() {
  try {
    loading.value = true;
    const response = await get('/instructor/my-groups');
    groups.value = response.data || response || [];
  } catch (err) {
    console.error('Error loading groups:', err);
    groups.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(loadGroups);
</script>

