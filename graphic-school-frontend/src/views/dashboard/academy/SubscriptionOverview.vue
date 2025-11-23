<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('subscription.overview.title') || 'Subscription Overview' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('subscription.overview.subtitle') || 'Manage your subscription and plan' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="subscription" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Current Plan Card -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold mb-4">{{ $t('subscription.overview.currentPlan') || 'Current Plan' }}</h3>
        <div class="space-y-3">
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('subscription.overview.planName') || 'Plan' }}</p>
            <p class="text-xl font-bold text-slate-900 dark:text-white">{{ subscription.plan?.name || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('subscription.overview.status') || 'Status' }}</p>
            <span :class="getStatusClass(subscription.status)" class="px-3 py-1 rounded-full text-sm font-medium">
              {{ subscription.status }}
            </span>
          </div>
          <div v-if="subscription.trial_ends_at">
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('subscription.overview.trialEnds') || 'Trial Ends' }}</p>
            <p class="text-lg font-semibold text-slate-900 dark:text-white">{{ formatDate(subscription.trial_ends_at) }}</p>
            <p class="text-xs text-yellow-600">{{ subscription.days_until_trial_ends }} days remaining</p>
          </div>
          <div v-if="subscription.expires_at">
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('subscription.overview.expiresAt') || 'Expires At' }}</p>
            <p class="text-lg font-semibold text-slate-900 dark:text-white">{{ formatDate(subscription.expires_at) }}</p>
          </div>
        </div>
      </div>

      <!-- Actions Card -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold mb-4">{{ $t('subscription.overview.actions') || 'Actions' }}</h3>
        <div class="space-y-3">
          <router-link to="/academy/subscription/plans" class="btn-primary block text-center">
            {{ $t('subscription.overview.changePlan') || 'Change Plan' }}
          </router-link>
          <button @click="handleRenew" class="btn-secondary w-full" :disabled="renewing">
            {{ renewing ? ($t('common.processing') || 'Processing...') : ($t('subscription.overview.renew') || 'Renew Subscription') }}
          </button>
          <button @click="handleCancel" class="btn-danger w-full" :disabled="canceling">
            {{ canceling ? ($t('common.processing') || 'Processing...') : ($t('subscription.overview.cancel') || 'Cancel Subscription') }}
          </button>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('subscription.overview.noSubscription') || 'No active subscription found' }}</p>
      <router-link to="/academy/subscription/plans" class="btn-primary mt-4 inline-block">
        {{ $t('subscription.overview.selectPlan') || 'Select a Plan' }}
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const router = useRouter();
const toast = useToast();
const loading = ref(false);
const renewing = ref(false);
const canceling = ref(false);
const subscription = ref(null);

async function loadSubscription() {
  loading.value = true;
  try {
    const response = await api.get('/academy/subscription');
    subscription.value = response.data.data || response.data;
    if (subscription.value.trial_ends_at) {
      subscription.value.days_until_trial_ends = Math.ceil((new Date(subscription.value.trial_ends_at) - new Date()) / (1000 * 60 * 60 * 24));
    }
  } catch (error) {
    console.error('Error loading subscription:', error);
    if (error.response?.status !== 404) {
      toast.error('Failed to load subscription');
    }
  } finally {
    loading.value = false;
  }
}

async function handleRenew() {
  renewing.value = true;
  try {
    await api.post('/academy/subscription/renew');
    toast.success('Subscription renewed successfully');
    await loadSubscription();
  } catch (error) {
    toast.error('Failed to renew subscription');
  } finally {
    renewing.value = false;
  }
}

async function handleCancel() {
  if (!confirm('Are you sure you want to cancel your subscription?')) return;
  
  canceling.value = true;
  try {
    await api.post('/academy/subscription/cancel');
    toast.success('Subscription canceled successfully');
    await loadSubscription();
  } catch (error) {
    toast.error('Failed to cancel subscription');
  } finally {
    canceling.value = false;
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
  loadSubscription();
});
</script>

