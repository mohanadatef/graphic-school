<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <button @click="$router.back()" class="text-primary mb-2">← {{ $t('common.back') || 'Back' }}</button>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.certificates.issue') || 'Issue Certificate' }}</h2>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="enrollment" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <!-- Enrollment Info -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('admin.certificates.enrollmentInfo') || 'Enrollment Information' }}</h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.certificates.student') || 'Student' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ enrollment.student?.name }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.certificates.program') || 'Program' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ enrollment.program?.title || enrollment.program?.name }}</p>
          </div>
        </div>
      </div>

      <!-- Certificate Template Selection -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('admin.certificates.template') || 'Certificate Template' }}</label>
        <select v-model="form.template_id" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
          <option v-for="template in templates" :key="template.id" :value="template.id">{{ template.title }}</option>
        </select>
      </div>

      <!-- Preview -->
      <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-900 rounded-lg">
        <h4 class="font-medium text-slate-900 dark:text-white mb-2">{{ $t('admin.certificates.preview') || 'Preview' }}</h4>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('admin.certificates.student') || 'Student' }}: {{ enrollment.student?.name }}</p>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('admin.certificates.program') || 'Program' }}: {{ enrollment.program?.title || enrollment.program?.name }}</p>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('admin.certificates.issueDate') || 'Issue Date' }}: {{ new Date().toLocaleDateString() }}</p>
      </div>

      <!-- Actions -->
      <div class="flex gap-3">
        <button @click="issueCertificate" :disabled="issuing" class="btn-primary flex-1">
          <span v-if="issuing">{{ $t('common.loading') || 'Loading...' }}</span>
          <span v-else>{{ $t('admin.certificates.issue') || 'Issue Certificate' }}</span>
        </button>
        <button @click="$router.back()" class="btn-secondary flex-1">{{ $t('common.cancel') || 'Cancel' }}</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const loading = ref(false);
const issuing = ref(false);
const enrollment = ref(null);
const templates = ref([]);
const form = reactive({
  template_id: null,
});

async function loadData() {
  loading.value = true;
  try {
    // Load enrollment
    const enrollmentResponse = await api.get(`/admin/enrollments/${route.params.enrollmentId}`);
    enrollment.value = enrollmentResponse.data;
    
    // Load templates
    const templatesResponse = await api.get('/admin/certificate-templates');
    templates.value = templatesResponse.data || [];
    if (templates.value.length > 0) {
      form.template_id = templates.value[0].id;
    }
  } catch (error) {
    console.error('Error loading data:', error);
    toast.error('Failed to load data');
  } finally {
    loading.value = false;
  }
}

async function issueCertificate() {
  issuing.value = true;
  try {
    await api.post('/admin/certificates/issue', {
      enrollment_id: route.params.enrollmentId,
      template_id: form.template_id,
    });
    toast.success('Certificate issued successfully');
    router.push({ name: 'admin-certificates' });
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to issue certificate');
  } finally {
    issuing.value = false;
  }
}

onMounted(() => {
  loadData();
});
</script>

