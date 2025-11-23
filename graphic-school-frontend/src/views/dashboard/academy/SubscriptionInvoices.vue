<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('subscription.invoices.title') || 'Invoices' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('subscription.invoices.subtitle') || 'View your billing history' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="invoices.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('subscription.invoices.noInvoices') || 'No invoices found' }}</p>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Invoice #</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Plan</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Amount</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Period</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Date</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <tr v-for="invoice in invoices" :key="invoice.id">
            <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">#{{ invoice.id }}</td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ invoice.plan?.name }}</td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ invoice.amount }} {{ invoice.currency }}</td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ invoice.billing_period }}</td>
            <td class="px-6 py-4 text-sm">
              <span :class="getStatusClass(invoice.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ invoice.status }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ formatDate(invoice.created_at) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const loading = ref(false);
const invoices = ref([]);

async function loadInvoices() {
  loading.value = true;
  try {
    const response = await api.get('/academy/subscription/invoices');
    invoices.value = response.data.data?.data || response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading invoices:', error);
    toast.error('Failed to load invoices');
  } finally {
    loading.value = false;
  }
}

function getStatusClass(status) {
  const classes = {
    paid: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    unpaid: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
  };
  return classes[status] || classes.unpaid;
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadInvoices();
});
</script>

