<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-12 px-4">
    <div class="max-w-2xl mx-auto">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ $t('public.enrollment.title') || 'Enroll in Program' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">{{ $t('public.enrollment.subtitle') || 'Fill out the form below to enroll' }}</p>

        <form @submit.prevent="submitEnrollment" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('public.enrollment.program') || 'Program' }}</label>
            <select
              v-model="form.program_id"
              required
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
            >
              <option value="">{{ $t('public.enrollment.selectProgram') || 'Select Program' }}</option>
              <option v-for="program in programs" :key="program.id" :value="program.id">{{ program.title || program.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('public.enrollment.name') || 'Full Name' }}</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('public.enrollment.email') || 'Email' }}</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('public.enrollment.phone') || 'Phone' }}</label>
            <input
              v-model="form.phone"
              type="tel"
              required
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
            />
          </div>

          <div v-if="selectedProgram">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('public.enrollment.batch') || 'Batch (Optional)' }}</label>
            <select
              v-model="form.batch_id"
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
            >
              <option value="">{{ $t('public.enrollment.selectBatch') || 'Select Batch' }}</option>
              <option v-for="batch in batches" :key="batch.id" :value="batch.id">{{ batch.code }}</option>
            </select>
          </div>

          <button type="submit" :disabled="submitting" class="btn-primary w-full">
            <span v-if="submitting">{{ $t('common.submitting') || 'Submitting...' }}</span>
            <span v-else>{{ $t('public.enrollment.submit') || 'Submit Enrollment' }}</span>
          </button>
        </form>

        <div v-if="success" class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
          <p class="text-green-800 dark:text-green-400">{{ $t('public.enrollment.success') || 'Enrollment submitted successfully! We will contact you soon.' }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const submitting = ref(false);
const success = ref(false);
const programs = ref([]);
const batches = ref([]);
const form = reactive({
  program_id: null,
  name: '',
  email: '',
  phone: '',
  batch_id: null,
  group_id: null,
});

const selectedProgram = computed(() => {
  return programs.value.find(p => p.id === form.program_id);
});

watch(() => form.program_id, async (programId) => {
  if (programId) {
    try {
      const response = await api.get(`/programs/${selectedProgram.value.slug}/batches`);
      batches.value = response.data || [];
    } catch (error) {
      console.error('Error loading batches:', error);
    }
  }
});

async function loadPrograms() {
  try {
    const response = await api.get('/programs');
    programs.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading programs:', error);
    toast.error('Failed to load programs');
  }
}

async function submitEnrollment() {
  submitting.value = true;
  try {
    await api.post('/enroll', form);
    success.value = true;
    toast.success('Enrollment submitted successfully');
    // Reset form
    Object.assign(form, {
      program_id: null,
      name: '',
      email: '',
      phone: '',
      batch_id: null,
      group_id: null,
    });
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to submit enrollment');
  } finally {
    submitting.value = false;
  }
}

onMounted(() => {
  loadPrograms();
});
</script>

