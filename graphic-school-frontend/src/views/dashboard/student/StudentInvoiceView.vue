<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <button @click="$router.back()" class="text-primary mb-2">← {{ $t('common.back') || 'Back' }}</button>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('student.payments.invoice') || 'Invoice' }} #{{ invoice?.invoice_number }}</h2>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="invoice" class="space-y-6">
      <!-- Invoice Details -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="mb-6">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('student.payments.invoiceDetails') || 'Invoice Details' }}</h3>
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.payments.program') || 'Program' }}</p>
              <p class="font-medium text-slate-900 dark:text-white">{{ invoice.enrollment?.program?.title || invoice.enrollment?.program?.name }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.payments.status') || 'Status' }}</p>
              <span
                :class="{
                  'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': invoice.status === 'unpaid' || invoice.status === 'overdue',
                  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': invoice.status === 'partially_paid',
                  'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': invoice.status === 'paid',
                }"
                class="px-2 py-1 text-xs font-semibold rounded-full"
              >
                {{ $t(`student.payments.${invoice.status}`) || invoice.status }}
              </span>
            </div>
          </div>
        </div>

        <!-- Invoice Items -->
        <div class="border-t border-slate-200 dark:border-slate-700 pt-6 mb-6">
          <h4 class="font-semibold text-slate-900 dark:text-white mb-4">{{ $t('student.payments.items') || 'Items' }}</h4>
          <div class="space-y-2">
            <div v-for="item in invoice.items" :key="item.id" class="flex justify-between p-3 bg-slate-50 dark:bg-slate-900 rounded-lg">
              <span class="text-slate-900 dark:text-white">{{ item.title }}</span>
              <span class="font-medium text-slate-900 dark:text-white">{{ formatCurrency(item.amount) }}</span>
            </div>
          </div>
          <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-between">
            <span class="font-semibold text-slate-900 dark:text-white">{{ $t('student.payments.total') || 'Total' }}</span>
            <span class="font-bold text-lg text-slate-900 dark:text-white">{{ formatCurrency(invoice.total_amount) }}</span>
          </div>
        </div>

        <!-- Payment Form -->
        <div v-if="invoice.status !== 'paid'" class="border-t border-slate-200 dark:border-slate-700 pt-6">
          <h4 class="font-semibold text-slate-900 dark:text-white mb-4">{{ $t('student.payments.paymentMethod') || 'Payment Method' }}</h4>
          <div class="space-y-4">
            <select v-model="paymentForm.payment_method_id" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
              <option v-for="method in paymentMethods" :key="method.id" :value="method.id">{{ method.name }}</option>
            </select>
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('student.payments.amount') || 'Amount' }}</label>
              <input v-model.number="paymentForm.amount" type="number" step="0.01" :max="invoice.total_amount - invoice.paid_amount" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white" />
            </div>
            <button @click="processPayment" :disabled="processing" class="btn-primary w-full">
              <span v-if="processing">{{ $t('common.loading') || 'Processing...' }}</span>
              <span v-else>{{ $t('student.payments.payNow') || 'Pay Now' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const toast = useToast();
const loading = ref(false);
const processing = ref(false);
const invoice = ref(null);
const paymentMethods = ref([]);
const paymentForm = reactive({
  payment_method_id: null,
  amount: 0,
});

async function loadInvoice() {
  loading.value = true;
  try {
    const response = await api.get(`/student/invoices/${route.params.id}`);
    invoice.value = response.data;
    paymentForm.amount = invoice.value.total_amount - invoice.value.paid_amount;
    
    // Load payment methods
    const methodsResponse = await api.get('/admin/payment-methods');
    paymentMethods.value = methodsResponse.data || [];
    if (paymentMethods.value.length > 0) {
      paymentForm.payment_method_id = paymentMethods.value[0].id;
    }
  } catch (error) {
    console.error('Error loading invoice:', error);
    toast.error('Failed to load invoice');
  } finally {
    loading.value = false;
  }
}

async function processPayment() {
  processing.value = true;
  try {
    await api.post('/student/invoices/pay', {
      invoice_id: invoice.value.id,
      ...paymentForm,
    });
    toast.success('Payment processed successfully');
    loadInvoice();
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to process payment');
  } finally {
    processing.value = false;
  }
}

function formatCurrency(amount) {
  if (!amount) return '-';
  return new Intl.NumberFormat('ar-EG', { style: 'currency', currency: 'EGP' }).format(amount);
}

onMounted(() => {
  loadInvoice();
});
</script>

