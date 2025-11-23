<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <button @click="$router.back()" class="text-primary mb-2">← {{ $t('common.back') || 'Back' }}</button>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('instructor.assignments.create') || 'Create Assignment' }}</h2>
      </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <form @submit.prevent="submitAssignment" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('instructor.assignments.program') || 'Program' }} *</label>
          <select v-model="form.program_id" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white" @change="loadBatches">
            <option value="">{{ $t('instructor.assignments.selectProgram') || 'Select Program' }}</option>
            <option v-for="program in programs" :key="program.id" :value="program.id">{{ program.title || program.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('instructor.assignments.group') || 'Group' }}</label>
          <select v-model="form.group_id" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
            <option value="">{{ $t('instructor.assignments.selectGroup') || 'Select Group (Optional)' }}</option>
            <option v-for="group in groups" :key="group.id" :value="group.id">{{ group.code || group.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('instructor.assignments.title') || 'Title' }} *</label>
          <input v-model="form.title" type="text" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('instructor.assignments.description') || 'Description' }}</label>
          <textarea v-model="form.description" rows="4" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('instructor.assignments.dueDate') || 'Due Date' }} *</label>
          <input v-model="form.due_date" type="datetime-local" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('instructor.assignments.maxGrade') || 'Max Grade' }} *</label>
          <input v-model.number="form.max_grade" type="number" min="0" max="100" step="0.01" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('instructor.assignments.attachments') || 'Attachments' }}</label>
          <input type="file" multiple @change="handleFileChange" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white" />
        </div>

        <div class="flex gap-3">
          <button type="submit" :disabled="submitting" class="btn-primary flex-1">
            <span v-if="submitting">{{ $t('common.submitting') || 'Submitting...' }}</span>
            <span v-else>{{ $t('common.create') || 'Create' }}</span>
          </button>
          <button type="button" @click="$router.back()" class="btn-secondary flex-1">{{ $t('common.cancel') || 'Cancel' }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const router = useRouter();
const toast = useToast();
const submitting = ref(false);
const programs = ref([]);
const groups = ref([]);
const form = reactive({
  program_id: null,
  batch_id: null,
  group_id: null,
  session_id: null,
  title: '',
  description: '',
  due_date: '',
  max_grade: 100,
});
const attachments = ref([]);

async function loadPrograms() {
  try {
    const response = await api.get('/programs');
    programs.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading programs:', error);
  }
}

async function loadBatches() {
  if (!form.program_id) return;
  try {
    const program = programs.value.find(p => p.id === form.program_id);
    if (program) {
      const response = await api.get(`/programs/${program.slug}/batches`);
      const batches = response.data || [];
      groups.value = [];
      batches.forEach(batch => {
        if (batch.groups) {
          groups.value.push(...batch.groups);
        }
      });
    }
  } catch (error) {
    console.error('Error loading groups:', error);
  }
}

function handleFileChange(event) {
  attachments.value = Array.from(event.target.files);
}

async function submitAssignment() {
  submitting.value = true;
  try {
    const formData = new FormData();
    Object.keys(form).forEach(key => {
      if (form[key] !== null && form[key] !== '') {
        formData.append(key, form[key]);
      }
    });
    
    attachments.value.forEach(file => {
      formData.append('attachments[]', file);
    });

    await api.post('/instructor/assignments', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    
    toast.success('Assignment created successfully');
    router.push({ name: 'instructor-assignments' });
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to create assignment');
  } finally {
    submitting.value = false;
  }
}

onMounted(() => {
  loadPrograms();
});
</script>

