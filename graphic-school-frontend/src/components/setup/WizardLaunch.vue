<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
        {{ $t('setup.launch.title') || 'Launch Your Website' }}
      </h2>
      <p class="text-slate-600 dark:text-slate-400">
        {{ $t('setup.launch.description') || 'Review your settings and launch your website' }}
      </p>
    </div>

    <!-- Summary -->
    <div class="space-y-4">
      <!-- General Info -->
      <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-600">
        <h3 class="font-semibold text-slate-900 dark:text-white mb-3">General Information</h3>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Academy Name:</span>
            <span class="font-medium text-slate-900 dark:text-white">{{ formData.general.academy_name || 'Not set' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Country:</span>
            <span class="font-medium text-slate-900 dark:text-white">{{ formData.general.country || 'Not set' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Language:</span>
            <span class="font-medium text-slate-900 dark:text-white">{{ formData.general.default_language === 'ar' ? 'العربية' : 'English' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Currency:</span>
            <span class="font-medium text-slate-900 dark:text-white">{{ formData.general.default_currency }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Timezone:</span>
            <span class="font-medium text-slate-900 dark:text-white">{{ formData.general.timezone }}</span>
          </div>
        </div>
      </div>

      <!-- Branding -->
      <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-600">
        <h3 class="font-semibold text-slate-900 dark:text-white mb-3">Branding</h3>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Primary Color:</span>
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 rounded border border-slate-300 dark:border-slate-600" :style="{ backgroundColor: formData.branding.primary_color }"></div>
              <span class="font-medium text-slate-900 dark:text-white">{{ formData.branding.primary_color }}</span>
            </div>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Fonts:</span>
            <span class="font-medium text-slate-900 dark:text-white">{{ formData.branding.font_main }} / {{ formData.branding.font_headings }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Theme:</span>
            <span class="font-medium text-slate-900 dark:text-white capitalize">{{ formData.branding.default_theme }}</span>
          </div>
        </div>
      </div>

      <!-- Pages -->
      <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-600">
        <h3 class="font-semibold text-slate-900 dark:text-white mb-3">Pages</h3>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Homepage Template:</span>
            <span class="font-medium text-slate-900 dark:text-white">{{ formData.pages.homepage_template === 'template-a' ? 'Template A' : 'Template B' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Enabled Pages:</span>
            <span class="font-medium text-slate-900 dark:text-white">
              {{ Object.entries(formData.pages.enabled_pages).filter(([_, enabled]) => enabled).map(([key]) => key).join(', ') }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-col gap-3 pt-4">
      <button
        @click="handleComplete"
        :disabled="loading"
        class="w-full px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-semibold text-lg"
      >
        {{ loading ? ($t('setup.launch.launching') || 'Launching...') : ($t('setup.launch.launchWebsite') || '🚀 Launch Website') }}
      </button>
      
      <button
        @click="handleActivateDefault"
        :disabled="loading"
        class="w-full px-6 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
      >
        {{ $t('setup.launch.activateDefault') || 'Activate Default Website Instead' }}
      </button>

      <button
        @click="$emit('back')"
        :disabled="loading"
        class="w-full px-6 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
      >
        {{ $t('setup.back') || 'Back' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useSetupWizardStore } from '../../stores/setupWizard';
import { useToast } from '../../composables/useToast';

const props = defineProps({
  formData: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['complete', 'back']);

const router = useRouter();
const store = useSetupWizardStore();
const toast = useToast();

const loading = ref(false);

// Complete setup
async function handleComplete() {
  try {
    loading.value = true;
    
    await store.completeSetup();
    
    toast.success('Website launched successfully!');
    
    // Redirect to homepage
    setTimeout(() => {
      router.push('/');
    }, 1000);
  } catch (error) {
    toast.error('Failed to launch website');
    console.error('Error completing setup:', error);
  } finally {
    loading.value = false;
  }
}

// Activate default
async function handleActivateDefault() {
  try {
    loading.value = true;
    
    await store.activateDefault();
    
    toast.success('Default website activated successfully!');
    
    // Redirect to homepage
    setTimeout(() => {
      router.push('/');
    }, 1000);
  } catch (error) {
    toast.error('Failed to activate default website');
    console.error('Error activating default:', error);
  } finally {
    loading.value = false;
  }
}
</script>

