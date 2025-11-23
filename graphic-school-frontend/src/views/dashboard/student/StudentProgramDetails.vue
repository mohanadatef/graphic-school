<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <div>
        <RouterLink
          :to="{ name: 'student-programs' }"
          class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary mb-2 inline-flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          {{ $t('common.back') || 'Back' }}
        </RouterLink>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white">{{ program?.title || 'Program Details' }}</h1>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="!program" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('student.programs.notFound') || 'Program not found' }}</p>
    </div>

    <div v-else class="space-y-6">
      <!-- Program Info -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div v-if="program.image_path" class="mb-6">
          <img :src="program.image_path" :alt="program.title" class="w-full h-64 object-cover rounded-lg" />
        </div>
        <div class="prose dark:prose-invert max-w-none">
          <p class="text-slate-600 dark:text-slate-400">{{ program.description }}</p>
        </div>
        <div class="grid md:grid-cols-3 gap-4 mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
          <div>
            <label class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.programs.type') || 'Type' }}</label>
            <p class="text-slate-900 dark:text-white font-medium">{{ program.type }}</p>
          </div>
          <div>
            <label class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.programs.duration') || 'Duration' }}</label>
            <p class="text-slate-900 dark:text-white font-medium">{{ program.duration_weeks }} {{ $t('student.programs.weeks') || 'weeks' }}</p>
          </div>
          <div>
            <label class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.programs.price') || 'Price' }}</label>
            <p class="text-slate-900 dark:text-white font-medium">{{ formatCurrency(program.price) }}</p>
          </div>
        </div>
      </div>

      <!-- Batches -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4">{{ $t('student.programs.batches') || 'Available Batches' }}</h2>
        <div v-if="batches.length === 0" class="text-center py-8 text-slate-500 dark:text-slate-400">
          {{ $t('student.programs.noBatches') || 'No batches available' }}
        </div>
        <div v-else class="space-y-3">
          <div
            v-for="batch in batches"
            :key="batch.id"
            class="p-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50"
          >
            <div class="flex items-start justify-between">
              <div>
                <h3 class="font-medium text-slate-900 dark:text-white">{{ batch.name || batch.code }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                  {{ formatDate(batch.start_date) }} - {{ formatDate(batch.end_date) }}
                </p>
              </div>
              <span :class="batch.is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'" class="px-2 py-1 text-xs font-medium rounded-full">
                {{ batch.is_active ? ($t('common.active') || 'Active') : ($t('common.inactive') || 'Inactive') }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useI18nStore } from '../../../stores/i18n';

const route = useRoute();
const router = useRouter();
const { get } = useApi();
const i18nStore = useI18nStore();

const loading = ref(false);
const program = ref(null);
const batches = ref([]);

onMounted(async () => {
  await loadProgram();
});

async function loadProgram() {
  try {
    loading.value = true;
    const locale = i18nStore.locale;
    const response = await get(`/admin/programs/${route.params.id}?locale=${locale}`);
    program.value = response?.data || response;
    
    if (program.value.batches) {
      batches.value = program.value.batches;
    } else {
      // Load batches separately
      const batchesResponse = await get(`/programs/${program.value.slug}/batches?locale=${locale}`);
      batches.value = batchesResponse?.data || batchesResponse || [];
    }
  } catch (error) {
    console.error('Error loading program:', error);
  } finally {
    loading.value = false;
  }
}

function formatCurrency(amount) {
  if (!amount) return '-';
  return new Intl.NumberFormat(i18nStore.locale === 'ar' ? 'ar-EG' : 'en-US', {
    style: 'currency',
    currency: 'EGP',
  }).format(amount);
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString(i18nStore.locale === 'ar' ? 'ar-EG' : 'en-US');
}
</script>

