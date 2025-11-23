<template>
  <div class="space-y-8">
    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="!program" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('public.programs.notFound') || 'Program not found' }}</p>
    </div>

    <div v-else class="space-y-8">
      <!-- Hero Section -->
      <div class="bg-gradient-to-r from-primary to-primary/80 rounded-2xl p-8 text-white">
        <div class="max-w-4xl">
          <h1 class="text-4xl font-black mb-4">{{ program.title }}</h1>
          <p class="text-lg text-white/90 mb-6">{{ program.description }}</p>
          <div class="flex flex-wrap gap-4">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ program.duration_weeks }} {{ $t('public.programs.weeks') || 'weeks' }}</span>
            </div>
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="capitalize">{{ program.level }}</span>
            </div>
            <div v-if="program.price" class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ formatCurrency(program.price) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Program Image -->
      <div v-if="program.image_path" class="rounded-xl overflow-hidden">
        <img :src="program.image_path" :alt="program.title" class="w-full h-96 object-cover" />
      </div>

      <!-- Full Description -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
        <div class="prose dark:prose-invert max-w-none" v-html="program.description"></div>
      </div>

      <!-- Batches -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">{{ $t('public.programs.batches') || 'Available Batches' }}</h2>
        <div v-if="batches.length === 0" class="text-center py-8 text-slate-500 dark:text-slate-400">
          {{ $t('public.programs.noBatches') || 'No batches available at the moment' }}
        </div>
        <div v-else class="grid md:grid-cols-2 gap-6">
          <div
            v-for="batch in batches"
            :key="batch.id"
            class="p-6 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50"
          >
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ batch.name || batch.code }}</h3>
            <div class="space-y-2 text-sm text-slate-600 dark:text-slate-400 mb-4">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ formatDate(batch.start_date) }} - {{ formatDate(batch.end_date) }}</span>
              </div>
              <div v-if="batch.max_students" class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>{{ $t('public.programs.maxStudents') || 'Max' }}: {{ batch.max_students }}</span>
              </div>
            </div>
            <button class="btn-primary w-full">
              {{ $t('public.programs.enroll') || 'Enroll Now' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useI18nStore } from '../../../stores/i18n';

const route = useRoute();
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
    const response = await get(`/programs/${route.params.slug}?locale=${locale}`);
    program.value = response?.data || response;
    
    if (program.value.batches) {
      batches.value = program.value.batches;
    } else {
      const batchesResponse = await get(`/programs/${route.params.slug}/batches?locale=${locale}`);
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

