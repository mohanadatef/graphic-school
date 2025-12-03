<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.enrollments.title') || 'Enrollments Management' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.enrollments.subtitle') || 'Manage all student enrollments' }}</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
      <div class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[200px]">
          <input
            v-model="filters.search"
            class="w-full pl-10 pr-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
            :placeholder="$t('common.search') || 'Search...'"
            @input="handleSearch"
          />
        </div>
        <select
          v-model="filters.status"
          class="px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20"
          @change="handleFilterChange"
        >
          <option value="">{{ $t('admin.enrollments.allStatus') || 'All Status' }}</option>
          <option value="pending">{{ $t('admin.enrollments.pending') || 'Pending' }}</option>
          <option value="approved">{{ $t('admin.enrollments.approved') || 'Approved' }}</option>
          <option value="rejected">{{ $t('admin.enrollments.rejected') || 'Rejected' }}</option>
          <option value="withdrawn">{{ $t('admin.enrollments.withdrawn') || 'Withdrawn' }}</option>
        </select>
      </div>
    </div>

    <!-- Enrollments Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div v-if="loading" class="text-center py-20">
        <div class="spinner-lg mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
      </div>

      <div v-else-if="enrollments.length === 0" class="text-center py-20">
        <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.enrollments.noEnrollments') || 'No enrollments found' }}</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
            <tr>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('admin.enrollments.student') || 'Student' }}
              </th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('admin.enrollments.course') || 'Course' }}
              </th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('admin.enrollments.group') || 'Group' }}
              </th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('admin.enrollments.status') || 'Status' }}
              </th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('common.actions') || 'Actions' }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <tr v-for="enrollment in enrollments" :key="enrollment.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
              <td class="px-4 py-3">
                <div class="font-medium text-slate-900 dark:text-white">{{ enrollment.student?.name }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ enrollment.student?.email }}</div>
              </td>
              <td class="px-4 py-3 text-slate-900 dark:text-white">
                {{ enrollment.course?.title || '-' }}
              </td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                {{ enrollment.group?.name || enrollment.group?.code || '-' }}
              </td>
              <td class="px-4 py-3">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': enrollment.status === 'pending',
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': enrollment.status === 'approved',
                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': enrollment.status === 'rejected',
                    'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400': enrollment.status === 'withdrawn',
                  }"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ $t(`admin.enrollments.${enrollment.status}`) || enrollment.status }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <button
                    v-if="enrollment.status === 'pending'"
                    @click="approveEnrollment(enrollment.id)"
                    class="px-3 py-1 text-xs bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
                  >
                    {{ $t('admin.enrollments.approve') || 'Approve' }}
                  </button>
                  <button
                    v-if="enrollment.status === 'pending'"
                    @click="rejectEnrollment(enrollment.id)"
                    class="px-3 py-1 text-xs bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                  >
                    {{ $t('admin.enrollments.reject') || 'Reject' }}
                  </button>
                  <button
                    v-if="enrollment.status === 'approved'"
                    @click="withdrawEnrollment(enrollment.id)"
                    class="px-3 py-1 text-xs bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                  >
                    {{ $t('admin.enrollments.withdraw') || 'Withdraw' }}
                  </button>
                  <button
                    @click="$router.push({ name: 'admin-enrollment-review', params: { id: enrollment.id } })"
                    class="px-3 py-1 text-xs bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors"
                  >
                    {{ $t('common.view') || 'View' }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <div class="text-sm text-slate-600 dark:text-slate-400">
          {{ $t('common.showing') || 'Showing' }} {{ pagination.from }} {{ $t('common.to') || 'to' }} {{ pagination.to }} {{ $t('common.of') || 'of' }} {{ pagination.total }}
        </div>
        <div class="flex gap-2">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 text-sm border border-slate-300 dark:border-slate-600 rounded-lg disabled:opacity-50"
          >
            {{ $t('common.previous') || 'Previous' }}
          </button>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 text-sm border border-slate-300 dark:border-slate-600 rounded-lg disabled:opacity-50"
          >
            {{ $t('common.next') || 'Next' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useI18n } from '../../../composables/useI18n';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const router = useRouter();
const toast = useToast();
const { t } = useI18n();
const { post } = useApi();

// Use unified list page composable
const {
  items: enrollments,
  loading,
  error,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
  loadItemsDebounced,
  applyFilters,
} = useListPage({
  endpoint: '/admin/enrollments',
  initialFilters: {
    search: '',
    status: '',
  },
  perPage: 15,
  debounceMs: 500,
  autoApplyFilters: false,
});

function handleSearch() {
  loadItemsDebounced();
}

function handleFilterChange() {
  applyFilters();
}

async function approveEnrollment(id) {
  try {
    await post(`/admin/enrollments/${id}/approve`);
    toast.success(t('admin.enrollments.approvedSuccess') || 'Enrollment approved successfully');
    await loadItems();
  } catch (error) {
    toast.error(error.response?.data?.message || t('errors.approveError') || 'Failed to approve enrollment');
  }
}

async function rejectEnrollment(id) {
  const confirmMessage = t('admin.enrollments.confirmReject') || 'Are you sure you want to reject this enrollment?';
  if (!confirm(confirmMessage)) return;
  try {
    await post(`/admin/enrollments/${id}/reject`);
    toast.success(t('admin.enrollments.rejectedSuccess') || 'Enrollment rejected successfully');
    await loadItems();
  } catch (error) {
    toast.error(error.response?.data?.message || t('errors.rejectError') || 'Failed to reject enrollment');
  }
}

async function withdrawEnrollment(id) {
  const confirmMessage = t('admin.enrollments.confirmWithdraw') || 'Are you sure you want to withdraw this enrollment?';
  if (!confirm(confirmMessage)) return;
  try {
    await post(`/admin/enrollments/${id}/withdraw`);
    toast.success(t('admin.enrollments.withdrawnSuccess') || 'Enrollment withdrawn successfully');
    await loadItems();
  } catch (error) {
    toast.error(error.response?.data?.message || t('errors.withdrawError') || 'Failed to withdraw enrollment');
  }
}

onMounted(async () => {
  await loadItems();
});
</script>
