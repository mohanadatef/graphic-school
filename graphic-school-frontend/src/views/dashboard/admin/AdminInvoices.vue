<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.invoices.title') || 'Invoices Management' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.invoices.subtitle') || 'Manage all invoices' }}</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
      <div class="flex flex-wrap gap-3 items-center">
        <select
          v-model="filters.status"
          class="px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
          @change="loadInvoices"
        >
          <option value="">{{ $t('admin.invoices.allStatus') || 'All Status' }}</option>
          <option value="unpaid">{{ $t('admin.invoices.unpaid') || 'Unpaid' }}</option>
          <option value="partially_paid">{{ $t('admin.invoices.partiallyPaid') || 'Partially Paid' }}</option>
          <option value="paid">{{ $t('admin.invoices.paid') || 'Paid' }}</option>
          <option value="overdue">{{ $t('admin.invoices.overdue') || 'Overdue' }}</option>
        </select>
      </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div v-if="loading" class="text-center py-20">
        <div class="spinner-lg mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
      </div>

      <div v-else-if="invoices.length === 0" class="text-center py-20">
        <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.invoices.noInvoices') || 'No invoices found' }}</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
            <tr>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.invoices.invoiceNumber') || 'Invoice #' }}</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.invoices.student') || 'Student' }}</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.invoices.amount') || 'Amount' }}</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.invoices.status') || 'Status' }}</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.invoices.dueDate') || 'Due Date' }}</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('common.actions') || 'Actions' }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <tr v-for="invoice in invoices" :key="invoice.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
              <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ invoice.invoice_number }}</td>
              <td class="px-4 py-3 text-slate-900 dark:text-white">{{ invoice.enrollment?.student?.name }}</td>
              <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ formatCurrency(invoice.total_amount) }}</td>
              <td class="px-4 py-3">
                <span
                  :class="{
                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': invoice.status === 'unpaid' || invoice.status === 'overdue',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': invoice.status === 'partially_paid',
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': invoice.status === 'paid',
                  }"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ $t(`admin.invoices.${invoice.status}`) || invoice.status }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ formatDate(invoice.due_date) }}</td>
              <td class="px-4 py-3">
                <button
                  @click="$router.push({ name: 'admin-invoice-view', params: { id: invoice.id } })"
                  class="px-3 py-1 text-xs bg-primary text-white rounded-lg hover:bg-primary/90"
                >
                  {{ $t('common.view') || 'View' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const loading = ref(false);
const invoices = ref([]);
const filters = reactive({ status: '' });

async function loadInvoices() {
  loading.value = true;
  try {
    const params = { ...filters };
    const response = await api.get('/admin/invoices', { params });
    invoices.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error loading invoices:', error);
    toast.error('Failed to load invoices');
  } finally {
    loading.value = false;
  }
}

function formatCurrency(amount) {
  if (!amount) return '-';
  return new Intl.NumberFormat('ar-EG', { style: 'currency', currency: 'EGP' }).format(amount);
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadInvoices();
});
</script>

