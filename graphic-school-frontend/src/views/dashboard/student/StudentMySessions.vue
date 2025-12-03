<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
        {{ $t('student.mySessions.title') || 'My Sessions' }}
      </h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        {{ $t('student.mySessions.subtitle') || 'View all your scheduled sessions' }}
      </p>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
      <div class="flex flex-wrap gap-3 items-center">
        <select
          v-model="filters.status"
          class="text-xs px-3 py-1.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
          @change="loadSessions"
        >
          <option value="">{{ $t('student.mySessions.allStatuses') || 'All Statuses' }}</option>
          <option value="scheduled">{{ $t('student.mySessions.scheduled') || 'Scheduled' }}</option>
          <option value="completed">{{ $t('student.mySessions.completed') || 'Completed' }}</option>
          <option value="cancelled">{{ $t('student.mySessions.cancelled') || 'Cancelled' }}</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="sessions.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">
        {{ $t('student.mySessions.noSessions') || 'No sessions found' }}
      </p>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="session in sessions"
        :key="session.id"
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6"
      >
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
              {{ session.title || session.session_template?.title || 'Session' }}
            </h3>
            <p v-if="session.group?.course" class="text-sm text-slate-500 dark:text-slate-400 mt-1">
              {{ session.group.course.title }}
            </p>
            <p v-if="session.group" class="text-sm text-slate-500 dark:text-slate-400">
              {{ $t('student.mySessions.group') || 'Group' }}: {{ session.group.name || session.group.code }}
            </p>
          </div>
          <span
            :class="{
              'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': session.status === 'scheduled',
              'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': session.status === 'completed',
              'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': session.status === 'cancelled',
            }"
            class="px-2 py-1 text-xs font-semibold rounded-full"
          >
            {{ session.status }}
          </span>
        </div>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="text-xs text-slate-500 dark:text-slate-400">{{ $t('student.mySessions.date') || 'Date' }}</label>
            <p class="text-sm font-medium text-slate-900 dark:text-white">
              {{ formatDate(session.session_date) }}
            </p>
          </div>
          <div>
            <label class="text-xs text-slate-500 dark:text-slate-400">{{ $t('student.mySessions.time') || 'Time' }}</label>
            <p class="text-sm font-medium text-slate-900 dark:text-white">
              {{ session.start_time }} - {{ session.end_time }}
            </p>
          </div>
          <div v-if="session.attendance && session.attendance.length">
            <label class="text-xs text-slate-500 dark:text-slate-400">{{ $t('student.mySessions.attendance') || 'Attendance' }}</label>
            <p class="text-sm font-medium text-slate-900 dark:text-white">
              <span
                :class="{
                  'text-green-600 dark:text-green-400': session.attendance[0].status === 'present',
                  'text-red-600 dark:text-red-400': session.attendance[0].status === 'absent',
                }"
              >
                {{ session.attendance[0].status }}
              </span>
            </p>
          </div>
        </div>

        <div v-if="session.note" class="mt-4 p-3 bg-slate-50 dark:bg-slate-900 rounded-lg">
          <p class="text-sm text-slate-600 dark:text-slate-400">{{ session.note }}</p>
        </div>
      </div>
    </div>

    <PaginationControls
      v-if="!loading && sessions.length > 0 && pagination"
      :meta="{
        current_page: pagination?.current_page || 1,
        last_page: pagination?.last_page || 1,
        per_page: pagination?.per_page || 15,
        total: pagination?.total || 0,
      }"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useListPage } from '../../../composables/useListPage';
import { useI18n } from '../../../composables/useI18n';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const { t } = useI18n();

const {
  items: sessions,
  loading,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
} = useListPage({
  endpoint: '/student/my-sessions',
  initialFilters: {
    status: '',
  },
  perPage: 15,
  autoApplyFilters: false,
});

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

function loadSessions() {
  loadItems();
}

onMounted(loadSessions);
</script>

