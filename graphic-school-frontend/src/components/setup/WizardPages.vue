<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
        {{ $t('setup.pages.title') || 'Website Pages' }}
      </h2>
      <p class="text-slate-600 dark:text-slate-400">
        {{ $t('setup.pages.description') || 'Choose your homepage template and enable pages' }}
      </p>
    </div>

    <form @submit.prevent="handleNext" class="space-y-6">
      <!-- Homepage Template -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-4">
          {{ $t('setup.pages.homepageTemplate') || 'Homepage Template' }}
        </label>
        <div class="grid md:grid-cols-2 gap-4">
          <label
            class="relative cursor-pointer border-2 rounded-lg p-4 transition-all"
            :class="localData.homepage_template === 'template-a' ? 'border-primary bg-primary/5' : 'border-slate-200 dark:border-slate-600 hover:border-primary/50'"
          >
            <input
              v-model="localData.homepage_template"
              type="radio"
              value="template-a"
              class="sr-only"
            />
            <div class="flex items-center gap-3 mb-2">
              <div
                class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                :class="localData.homepage_template === 'template-a' ? 'border-primary bg-primary' : 'border-slate-300 dark:border-slate-600'"
              >
                <div
                  v-if="localData.homepage_template === 'template-a'"
                  class="w-3 h-3 rounded-full bg-white"
                ></div>
              </div>
              <span class="font-semibold text-slate-900 dark:text-white">Template A</span>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400">
              Simple and clean design with hero section and features
            </p>
          </label>

          <label
            class="relative cursor-pointer border-2 rounded-lg p-4 transition-all"
            :class="localData.homepage_template === 'template-b' ? 'border-primary bg-primary/5' : 'border-slate-200 dark:border-slate-600 hover:border-primary/50'"
          >
            <input
              v-model="localData.homepage_template"
              type="radio"
              value="template-b"
              class="sr-only"
            />
            <div class="flex items-center gap-3 mb-2">
              <div
                class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                :class="localData.homepage_template === 'template-b' ? 'border-primary bg-primary' : 'border-slate-300 dark:border-slate-600'"
              >
                <div
                  v-if="localData.homepage_template === 'template-b'"
                  class="w-3 h-3 rounded-full bg-white"
                ></div>
              </div>
              <span class="font-semibold text-slate-900 dark:text-white">Template B</span>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400">
              Advanced design with programs, testimonials, and more sections
            </p>
          </label>
        </div>
      </div>

      <!-- Enabled Pages -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-4">
          {{ $t('setup.pages.enabledPages') || 'Enabled Pages' }}
        </label>
        <div class="space-y-3">
          <label
            v-for="page in pages"
            :key="page.key"
            class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer"
          >
            <input
              v-model="localData.enabled_pages[page.key]"
              type="checkbox"
              class="w-5 h-5 text-primary rounded focus:ring-primary"
            />
            <div class="flex-1">
              <span class="font-medium text-slate-900 dark:text-white">{{ page.label }}</span>
              <p class="text-sm text-slate-500 dark:text-slate-400">{{ page.description }}</p>
            </div>
          </label>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-between pt-4">
        <button
          type="button"
          @click="$emit('back')"
          class="px-6 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
        >
          {{ $t('setup.back') || 'Back' }}
        </button>
        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          {{ loading ? ($t('setup.saving') || 'Saving...') : ($t('setup.next') || 'Next') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import { useSetupWizardStore } from '../../stores/setupWizard';
import { useToast } from '../../composables/useToast';

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['update:modelValue', 'next', 'back']);

const store = useSetupWizardStore();
const toast = useToast();

const localData = reactive({
  homepage_template: props.modelValue.homepage_template || 'template-a',
  enabled_pages: {
    home: true,
    about: props.modelValue.enabled_pages?.about ?? true,
    contact: props.modelValue.enabled_pages?.contact ?? true,
    programs: props.modelValue.enabled_pages?.programs ?? true,
    community: props.modelValue.enabled_pages?.community ?? true,
    faq: props.modelValue.enabled_pages?.faq ?? false,
  },
});

const loading = ref(false);

const pages = [
  {
    key: 'about',
    label: 'About Page',
    description: 'Tell visitors about your academy',
  },
  {
    key: 'contact',
    label: 'Contact Page',
    description: 'Allow visitors to get in touch',
  },
  {
    key: 'programs',
    label: 'Programs Page',
    description: 'Showcase your programs and courses',
  },
  {
    key: 'community',
    label: 'Community Page',
    description: 'Enable community features and discussions',
  },
  {
    key: 'faq',
    label: 'FAQ Page',
    description: 'Answer frequently asked questions',
  },
];

// Watch for changes and emit
watch(localData, (newVal) => {
  emit('update:modelValue', newVal);
}, { deep: true });

// Handle next
async function handleNext() {
  try {
    loading.value = true;
    
    // Update store
    Object.assign(store.formData.pages, localData);
    
    // Save to backend
    await store.saveStep(3, {
      enabled_pages: localData.enabled_pages,
      homepage_template: localData.homepage_template,
    });

    toast.success('Page settings saved');
    emit('next');
  } catch (error) {
    toast.error('Failed to save page settings');
    console.error('Error saving step 3:', error);
  } finally {
    loading.value = false;
  }
}
</script>

