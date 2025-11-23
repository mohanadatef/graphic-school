<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('subscription.plans.title') || 'Select a Plan' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('subscription.plans.subtitle') || 'Choose the plan that fits your needs' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="plan in plans" :key="plan.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ plan.name }}</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ plan.description }}</p>
        
        <div class="mb-4">
          <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ plan.price_monthly }} {{ plan.currency }}</p>
          <p class="text-sm text-slate-500 dark:text-slate-400">per month</p>
          <p class="text-sm text-slate-500 dark:text-slate-400">or {{ plan.price_yearly }} {{ plan.currency }}/year</p>
        </div>

        <div v-if="plan.features && plan.features.length > 0" class="mb-4">
          <h4 class="font-semibold mb-2">{{ $t('subscription.plans.features') || 'Features' }}</h4>
          <ul class="space-y-1 text-sm">
            <li v-for="feature in plan.features" :key="feature" class="flex items-center">
              <span class="text-green-500 mr-2">✓</span>
              {{ feature }}
            </li>
          </ul>
        </div>

        <div v-if="plan.limits" class="mb-4">
          <h4 class="font-semibold mb-2">{{ $t('subscription.plans.limits') || 'Limits' }}</h4>
          <ul class="space-y-1 text-sm">
            <li v-for="(limit, key) in plan.limits" :key="key">
              <strong>{{ key }}:</strong> {{ limit }}
            </li>
          </ul>
        </div>

        <button @click="selectPlan(plan.id)" class="btn-primary w-full" :disabled="selecting">
          {{ selecting ? ($t('common.processing') || 'Processing...') : ($t('subscription.plans.select') || 'Select Plan') }}
        </button>
      </div>
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
const selecting = ref(false);
const plans = ref([]);

async function loadPlans() {
  loading.value = true;
  try {
    const response = await api.get('/hq/plans');
    plans.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading plans:', error);
    toast.error('Failed to load plans');
  } finally {
    loading.value = false;
  }
}

async function selectPlan(planId) {
  selecting.value = true;
  try {
    await api.post('/academy/subscription/change-plan', { plan_id: planId });
    toast.success('Plan changed successfully');
    router.push('/academy/subscription');
  } catch (error) {
    toast.error('Failed to change plan');
  } finally {
    selecting.value = false;
  }
}

onMounted(() => {
  loadPlans();
});
</script>

