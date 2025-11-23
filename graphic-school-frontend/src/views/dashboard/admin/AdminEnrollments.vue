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
                {{ $t('admin.enrollments.program') || 'Program' }}
              </th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('admin.enrollments.batch') || 'Batch' }}
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
                {{ enrollment.program?.title || enrollment.program?.name || '-' }}
              </td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                {{ enrollment.batch?.code || '-' }}
              </td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                {{ enrollment.group?.code || enrollment.group?.name || '-' }}
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
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const router = useRouter();
const toast = useToast();
const loading = ref(false);
const enrollments = ref([]);
const filters = reactive({
  search: '',
  status: '',
});
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0,
});

async function loadEnrollments() {
  loading.value = true;
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      ...filters,
    };
    const response = await api.get('/admin/enrollments', { params });
    enrollments.value = response.data.data || response.data;
    
    if (response.data.meta) {
      Object.assign(pagination, response.data.meta);
    } else if (response.meta) {
      Object.assign(pagination, response.meta);
    }
  } catch (error) {
    console.error('Error loading enrollments:', error);
    toast.error(error.response?.data?.message || 'Failed to load enrollments');
  } finally {
    loading.value = false;
  }
}

async function approveEnrollment(id) {
  try {
    await api.post(`/admin/enrollments/${id}/approve`);
    toast.success('Enrollment approved successfully');
    loadEnrollments();
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to approve enrollment');
  }
}

async function rejectEnrollment(id) {
  if (!confirm('Are you sure you want to reject this enrollment?')) return;
  try {
    await api.post(`/admin/enrollments/${id}/reject`);
    toast.success('Enrollment rejected successfully');
    loadEnrollments();
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to reject enrollment');
  }
}

async function withdrawEnrollment(id) {
  if (!confirm('Are you sure you want to withdraw this enrollment?')) return;
  try {
    await api.post(`/admin/enrollments/${id}/withdraw`);
    toast.success('Enrollment withdrawn successfully');
    loadEnrollments();
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to withdraw enrollment');
  }
}

function handleSearch() {
  pagination.current_page = 1;
  loadEnrollments();
}

function handleFilterChange() {
  pagination.current_page = 1;
  loadEnrollments();
}

function changePage(page) {
  pagination.current_page = page;
  loadEnrollments();
}

onMounted(() => {
  loadEnrollments();
});
</script>
