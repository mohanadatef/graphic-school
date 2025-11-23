<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-black text-slate-900 dark:text-white">{{ $t('instructor.groups.title') || 'My Groups' }}</h1>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="groups.length === 0" class="text-center py-20">
      <svg class="w-24 h-24 mx-auto text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('instructor.groups.noGroups') || 'No groups assigned' }}</p>
    </div>

    <div v-else class="space-y-6">
      <div
        v-for="group in groups"
        :key="group.id"
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6"
      >
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ group.name || group.code }}</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">
              {{ group.batch?.program?.title || group.batch?.code }} - {{ group.batch?.name || group.batch?.code }}
            </p>
          </div>
          <button
            @click="toggleSessions(group.id)"
            class="btn-secondary text-sm"
          >
            {{ expandedGroups.includes(group.id) ? ($t('instructor.groups.hideSessions') || 'Hide Sessions') : ($t('instructor.groups.viewSessions') || 'View Sessions') }}
          </button>
        </div>

        <div class="grid md:grid-cols-3 gap-4 text-sm text-slate-600 dark:text-slate-400 mb-4">
          <div>
            <span class="font-medium">{{ $t('instructor.groups.capacity') || 'Capacity' }}:</span>
            <span class="ml-2">{{ group.capacity }}</span>
          </div>
          <div v-if="group.room">
            <span class="font-medium">{{ $t('instructor.groups.room') || 'Room' }}:</span>
            <span class="ml-2">{{ group.room }}</span>
          </div>
          <div>
            <span class="font-medium">{{ $t('instructor.groups.students') || 'Students' }}:</span>
            <span class="ml-2">{{ group.students?.length || 0 }}</span>
          </div>
        </div>

        <!-- Sessions -->
        <div v-if="expandedGroups.includes(group.id)" class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
          <div v-if="loadingSessions[group.id]" class="text-center py-8">
            <div class="spinner mx-auto mb-2"></div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
          </div>
          <div v-else-if="groupSessions[group.id]?.length === 0" class="text-center py-8 text-slate-500 dark:text-slate-400">
            {{ $t('instructor.groups.noSessions') || 'No sessions scheduled' }}
          </div>
          <div v-else class="space-y-2">
            <div
              v-for="session in groupSessions[group.id]"
              :key="session.id"
              class="p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50"
            >
              <div class="flex items-start justify-between">
                <div>
                  <p class="font-medium text-slate-900 dark:text-white">{{ session.title }}</p>
                  <div class="flex items-center gap-4 mt-1 text-sm text-slate-500 dark:text-slate-400">
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '../../../composables/useApi';
import { useI18nStore } from '../../../stores/i18n';
import { useAuthStore } from '../../../stores/auth';

const { get } = useApi();
const i18nStore = useI18nStore();
const authStore = useAuthStore();

const loading = ref(false);
const groups = ref([]);
const expandedGroups = ref([]);
const groupSessions = ref({});
const loadingSessions = ref({});

onMounted(async () => {
  await loadGroups();
});

async function loadGroups() {
  try {
    loading.value = true;
    const response = await get(`/admin/groups?instructor_id=${authStore.user?.id}`);
    groups.value = response?.data || response || [];
  } catch (error) {
    console.error('Error loading groups:', error);
    groups.value = [];
  } finally {
    loading.value = false;
  }
}

async function toggleSessions(groupId) {
  if (expandedGroups.value.includes(groupId)) {
    expandedGroups.value = expandedGroups.value.filter(id => id !== groupId);
  } else {
    expandedGroups.value.push(groupId);
    await loadSessions(groupId);
  }
}

async function loadSessions(groupId) {
  try {
    loadingSessions.value[groupId] = true;
    const locale = i18nStore.locale;
    const response = await get(`/groups/${groupId}/sessions?locale=${locale}`);
    groupSessions.value[groupId] = response?.data || response || [];
  } catch (error) {
    console.error('Error loading sessions:', error);
    groupSessions.value[groupId] = [];
  } finally {
    loadingSessions.value[groupId] = false;
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

