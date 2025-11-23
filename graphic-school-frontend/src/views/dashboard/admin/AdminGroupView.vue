<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ group?.name || group?.code || 'Group Details' }}</h2>
        <nav class="text-sm text-slate-500 dark:text-slate-400 mt-1">
          <RouterLink :to="{ name: 'admin-programs' }" class="hover:text-primary">
            {{ $t('admin.programs.title') || 'Programs' }}
          </RouterLink>
          <span class="mx-2">/</span>
          <RouterLink v-if="batch" :to="{ name: 'admin-batches-groups', params: { batchId: batch.id } }" class="hover:text-primary">
            {{ batch.name || batch.code }}
          </RouterLink>
          <span class="mx-2">/</span>
          <span>{{ group?.name || group?.code }}</span>
        </nav>
      </div>
      <RouterLink
        :to="{ name: 'admin-batches-groups', params: { batchId: batch?.id } }"
        class="btn-secondary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ $t('common.back') || 'Back' }}
      </RouterLink>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else class="space-y-6">
      <!-- Group Info -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">{{ $t('admin.groups.info') || 'Group Information' }}</h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.groups.code') || 'Code' }}</label>
            <p class="text-slate-900 dark:text-white font-medium">{{ group?.code }}</p>
          </div>
          <div>
            <label class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.groups.capacity') || 'Capacity' }}</label>
            <p class="text-slate-900 dark:text-white font-medium">{{ group?.capacity }}</p>
          </div>
          <div>
            <label class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.groups.room') || 'Room' }}</label>
            <p class="text-slate-900 dark:text-white font-medium">{{ group?.room || '-' }}</p>
          </div>
          <div>
            <label class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.groups.instructor') || 'Instructor' }}</label>
            <p class="text-slate-900 dark:text-white font-medium">{{ group?.instructor?.name || '-' }}</p>
          </div>
        </div>
      </div>

      <!-- Students -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
          {{ $t('admin.groups.students') || 'Students' }} ({{ students.length }})
        </h3>
        <div v-if="students.length === 0" class="text-center py-8 text-slate-500 dark:text-slate-400">
          {{ $t('admin.groups.noStudents') || 'No students assigned' }}
        </div>
        <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="student in students"
            :key="student.id"
            class="p-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50"
          >
            <p class="font-medium text-slate-900 dark:text-white">{{ student.name }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ student.email }}</p>
          </div>
        </div>
      </div>

      <!-- Sessions -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
          {{ $t('admin.groups.sessions') || 'Sessions' }} ({{ sessions.length }})
        </h3>
        <div v-if="sessions.length === 0" class="text-center py-8 text-slate-500 dark:text-slate-400">
          {{ $t('admin.groups.noSessions') || 'No sessions scheduled' }}
        </div>
        <div v-else class="space-y-3">
          <div
            v-for="session in sessions"
            :key="session.id"
            class="p-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50"
          >
            <div class="flex items-start justify-between">
              <div>
                <p class="font-medium text-slate-900 dark:text-white">{{ session.title }}</p>
                <div class="flex items-center gap-4 mt-2 text-sm text-slate-500 dark:text-slate-400">
                  <span>{{ formatDate(session.session_date) }}</span>
                  <span v-if="session.start_time">{{ session.start_time }} - {{ session.end_time }}</span>
                </div>
              </div>
              <span :class="getStatusClass(session.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                {{ session.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useI18nStore } from '../../../stores/i18n';

const route = useRoute();
const router = useRouter();
const { get } = useApi();
const i18nStore = useI18nStore();

const loading = ref(false);
const group = ref(null);
const batch = ref(null);
const students = ref([]);
const sessions = ref([]);

onMounted(async () => {
  await loadGroup();
});

async function loadGroup() {
  try {
    loading.value = true;
    const locale = i18nStore.locale;
    const response = await get(`/admin/groups/${route.params.groupId}?include_translations=true&locale=${locale}`);
    group.value = response?.data || response;
    
    if (group.value.batch) {
      batch.value = group.value.batch;
    }
    
    if (group.value.students) {
      students.value = group.value.students;
    }
    
    // Load sessions
    const sessionsResponse = await get(`/groups/${route.params.groupId}/sessions?locale=${locale}`);
    sessions.value = sessionsResponse?.data || sessionsResponse || [];
  } catch (error) {
    console.error('Error loading group:', error);
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString(i18nStore.locale === 'ar' ? 'ar-EG' : 'en-US');
}

function getStatusClass(status) {
  const classes = {
    scheduled: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    completed: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
    cancelled: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
  };
  return classes[status] || classes.scheduled;
}
</script>

