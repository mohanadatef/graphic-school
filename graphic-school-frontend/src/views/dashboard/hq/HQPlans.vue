<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('hq.plans.title') || 'Subscription Plans' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('hq.plans.subtitle') || 'Manage subscription plans' }}</p>
      </div>
      <button @click="showCreateModal = true" class="btn-primary">{{ $t('hq.plans.create') || 'Create Plan' }}</button>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="plan in plans" :key="plan.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-xl font-bold mb-2">{{ plan.name }}</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ plan.description }}</p>
        <p class="text-2xl font-bold mb-4">{{ plan.price_monthly }} {{ plan.currency }}/month</p>
        <div class="flex gap-2">
          <button @click="editPlan(plan)" class="btn-secondary flex-1">{{ $t('common.edit') || 'Edit' }}</button>
          <button @click="deletePlan(plan.id)" class="btn-danger flex-1">{{ $t('common.delete') || 'Delete' }}</button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || editingPlan" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">{{ editingPlan ? ($t('hq.plans.edit') || 'Edit Plan') : ($t('hq.plans.create') || 'Create Plan') }}</h3>
        <form @submit.prevent="savePlan" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">{{ $t('hq.plans.name') || 'Name' }}</label>
            <input v-model="planForm.name" type="text" class="w-full px-4 py-2 border rounded-lg" required>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">{{ $t('hq.plans.code') || 'Code' }}</label>
            <input v-model="planForm.code" type="text" class="w-full px-4 py-2 border rounded-lg" required>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-2">{{ $t('hq.plans.priceMonthly') || 'Monthly Price' }}</label>
              <input v-model.number="planForm.price_monthly" type="number" step="0.01" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">{{ $t('hq.plans.priceYearly') || 'Yearly Price' }}</label>
              <input v-model.number="planForm.price_yearly" type="number" step="0.01" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">{{ $t('hq.plans.description') || 'Description' }}</label>
            <textarea v-model="planForm.description" class="w-full px-4 py-2 border rounded-lg"></textarea>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" @click="closeModal" class="btn-secondary">{{ $t('common.cancel') || 'Cancel' }}</button>
            <button type="submit" class="btn-primary" :disabled="saving">{{ saving ? ($t('common.saving') || 'Saving...') : ($t('common.save') || 'Save') }}</button>
          </div>
        </form>
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
const saving = ref(false);
const plans = ref([]);
const showCreateModal = ref(false);
const editingPlan = ref(null);
const planForm = ref({
  name: '',
  code: '',
  price_monthly: 0,
  price_yearly: 0,
  currency: 'EGP',
  description: '',
  features: [],
  limits: {},
  is_active: true,
});

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

function editPlan(plan) {
  editingPlan.value = plan;
  planForm.value = { ...plan };
  showCreateModal.value = true;
}

async function savePlan() {
  saving.value = true;
  try {
    if (editingPlan.value) {
      await api.put(`/hq/plans/${editingPlan.value.id}`, planForm.value);
      toast.success('Plan updated successfully');
    } else {
      await api.post('/hq/plans', planForm.value);
      toast.success('Plan created successfully');
    }
    closeModal();
    await loadPlans();
  } catch (error) {
    toast.error('Failed to save plan');
  } finally {
    saving.value = false;
  }
}

async function deletePlan(id) {
  if (!confirm('Are you sure you want to delete this plan?')) return;
  try {
    await api.delete(`/hq/plans/${id}`);
    toast.success('Plan deleted successfully');
    await loadPlans();
  } catch (error) {
    toast.error('Failed to delete plan');
  }
}

function closeModal() {
  showCreateModal.value = false;
  editingPlan.value = null;
  planForm.value = {
    name: '',
    code: '',
    price_monthly: 0,
    price_yearly: 0,
    currency: 'EGP',
    description: '',
    features: [],
    limits: {},
    is_active: true,
  };
}

onMounted(() => {
  loadPlans();
});
</script>

