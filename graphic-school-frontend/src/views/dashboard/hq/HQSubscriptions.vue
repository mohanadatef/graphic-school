<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('hq.subscriptions.title') || 'Academy Subscriptions' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('hq.subscriptions.subtitle') || 'Manage all academy subscriptions' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Academy</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Plan</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Expires At</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <tr v-for="subscription in subscriptions" :key="subscription.id">
            <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">{{ subscription.academy?.name }}</td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ subscription.plan?.name }}</td>
            <td class="px-6 py-4 text-sm">
              <span :class="getStatusClass(subscription.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ subscription.status }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ formatDate(subscription.expires_at) }}</td>
            <td class="px-6 py-4 text-sm space-x-2">
              <button v-if="subscription.status !== 'suspended'" @click="suspendSubscription(subscription.id)" class="text-yellow-600">Suspend</button>
              <button v-if="subscription.status === 'suspended'" @click="resumeSubscription(subscription.id)" class="text-green-600">Resume</button>
              <router-link :to="`/hq/subscriptions/${subscription.id}/usage`" class="text-primary">Usage</router-link>
            </td>
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
const subscriptions = ref([]);

async function loadSubscriptions() {
  loading.value = true;
  try {
    const response = await api.get('/hq/subscriptions');
    subscriptions.value = response.data.data?.data || response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading subscriptions:', error);
    toast.error('Failed to load subscriptions');
  } finally {
    loading.value = false;
  }
}

async function suspendSubscription(id) {
  try {
    await api.put(`/hq/subscriptions/${id}/suspend`);
    toast.success('Subscription suspended');
    await loadSubscriptions();
  } catch (error) {
    toast.error('Failed to suspend subscription');
  }
}

async function resumeSubscription(id) {
  try {
    await api.put(`/hq/subscriptions/${id}/resume`);
    toast.success('Subscription resumed');
    await loadSubscriptions();
  } catch (error) {
    toast.error('Failed to resume subscription');
  }
}

function getStatusClass(status) {
  const classes = {
    active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    trial: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    expired: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    canceled: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
    suspended: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
  };
  return classes[status] || classes.canceled;
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadSubscriptions();
});
</script>

