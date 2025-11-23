<template>
  <div class="space-y-8">
    <div class="text-center py-12">
      <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-4">{{ $t('public.programs.title') || 'Our Programs' }}</h1>
      <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
        {{ $t('public.programs.subtitle') || 'Discover our comprehensive training programs designed to help you excel in your career' }}
      </p>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 justify-center">
      <FilterDropdown
        v-model="filters.type"
        :options="typeOptions"
        :placeholder="$t('public.programs.filterType') || 'All Types'"
        @update:modelValue="loadPrograms"
      />
      <FilterDropdown
        v-model="filters.level"
        :options="levelOptions"
        :placeholder="$t('public.programs.filterLevel') || 'All Levels'"
        @update:modelValue="loadPrograms"
      />
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="programs.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('public.programs.noPrograms') || 'No programs found' }}</p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div
        v-for="program in programs"
        :key="program.id"
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-lg transition-all cursor-pointer group"
        @click="$router.push({ name: 'public-programs-details', params: { slug: program.slug } })"
      >
        <div v-if="program.image_path" class="h-48 bg-slate-200 dark:bg-slate-700 overflow-hidden">
          <img :src="program.image_path" :alt="program.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
        </div>
        <div class="p-6">
          <div class="flex items-start justify-between mb-3">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
              {{ program.title }}
            </h3>
            <span class="px-2 py-1 text-xs font-medium rounded-full bg-primary/10 text-primary">
              {{ program.type }}
            </span>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-3">{{ program.description }}</p>
          <div class="flex items-center justify-between text-sm pt-4 border-t border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-4">
              <span class="text-slate-600 dark:text-slate-400">{{ program.duration_weeks }} {{ $t('public.programs.weeks') || 'weeks' }}</span>
              <span v-if="program.level" class="text-slate-600 dark:text-slate-400 capitalize">{{ program.level }}</span>
            </div>
            <span v-if="program.price" class="font-bold text-primary">{{ formatCurrency(program.price) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../../api';
import { useI18n } from '../../../composables/useI18n';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';

const router = useRouter();
const { locale } = useI18n();

const loading = ref(false);
const programs = ref([]);

const filters = reactive({
  type: '',
  level: '',
});

const typeOptions = [
  { id: '', name: 'All Types' },
  { id: 'bootcamp', name: 'Bootcamp' },
  { id: 'track', name: 'Track' },
  { id: 'workshop', name: 'Workshop' },
  { id: 'course', name: 'Course' },
];

const levelOptions = [
  { id: '', name: 'All Levels' },
  { id: 'beginner', name: 'Beginner' },
  { id: 'intermediate', name: 'Intermediate' },
  { id: 'advanced', name: 'Advanced' },
];

onMounted(async () => {
  await loadPrograms();
});

async function loadPrograms() {
  try {
    loading.value = true;
    const params = new URLSearchParams({ locale: locale.value });
    if (filters.type) params.append('type', filters.type);
    if (filters.level) params.append('level', filters.level);
    
    const response = await api.get(`/programs?${params.toString()}`);
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
  return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-US', {
    style: 'currency',
    currency: 'EGP',
  }).format(amount);
}
</script>

