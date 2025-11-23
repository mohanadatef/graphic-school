<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('student.payments.title') || 'My Payments' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.payments.subtitle') || 'View and pay your invoices' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="invoices.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('student.payments.noInvoices') || 'No invoices found' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="invoice in invoices" :key="invoice.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ invoice.invoice_number }}</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">{{ invoice.enrollment?.program?.title || invoice.enrollment?.program?.name }}</p>
            <div class="grid md:grid-cols-3 gap-4 text-sm">
              <div>
                <p class="text-slate-500 dark:text-slate-400">{{ $t('student.payments.totalAmount') || 'Total Amount' }}</p>
                <p class="font-medium text-slate-900 dark:text-white">{{ formatCurrency(invoice.total_amount) }}</p>
              </div>
              <div>
                <p class="text-slate-500 dark:text-slate-400">{{ $t('student.payments.paidAmount') || 'Paid Amount' }}</p>
                <p class="font-medium text-slate-900 dark:text-white">{{ formatCurrency(invoice.paid_amount) }}</p>
              </div>
              <div>
                <p class="text-slate-500 dark:text-slate-400">{{ $t('student.payments.dueDate') || 'Due Date' }}</p>
                <p class="font-medium text-slate-900 dark:text-white">{{ formatDate(invoice.due_date) }}</p>
              </div>
            </div>
          </div>
          <div class="ml-4">
            <span
              :class="{
                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': invoice.status === 'unpaid' || invoice.status === 'overdue',
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': invoice.status === 'partially_paid',
                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': invoice.status === 'paid',
              }"
              class="px-3 py-1 text-sm font-semibold rounded-full"
            >
              {{ $t(`student.payments.${invoice.status}`) || invoice.status }}
            </span>
          </div>
        </div>

        <div v-if="invoice.status !== 'paid'" class="mt-4 flex gap-3">
          <button
            @click="$router.push({ name: 'student-invoice-view', params: { id: invoice.id } })"
            class="btn-primary"
          >
            {{ $t('student.payments.payNow') || 'Pay Now' }}
          </button>
          <button
            @click="$router.push({ name: 'student-invoice-view', params: { id: invoice.id } })"
            class="btn-secondary"
          >
            {{ $t('common.view') || 'View Details' }}
          </button>
        </div>
      </div>
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
    const response = await api.get('/student/invoices');
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
