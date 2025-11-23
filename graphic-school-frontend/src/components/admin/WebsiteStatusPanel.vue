<template>
  <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-bold text-slate-900 dark:text-white">
        {{ $t('admin.websiteStatus') || 'Website Status' }}
      </h2>
      <RouterLink
        to="/setup"
        class="text-sm text-primary hover:text-primary/80 font-medium"
      >
        {{ $t('admin.editSetup') || 'Edit Setup' }} →
      </RouterLink>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Activation Status -->
      <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">{{ $t('admin.activated') || 'Activated' }}</p>
        <div class="flex items-center gap-2">
          <div
            class="w-3 h-3 rounded-full"
            :class="status?.is_activated ? 'bg-green-500' : 'bg-red-500'"
          ></div>
          <span class="font-semibold text-slate-900 dark:text-white">
            {{ status?.is_activated ? ($t('admin.yes') || 'Yes') : ($t('admin.no') || 'No') }}
          </span>
        </div>
      </div>

      <!-- Language -->
      <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">{{ $t('admin.defaultLanguage') || 'Language' }}</p>
        <p class="font-semibold text-slate-900 dark:text-white">
          {{ status?.default_language === 'ar' ? 'العربية' : 'English' }}
        </p>
      </div>

      <!-- Currency -->
      <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">{{ $t('admin.defaultCurrency') || 'Currency' }}</p>
        <p class="font-semibold text-slate-900 dark:text-white">{{ status?.default_currency || 'USD' }}</p>
      </div>

      <!-- Homepage Template -->
      <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">{{ $t('admin.homepageTemplate') || 'Homepage' }}</p>
        <p class="font-semibold text-slate-900 dark:text-white">
          {{ homepageTemplate }}
        </p>
      </div>
    </div>

    <!-- Enabled Pages -->
    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
      <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
        {{ $t('admin.enabledPages') || 'Enabled Pages' }}
      </p>
      <div class="flex flex-wrap gap-2">
        <span
          v-for="(enabled, page) in enabledPages"
          :key="page"
          class="px-3 py-1 rounded-full text-xs font-medium"
          :class="enabled
            ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
            : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'"
        >
          {{ pageLabels[page] || page }}
          <span v-if="enabled">✓</span>
          <span v-else>✗</span>
        </span>
      </div>
    </div>

    <!-- Branding Preview -->
    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
      <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
        {{ $t('admin.brandingPreview') || 'Branding Preview' }}
      </p>
      <div class="flex items-center gap-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-900">
        <div
          class="w-16 h-16 rounded-lg border-2 border-slate-200 dark:border-slate-600"
          :style="{ backgroundColor: branding?.primary_color || '#3b82f6' }"
        ></div>
        <div
          class="w-16 h-16 rounded-lg border-2 border-slate-200 dark:border-slate-600"
          :style="{ backgroundColor: branding?.secondary_color || '#6366f1' }"
        ></div>
        <div class="flex-1">
          <p class="text-sm text-slate-600 dark:text-slate-400 mb-1">Colors</p>
          <p class="text-xs font-mono text-slate-500 dark:text-slate-500">
            Primary: {{ branding?.primary_color || '#3b82f6' }} | 
            Secondary: {{ branding?.secondary_color || '#6366f1' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700 flex gap-3">
      <RouterLink
        to="/setup"
        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium"
      >
        {{ $t('admin.runSetupWizard') || 'Run Setup Wizard' }}
      </RouterLink>
      <button
        @click="handleReset"
        :disabled="resetting"
        class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors text-sm font-medium disabled:opacity-50"
      >
        {{ resetting ? ($t('admin.resetting') || 'Resetting...') : ($t('admin.resetToDefault') || 'Reset to Default') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { RouterLink } from 'vue-router';
import api from '../../api';
import { useToast } from '../../composables/useToast';
import { useWebsiteSettingsStore } from '../../stores/websiteSettings';

const props = defineProps({
  status: {
    type: Object,
    default: () => ({}),
  },
});

const toast = useToast();
const websiteStore = useWebsiteSettingsStore();

const resetting = ref(false);

const branding = computed(() => props.status?.branding || {});
const enabledPages = computed(() => props.status?.enabled_pages || {});
const homepageTemplate = computed(() => {
  // This would come from the homepage_id or template setting
  return 'Template A'; // Placeholder
});

const pageLabels = {
  home: 'Home',
  about: 'About',
  contact: 'Contact',
  programs: 'Programs',
  community: 'Community',
  faq: 'FAQ',
};

async function handleReset() {
  if (!confirm('Are you sure you want to reset the website to default? This will reset all branding and page settings.')) {
    return;
  }

  try {
    resetting.value = true;
    await api.post('/admin/setup/reset');
    toast.success('Website reset to default successfully');
    
    // Refresh status
    await websiteStore.refresh();
    
    // Reload page to see changes
    setTimeout(() => {
      window.location.reload();
    }, 1000);
  } catch (error) {
    console.error('Error resetting website:', error);
    toast.error('Failed to reset website');
  } finally {
    resetting.value = false;
  }
}
</script>

