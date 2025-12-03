<template>
  <div class="space-y-8">
    <!-- CMS Content -->
    <CMSPageRenderer slug="contact" />

    <!-- Contact Form -->
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">
          {{ $t('contact.sendMessage') || 'Send us a message' }}
        </h2>
        <form @submit.prevent="submitForm" class="space-y-4">
          <div>
            <label class="label">{{ $t('contact.name') || 'Name' }} <span class="text-red-500">*</span></label>
            <input
              v-model="form.name"
              type="text"
              required
              class="input"
            />
          </div>
          <div>
            <label class="label">{{ $t('contact.email') || 'Email' }} <span class="text-red-500">*</span></label>
            <input
              v-model="form.email"
              type="email"
              required
              class="input"
            />
          </div>
          <div>
            <label class="label">{{ $t('contact.subject') || 'Subject' }}</label>
            <input
              v-model="form.subject"
              type="text"
              class="input"
            />
          </div>
          <div>
            <label class="label">{{ $t('contact.message') || 'Message' }} <span class="text-red-500">*</span></label>
            <textarea
              v-model="form.message"
              rows="5"
              required
              class="input"
            ></textarea>
          </div>
          <button
            type="submit"
            :disabled="saving"
            class="w-full px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-semibold disabled:opacity-50"
          >
            {{ saving ? ($t('common.sending') || 'Sending...') : ($t('contact.send') || 'Send Message') }}
          </button>
          <p v-if="success" class="text-green-600 dark:text-green-400 text-sm text-center">
            {{ $t('contact.sent') || 'Message sent successfully!' }}
          </p>
          <p v-if="error" class="text-red-600 dark:text-red-400 text-sm text-center">
            {{ error }}
          </p>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useApi } from '../../composables/useApi';
import { useToast } from '../../composables/useToast';
import { useI18n } from '../../composables/useI18n';
import CMSPageRenderer from '../../components/public/CMSPageRenderer.vue';

const { post } = useApi();
const toast = useToast();
const { t } = useI18n();

const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: '',
});

const saving = ref(false);
const success = ref(false);
const error = ref(null);

async function submitForm() {
  try {
    saving.value = true;
    error.value = null;
    success.value = false;

    await post('/public/contact', form);
    
    success.value = true;
    toast.success(t('contact.sent') || 'Message sent successfully!');
    
    // Reset form
    Object.assign(form, {
      name: '',
      email: '',
      subject: '',
      message: '',
    });
  } catch (err) {
    error.value = err.response?.data?.message || t('errors.sendError') || 'Failed to send message';
    toast.error(error.value);
  } finally {
    saving.value = false;
  }
}
</script>

<style scoped>
.label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.5rem;
}
.dark .label {
  color: #cbd5e1;
}
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 0.65rem 0.9rem;
  font-size: 0.95rem;
  background: white;
}
.dark .input {
  background: #1e293b;
  border-color: #334155;
  color: #f1f5f9;
}
</style>
