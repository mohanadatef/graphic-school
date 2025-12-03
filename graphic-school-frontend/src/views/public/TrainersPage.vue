<template>
  <div class="space-y-8">
    <!-- CMS Content -->
    <CMSPageRenderer slug="trainers" />

    <!-- Instructors List -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div v-if="loading" class="text-center py-20">
        <div class="spinner-lg mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
      </div>

      <div v-else-if="instructors.length === 0" class="text-center py-20">
        <p class="text-slate-500 dark:text-slate-400 text-lg">
          {{ $t('trainers.noInstructors') || 'No instructors available' }}
        </p>
      </div>

      <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="instructor in instructors"
          :key="instructor.id"
          class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-shadow"
        >
          <div class="p-6 text-center">
            <div v-if="instructor.avatar_path" class="w-24 h-24 rounded-full overflow-hidden mx-auto mb-4">
              <img :src="instructor.avatar_path" :alt="instructor.name" class="w-full h-full object-cover" />
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
              {{ instructor.name }}
            </h3>
            <p v-if="instructor.bio" class="text-sm text-slate-600 dark:text-slate-400 line-clamp-3 mb-4">
              {{ instructor.bio }}
            </p>
            <RouterLink
              :to="`/instructors/${instructor.id}`"
              class="inline-block px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm"
            >
              {{ $t('trainers.viewProfile') || 'View Profile' }}
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { useApi } from '../../composables/useApi';
import { useI18n } from '../../composables/useI18n';
import CMSPageRenderer from '../../components/public/CMSPageRenderer.vue';

const { get } = useApi();
const { t, locale } = useI18n();

const instructors = ref([]);
const loading = ref(false);

async function loadInstructors() {
  try {
    loading.value = true;
    const response = await get('/public/instructors', {
      params: { locale: locale.value },
    });
    instructors.value = response.data || response || [];
  } catch (err) {
    console.error('Error loading instructors:', err);
    instructors.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(loadInstructors);
</script>

