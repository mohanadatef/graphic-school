<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
        {{ $t('setup.email.title') || 'Email Configuration' }}
      </h2>
      <p class="text-slate-600 dark:text-slate-400">
        {{ $t('setup.email.description') || 'Configure SMTP settings for sending emails (optional)' }}
      </p>
    </div>

    <form @submit.prevent="handleNext" class="space-y-6">
      <!-- SMTP Host -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.email.smtpHost') || 'SMTP Host' }}
        </label>
        <input
          v-model="localData.smtp_host"
          type="text"
          class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
          placeholder="smtp.gmail.com"
        />
      </div>

      <!-- SMTP Port -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.email.smtpPort') || 'SMTP Port' }}
        </label>
        <input
          v-model="localData.smtp_port"
          type="number"
          class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
          placeholder="587"
        />
      </div>

      <!-- SMTP Username -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.email.smtpUsername') || 'SMTP Username' }}
        </label>
        <input
          v-model="localData.smtp_username"
          type="text"
          class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
          placeholder="your-email@example.com"
        />
      </div>

      <!-- SMTP Password -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.email.smtpPassword') || 'SMTP Password' }}
        </label>
        <input
          v-model="localData.smtp_password"
          type="password"
          class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
          placeholder="••••••••"
        />
      </div>

      <!-- SMTP Encryption -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.email.smtpEncryption') || 'Encryption' }}
        </label>
        <select
          v-model="localData.smtp_encryption"
          class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
        >
          <option value="tls">TLS</option>
          <option value="ssl">SSL</option>
        </select>
      </div>

      <!-- Test Email -->
      <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-600">
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.email.testEmail') || 'Test Email' }}
        </label>
        <div class="flex gap-3">
          <input
            v-model="testEmailAddress"
            type="email"
            class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="test@example.com"
          />
          <button
            type="button"
            @click="handleTestEmail"
            :disabled="testingEmail || !testEmailAddress || !localData.smtp_host"
            class="px-6 py-2 bg-secondary text-white rounded-lg hover:bg-secondary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ testingEmail ? ($t('setup.email.sending') || 'Sending...') : ($t('setup.email.sendTest') || 'Send Test') }}
          </button>
        </div>
        <p v-if="testEmailResult" class="mt-2 text-sm" :class="testEmailResult.success ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
          {{ testEmailResult.message }}
        </p>
      </div>

      <!-- Note -->
      <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
        <p class="text-sm text-blue-800 dark:text-blue-200">
          <strong>Note:</strong> Email configuration is optional. You can skip this step and configure it later from admin settings.
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
  smtp_host: props.modelValue.smtp_host || '',
  smtp_port: props.modelValue.smtp_port || '587',
  smtp_username: props.modelValue.smtp_username || '',
  smtp_password: props.modelValue.smtp_password || '',
  smtp_encryption: props.modelValue.smtp_encryption || 'tls',
});

const testEmailAddress = ref('');
const testingEmail = ref(false);
const testEmailResult = ref(null);
const loading = ref(false);

// Watch for changes and emit
watch(localData, (newVal) => {
  emit('update:modelValue', newVal);
}, { deep: true });

// Test email
async function handleTestEmail() {
  if (!testEmailAddress.value) {
    toast.error('Please enter an email address');
    return;
  }

  try {
    testingEmail.value = true;
    testEmailResult.value = null;

    await store.testEmail(testEmailAddress.value);

    testEmailResult.value = {
      success: true,
      message: 'Test email sent successfully!',
    };
    toast.success('Test email sent');
  } catch (error) {
    testEmailResult.value = {
      success: false,
      message: error.message || 'Failed to send test email',
    };
    toast.error('Failed to send test email');
  } finally {
    testingEmail.value = false;
  }
}

// Handle next
async function handleNext() {
  try {
    loading.value = true;
    
    // Update store
    Object.assign(store.formData.email, localData);
    
    // Save to backend
    await store.saveStep(4, {
      email_settings: localData,
    });

    toast.success('Email settings saved');
    emit('next');
  } catch (error) {
    toast.error('Failed to save email settings');
    console.error('Error saving step 4:', error);
  } finally {
    loading.value = false;
  }
}
</script>

