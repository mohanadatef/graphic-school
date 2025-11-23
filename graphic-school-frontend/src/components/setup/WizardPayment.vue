<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
        {{ $t('setup.payment.title') || 'Payment Configuration' }}
      </h2>
      <p class="text-slate-600 dark:text-slate-400">
        {{ $t('setup.payment.description') || 'Configure payment gateways (optional)' }}
      </p>
    </div>

    <form @submit.prevent="handleNext" class="space-y-6">
      <!-- Stripe -->
      <div class="p-4 border-2 border-slate-200 dark:border-slate-600 rounded-lg">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218 3.757 9.361 4.532 11.345 5.928 12.786c1.386 1.411 3.372 2.563 6.446 3.518 2.172.806 3.356 1.426 3.356 2.409 0 .98-.84 1.545-2.129 1.545-1.584 0-4.354-.859-6.305-1.87l-.89 5.494C6.933 23.025 9.667 24 12.165 24c2.5 0 4.578-.654 6.062-1.872 1.546-1.275 2.349-3.12 2.349-5.346 0-2.143-.775-4.127-2.171-5.568-1.386-1.411-3.372-2.563-6.429-3.524z"/>
          </svg>
          Stripe
        </h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('setup.payment.stripeKey') || 'Publishable Key' }}
            </label>
            <input
              v-model="localData.stripe_key"
              type="text"
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
              placeholder="pk_test_..."
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('setup.payment.stripeSecret') || 'Secret Key' }}
            </label>
            <input
              v-model="localData.stripe_secret"
              type="password"
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
              placeholder="sk_test_..."
            />
          </div>
        </div>
      </div>

      <!-- Paymob -->
      <div class="p-4 border-2 border-slate-200 dark:border-slate-600 rounded-lg">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Paymob</h3>
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            {{ $t('setup.payment.paymobKey') || 'API Key' }}
          </label>
          <input
            v-model="localData.paymob_api_key"
            type="text"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Enter Paymob API key"
          />
        </div>
      </div>

      <!-- Paytabs -->
      <div class="p-4 border-2 border-slate-200 dark:border-slate-600 rounded-lg">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Paytabs</h3>
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            {{ $t('setup.payment.paytabsSecret') || 'Secret Key' }}
          </label>
          <input
            v-model="localData.paytabs_secret"
            type="password"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Enter Paytabs secret key"
          />
        </div>
      </div>

      <!-- Note -->
      <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
        <p class="text-sm text-blue-800 dark:text-blue-200">
          <strong>Note:</strong> Payment configuration is optional. You can skip this step and configure payment gateways later from admin settings.
        </p>
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
  stripe_key: props.modelValue.stripe_key || '',
  stripe_secret: props.modelValue.stripe_secret || '',
  paymob_api_key: props.modelValue.paymob_api_key || '',
  paytabs_secret: props.modelValue.paytabs_secret || '',
});

const loading = ref(false);

// Watch for changes and emit
watch(localData, (newVal) => {
  emit('update:modelValue', newVal);
}, { deep: true });

// Handle next
async function handleNext() {
  try {
    loading.value = true;
    
    // Update store
    Object.assign(store.formData.payment, localData);
    
    // Save to backend
    await store.saveStep(5, {
      payment_settings: localData,
    });

    toast.success('Payment settings saved');
    emit('next');
  } catch (error) {
    toast.error('Failed to save payment settings');
    console.error('Error saving step 5:', error);
  } finally {
    loading.value = false;
  }
}
</script>

