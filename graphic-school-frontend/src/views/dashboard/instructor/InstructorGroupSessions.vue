<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
          {{ $t('instructor.groupSessions.title') || 'Group Sessions' }}
        </h2>
        <p v-if="group" class="text-sm text-slate-500 dark:text-slate-400">
          {{ group.name || group.code }} - {{ group.course?.title }}
        </p>
      </div>
      <RouterLink
        :to="`/dashboard/instructor/groups/${groupId}`"
        class="text-sm text-primary hover:underline"
      >
        {{ $t('common.back') || 'Back to Group' }}
      </RouterLink>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div class="flex flex-wrap items-center gap-3">
        <select v-model="filters.status" class="input w-40">
          <option value="">{{ $t('common.allStatuses') || 'All Statuses' }}</option>
          <option value="scheduled">{{ $t('common.scheduled') || 'Scheduled' }}</option>
          <option value="completed">{{ $t('common.completed') || 'Completed' }}</option>
          <option value="cancelled">{{ $t('common.cancelled') || 'Cancelled' }}</option>
        </select>
        <input
          v-model="filters.from_date"
          type="date"
          class="input"
          :placeholder="$t('common.fromDate') || 'From Date'"
        />
        <input
          v-model="filters.to_date"
          type="date"
          class="input"
          :placeholder="$t('common.toDate') || 'To Date'"
        />
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-700 text-xs uppercase text-slate-500 dark:text-slate-400">
            <tr>
              <th class="px-4 py-3 text-left">{{ $t('common.session') || 'Session' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('common.dateTime') || 'Date & Time' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('common.room') || 'Room' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('common.status') || 'Status' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('common.actions') || 'Actions' }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="session in sessions"
              :key="session.id"
              class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50"
            >
              <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">
                {{ session.title }}
              </td>
              <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                {{ formatDateTime(session.session_date, session.start_time, session.end_time) }}
              </td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                {{ session.room || '--' }}
              </td>
              <td class="px-4 py-3">
                <span
                  :class="getStatusClass(session.status)"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ $t(`common.${session.status}`) || session.status }}
                </span>
              </td>
              <td class="px-4 py-3">
                <RouterLink
                  :to="`/dashboard/instructor/sessions/${session.id}/attendance`"
                  class="text-primary text-sm hover:underline"
                >
                  {{ $t('instructor.takeAttendance') || 'Take Attendance' }}
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-if="!sessions.length" class="text-center py-6 text-sm text-slate-400">
          {{ $t('common.noData') || 'No sessions found' }}
        </p>
      </div>

      <PaginationControls
        v-if="pagination.total"
        :meta="pagination"
        @change-page="changePage"
        @change-per-page="changePerPage"
      />
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useI18n } from '../../../composables/useI18n';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const route = useRoute();
const { get } = useApi();
const { t } = useI18n();

const groupId = ref(route.params.groupId);
const group = ref(null);
const sessions = ref([]);
const loading = ref(true);
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

const filters = reactive({
  status: '',
  from_date: '',
  to_date: '',
  page: 1,
  per_page: 10,
});

async function loadGroup() {
  try {
    const { data } = await get(`/instructor/groups/${groupId.value}`);
    group.value = data;
  } catch (error) {
    console.error('Failed to load group:', error);
  }
}

async function loadSessions() {
  loading.value = true;
  try {
    const { data } = await get(`/instructor/groups/${groupId.value}/sessions`, {
      params: {
        status: filters.status || undefined,
        from_date: filters.from_date || undefined,
        to_date: filters.to_date || undefined,
        page: filters.page,
        per_page: filters.per_page,
      },
    });

    sessions.value = data.data || data;
    if (data.meta) {
      Object.assign(pagination, {
        current_page: data.meta.current_page,
        last_page: data.meta.last_page,
        per_page: data.meta.per_page,
        total: data.meta.total,
      });
    }
  } catch (error) {
    console.error('Failed to load sessions:', error);
  } finally {
    loading.value = false;
  }
}

function formatDateTime(date, startTime, endTime) {
  if (!date) return '--';
  const formattedDate = new Date(date).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
  const start = startTime ? startTime.slice(0, 5) : '--:--';
  const end = endTime ? endTime.slice(0, 5) : '';
  return `${formattedDate} ${start}${end ? ` - ${end}` : ''}`;
}

function getStatusClass(status) {
  const classes = {
    scheduled: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
  };
  return classes[status] || 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-400';
}

function changePage(page) {
  filters.page = page;
  loadSessions();
}

function changePerPage(perPage) {
  filters.per_page = perPage;
  filters.page = 1;
  loadSessions();
}

watch(
  () => [filters.status, filters.from_date, filters.to_date],
  () => {
    filters.page = 1;
    loadSessions();
  },
);

onMounted(async () => {
  await loadGroup();
  loadSessions();
});
</script>

<style scoped>
.input {
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.5rem 0.75rem;
  background: white;
}

.dark .input {
  background: #1e293b;
  border-color: #334155;
  color: white;
}
</style>

