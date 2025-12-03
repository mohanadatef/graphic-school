<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
        {{ $t('student.attendanceHistory.title') || 'Attendance History' }}
      </h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        {{ $t('student.attendanceHistory.subtitle') || 'View your attendance records' }}
      </p>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
      <div class="flex flex-wrap gap-3 items-center">
        <select
          v-model="filters.status"
          class="text-xs px-3 py-1.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
          @change="loadAttendance"
        >
          <option value="">{{ $t('student.attendanceHistory.allStatuses') || 'All Statuses' }}</option>
          <option value="present">{{ $t('student.attendanceHistory.present') || 'Present' }}</option>
          <option value="absent">{{ $t('student.attendanceHistory.absent') || 'Absent' }}</option>
          <option value="late">{{ $t('student.attendanceHistory.late') || 'Late' }}</option>
        </select>
        <input
          v-model="filters.date_from"
          type="date"
          class="text-xs px-3 py-1.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
          @change="loadAttendance"
        />
        <input
          v-model="filters.date_to"
          type="date"
          class="text-xs px-3 py-1.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
          @change="loadAttendance"
        />
      </div>
    </div>

    <!-- Stats -->
    <div v-if="!loading && attendance.length > 0" class="grid md:grid-cols-3 gap-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.attendanceHistory.total') || 'Total' }}</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.total }}</p>
      </div>
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.attendanceHistory.present') || 'Present' }}</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.present }}</p>
      </div>
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.attendanceHistory.absent') || 'Absent' }}</p>
        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ stats.absent }}</p>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="attendance.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">
        {{ $t('student.attendanceHistory.noRecords') || 'No attendance records found' }}
      </p>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-900 text-xs uppercase">
            <tr>
              <th class="px-4 py-3 text-left">{{ $t('student.attendanceHistory.date') || 'Date' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('student.attendanceHistory.course') || 'Course' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('student.attendanceHistory.session') || 'Session' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('student.attendanceHistory.status') || 'Status' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('student.attendanceHistory.note') || 'Note' }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="record in attendance"
              :key="record.id"
              class="border-t border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50"
            >
              <td class="px-4 py-3">
                {{ formatDate(record.group_session?.session_date) }}
              </td>
              <td class="px-4 py-3">
                {{ record.group_session?.group?.course?.title || '-' }}
              </td>
              <td class="px-4 py-3">
                {{ record.group_session?.title || record.group_session?.session_template?.title || '-' }}
              </td>
              <td class="px-4 py-3">
                <span
                  :class="{
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': record.status === 'present',
                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': record.status === 'absent',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': record.status === 'late',
                  }"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ record.status }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                {{ record.note || '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <PaginationControls
      v-if="!loading && attendance.length > 0 && pagination"
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
import { ref, computed, onMounted } from 'vue';
import { useListPage } from '../../../composables/useListPage';
import { useI18n } from '../../../composables/useI18n';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const { t } = useI18n();

const {
  items: attendance,
  loading,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
} = useListPage({
  endpoint: '/student/attendance-history',
  initialFilters: {
    status: '',
    date_from: '',
    date_to: '',
  },
  perPage: 15,
  autoApplyFilters: false,
});

const stats = computed(() => {
  const total = attendance.value.length;
  const present = attendance.value.filter(a => a.status === 'present').length;
  const absent = attendance.value.filter(a => a.status === 'absent').length;
  return { total, present, absent };
});

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

function loadAttendance() {
  loadItems();
}

onMounted(loadAttendance);
</script>

