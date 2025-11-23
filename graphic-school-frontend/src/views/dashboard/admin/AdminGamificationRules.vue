<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.gamification.rules.title') || 'Gamification Rules' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.gamification.rules.subtitle') || 'Manage point rules' }}</p>
      </div>
      <button @click="showCreateModal = true" class="btn-primary">{{ $t('admin.gamification.rules.create') || 'Create Rule' }}</button>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Code</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Name</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Points</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Active</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <tr v-for="rule in rules" :key="rule.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-slate-900 dark:text-white">{{ rule.code }}</td>
            <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">{{ rule.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">{{ rule.points }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="rule.active ? 'text-green-600' : 'text-red-600'" class="text-sm">
                {{ rule.active ? '✓' : '✗' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button @click="editRule(rule)" class="text-primary mr-4">{{ $t('common.edit') || 'Edit' }}</button>
              <button @click="deleteRule(rule.id)" class="text-red-600">{{ $t('common.delete') || 'Delete' }}</button>
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
const rules = ref([]);
const showCreateModal = ref(false);

async function loadRules() {
  loading.value = true;
  try {
    const response = await api.get('/admin/gamification/rules');
    rules.value = response.data.data?.data || response.data.data || [];
  } catch (error) {
    console.error('Error loading rules:', error);
    toast.error('Failed to load rules');
  } finally {
    loading.value = false;
  }
}

function editRule(rule) {
  // TODO: Implement edit modal
  toast.info('Edit functionality coming soon');
}

async function deleteRule(id) {
  if (!confirm('Are you sure you want to delete this rule?')) return;
  
  try {
    await api.delete(`/admin/gamification/rules/${id}`);
    toast.success('Rule deleted successfully');
    loadRules();
  } catch (error) {
    toast.error('Failed to delete rule');
  }
}

onMounted(() => {
  loadRules();
});
</script>

