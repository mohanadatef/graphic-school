<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <button @click="$router.back()" class="text-primary mb-2">← {{ $t('common.back') || 'Back' }}</button>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.invoices.invoice') || 'Invoice' }} #{{ invoice?.invoice_number }}</h2>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="invoice" class="space-y-6">
      <!-- Invoice Details -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="grid md:grid-cols-2 gap-6 mb-6">
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">{{ $t('admin.invoices.student') || 'Student' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ invoice.enrollment?.student?.name }}</p>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ invoice.enrollment?.student?.email }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">{{ $t('admin.invoices.program') || 'Program' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ invoice.enrollment?.program?.title || invoice.enrollment?.program?.name }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">{{ $t('admin.invoices.status') || 'Status' }}</p>
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
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">{{ $t('admin.invoices.dueDate') || 'Due Date' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ formatDate(invoice.due_date) }}</p>
          </div>
        </div>

        <!-- Invoice Items -->
        <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('admin.invoices.items') || 'Invoice Items' }}</h3>
          <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-900">
              <tr>
                <th class="px-4 py-2 text-right text-xs font-semibold text-slate-600 dark:text-slate-300">{{ $t('admin.invoices.item') || 'Item' }}</th>
                <th class="px-4 py-2 text-right text-xs font-semibold text-slate-600 dark:text-slate-300">{{ $t('admin.invoices.quantity') || 'Quantity' }}</th>
                <th class="px-4 py-2 text-right text-xs font-semibold text-slate-600 dark:text-slate-300">{{ $t('admin.invoices.amount') || 'Amount' }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
              <tr v-for="item in invoice.items" :key="item.id">
                <td class="px-4 py-2 text-slate-900 dark:text-white">{{ item.title }}</td>
                <td class="px-4 py-2 text-slate-600 dark:text-slate-400">{{ item.quantity }}</td>
                <td class="px-4 py-2 font-medium text-slate-900 dark:text-white">{{ formatCurrency(item.amount) }}</td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-50 dark:bg-slate-900">
              <tr>
                <td colspan="2" class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ $t('admin.invoices.total') || 'Total' }}</td>
                <td class="px-4 py-3 font-bold text-lg text-slate-900 dark:text-white">{{ formatCurrency(invoice.total_amount) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Payment Transactions -->
        <div v-if="invoice.transactions?.length > 0" class="border-t border-slate-200 dark:border-slate-700 pt-6 mt-6">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('admin.invoices.payments') || 'Payments' }}</h3>
          <div class="space-y-2">
            <div v-for="transaction in invoice.transactions" :key="transaction.id" class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900 rounded-lg">
              <div>
                <p class="font-medium text-slate-900 dark:text-white">{{ transaction.payment_method?.name }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ transaction.reference_code }}</p>
              </div>
              <div class="text-right">
                <p class="font-medium text-slate-900 dark:text-white">{{ formatCurrency(transaction.amount) }}</p>
                <span
                  :class="{
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': transaction.status === 'success',
                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': transaction.status === 'failed',
                  }"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ transaction.status }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Mark Paid Button -->
        <div v-if="invoice.status !== 'paid'" class="border-t border-slate-200 dark:border-slate-700 pt-6 mt-6">
          <button @click="showMarkPaidModal = true" class="btn-primary">
            {{ $t('admin.invoices.markPaid') || 'Mark as Paid' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Mark Paid Modal -->
    <div v-if="showMarkPaidModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">{{ $t('admin.invoices.markPaid') || 'Mark as Paid' }}</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('admin.invoices.paymentMethod') || 'Payment Method' }}</label>
            <select v-model="markPaidForm.payment_method_id" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
              <option v-for="method in paymentMethods" :key="method.id" :value="method.id">{{ method.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('admin.invoices.amount') || 'Amount' }}</label>
            <input v-model.number="markPaidForm.amount" type="number" step="0.01" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('admin.invoices.referenceCode') || 'Reference Code' }}</label>
            <input v-model="markPaidForm.reference_code" type="text" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white" />
          </div>
          <div class="flex gap-3">
            <button @click="markAsPaid" class="btn-primary flex-1">{{ $t('common.save') || 'Save' }}</button>
            <button @click="showMarkPaidModal = false" class="btn-secondary flex-1">{{ $t('common.cancel') || 'Cancel' }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const loading = ref(false);
const invoice = ref(null);
const paymentMethods = ref([]);
const showMarkPaidModal = ref(false);
const markPaidForm = reactive({
  payment_method_id: null,
  amount: 0,
  reference_code: '',
});

async function loadInvoice() {
  loading.value = true;
  try {
    const response = await api.get(`/admin/invoices/${route.params.id}`);
    invoice.value = response.data;
    markPaidForm.amount = invoice.value.total_amount - invoice.value.paid_amount;
    
    // Load payment methods
    const methodsResponse = await api.get('/admin/payment-methods');
    paymentMethods.value = methodsResponse.data || [];
    if (paymentMethods.value.length > 0) {
      markPaidForm.payment_method_id = paymentMethods.value[0].id;
    }
  } catch (error) {
    console.error('Error loading invoice:', error);
    toast.error('Failed to load invoice');
  } finally {
    loading.value = false;
  }
}

async function markAsPaid() {
  try {
    await api.post(`/admin/invoices/${route.params.id}/mark-paid`, markPaidForm);
    toast.success('Invoice marked as paid successfully');
    showMarkPaidModal.value = false;
    loadInvoice();
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to mark invoice as paid');
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
  loadInvoice();
});
</script>

