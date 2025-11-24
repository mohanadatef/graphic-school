<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.batches.title') || 'Batches' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          {{ $t('admin.batches.subtitle') || 'Program' }}: {{ program?.title || programId }}
        </p>
      </div>
      <button
        data-cy="create-btn"
        @click="$router.push({ name: 'admin-batches-new' })"
        class="btn-primary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ $t('admin.batches.create') || 'Create Batch' }}
      </button>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="batches.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.batches.noBatches') || 'No batches found' }}</p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="batch in batches"
        :key="batch.id"
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow"
      >
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ batch.name || batch.code }}</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ batch.code }}</p>
          </div>
          <span :class="batch.is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'" class="px-2 py-1 text-xs font-medium rounded-full">
            {{ batch.is_active ? ($t('common.active') || 'Active') : ($t('common.inactive') || 'Inactive') }}
          </span>
        </div>
        
        <div class="space-y-2 text-sm text-slate-600 dark:text-slate-400 mb-4">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>{{ formatDate(batch.start_date) }} - {{ formatDate(batch.end_date) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>{{ $t('admin.batches.maxStudents') || 'Max' }}: {{ batch.max_students || '-' }}</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button
            @click="$router.push({ name: 'admin-batches-groups', params: { batchId: batch.id } })"
            class="btn-secondary flex-1 text-sm"
          >
            {{ $t('admin.batches.viewGroups') || 'View Groups' }}
          </button>
          <button
            @click="$router.push({ name: 'admin-batches-edit', params: { id: batch.id } })"
            class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const { get } = useApi();
const toast = useToast();

const programId = route.params.programId || route.query.program_id;
const loading = ref(false);
const program = ref(null);
const batches = ref([]);

onMounted(async () => {
  await loadProgram();
  await loadBatches();
});

async function loadProgram() {
  if (!programId) return;
  try {
    const response = await get(`/admin/programs/${programId}`);
    program.value = response?.data || response;
  } catch (error) {
    console.error('Error loading program:', error);
  }
}

async function loadBatches() {
  try {
    loading.value = true;
    const response = await get(`/admin/batches?program_id=${programId}`);
    batches.value = response?.data || response || [];
  } catch (error) {
    toast.error('Failed to load batches');
    console.error(error);
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}
</script>

