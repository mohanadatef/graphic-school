<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-black text-slate-900 dark:text-white">{{ $t('student.programs.title') || 'My Programs' }}</h1>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="programs.length === 0" class="text-center py-20">
      <svg class="w-24 h-24 mx-auto text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('student.programs.noPrograms') || 'No programs enrolled' }}</p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="program in programs"
        :key="program.id"
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-shadow cursor-pointer"
        @click="$router.push({ name: 'student-programs-details', params: { id: program.id } })"
      >
        <div v-if="program.image_path" class="h-48 bg-slate-200 dark:bg-slate-700 overflow-hidden">
          <img :src="program.image_path" :alt="program.title" class="w-full h-full object-cover" />
        </div>
        <div class="p-6">
          <div class="flex items-start justify-between mb-2">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ program.title }}</h3>
            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
              {{ program.type }}
            </span>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">{{ program.description }}</p>
          <div class="flex items-center justify-between text-sm">
            <span class="text-slate-600 dark:text-slate-400">{{ program.duration_weeks }} {{ $t('student.programs.weeks') || 'weeks' }}</span>
            <span v-if="program.price" class="font-medium text-slate-900 dark:text-white">{{ formatCurrency(program.price) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useI18nStore } from '../../../stores/i18n';

const router = useRouter();
const { get } = useApi();
const i18nStore = useI18nStore();

const loading = ref(false);
const programs = ref([]);

onMounted(async () => {
  await loadPrograms();
});

async function loadPrograms() {
  try {
    loading.value = true;
    const locale = i18nStore.locale;
    // For now, show all active programs. Later, filter by student enrollment
    const response = await get(`/programs?locale=${locale}&is_active=true`);
    programs.value = response?.data || response || [];
  } catch (error) {
    console.error('Error loading programs:', error);
    programs.value = [];
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
</script>

