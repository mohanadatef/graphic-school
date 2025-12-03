<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
          {{ $t('admin.groups.edit') || 'Edit Group' }}
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          {{ $t('admin.groups.editDescription') || 'Update group information' }}
        </p>
      </div>
      <RouterLink
        to="/dashboard/admin/groups"
        class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-md hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
      >
        {{ $t('common.back') || 'Back' }}
      </RouterLink>
    </div>

    <div v-if="loading && !group" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="group" class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6">
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="label">{{ $t('admin.groups.course') || 'Course' }}</label>
            <select v-model="form.course_id" class="input" required>
              <option value="">{{ $t('common.select') || 'Select...' }}</option>
              <option v-for="course in courses" :key="course.id" :value="course.id">
                {{ course.title }}
              </option>
            </select>
          </div>

          <div>
            <label class="label">{{ $t('admin.groups.code') || 'Code' }}</label>
            <input v-model="form.code" class="input" required />
          </div>

          <div>
            <label class="label">{{ $t('admin.groups.name') || 'Name' }}</label>
            <input v-model="form.name" class="input" />
          </div>

          <div>
            <label class="label">{{ $t('admin.groups.capacity') || 'Capacity' }}</label>
            <input v-model.number="form.capacity" type="number" min="1" class="input" required />
          </div>

          <div>
            <label class="label">{{ $t('admin.groups.room') || 'Room' }}</label>
            <input v-model="form.room" class="input" />
          </div>

          <div>
            <label class="label">{{ $t('admin.groups.instructor') || 'Instructor' }}</label>
            <select v-model="form.instructor_id" class="input">
              <option value="">{{ $t('common.select') || 'Select...' }}</option>
              <option v-for="instructor in instructors" :key="instructor.id" :value="instructor.id">
                {{ instructor.name }}
              </option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="flex items-center gap-2">
              <input v-model="form.is_active" type="checkbox" class="w-4 h-4" />
              <span>{{ $t('common.active') || 'Active' }}</span>
            </label>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
          <RouterLink
            to="/dashboard/admin/groups"
            class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-md hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
          >
            {{ $t('common.cancel') || 'Cancel' }}
          </RouterLink>
          <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md" :disabled="loading">
            {{ loading ? ($t('common.saving') || 'Saving...') : ($t('common.save') || 'Save') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute, RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useI18n } from '../../../composables/useI18n';

const props = defineProps({
  id: {
    type: [String, Number],
    required: true,
  },
});

const router = useRouter();
const route = useRoute();
const { get, put } = useApi();
const toast = useToast();
const { t } = useI18n();

const loading = ref(false);
const group = ref(null);
const courses = ref([]);
const instructors = ref([]);

const form = ref({
  course_id: '',
  code: '',
  name: '',
  capacity: 20,
  room: '',
  instructor_id: '',
  is_active: true,
});

async function loadGroup() {
  try {
    loading.value = true;
    const groupId = props.id || route.params.id;
    const response = await get(`/admin/groups/${groupId}`);
    group.value = response?.data || response;
    
    // Populate form
    form.value = {
      course_id: group.value.course_id || '',
      code: group.value.code || '',
      name: group.value.name || '',
      capacity: group.value.capacity || 20,
      room: group.value.room || '',
      instructor_id: group.value.instructor_id || '',
      is_active: group.value.is_active !== undefined ? group.value.is_active : true,
    };
  } catch (err) {
    toast.error(t('errors.loadError') || 'Failed to load group');
    console.error(err);
  } finally {
    loading.value = false;
  }
}

async function loadOptions() {
  try {
    const [coursesRes, instructorsRes] = await Promise.allSettled([
      get('/admin/courses'),
      get('/admin/users?filters[role]=instructor'),
    ]);

    if (coursesRes.status === 'fulfilled') {
      const data = coursesRes.value?.data || coursesRes.value;
      courses.value = Array.isArray(data) ? data : [];
    }

    if (instructorsRes.status === 'fulfilled') {
      const data = instructorsRes.value?.data || instructorsRes.value;
      instructors.value = Array.isArray(data) ? data : [];
    }
  } catch (err) {
    console.error('Error loading options:', err);
  }
}

async function submit() {
  try {
    loading.value = true;
    const groupId = props.id || route.params.id;
    await put(`/admin/groups/${groupId}`, form.value);
    toast.success(t('admin.groups.updated') || 'Group updated successfully');
    router.push('/dashboard/admin/groups');
  } catch (err) {
    toast.error(err.response?.data?.message || t('errors.saveError') || 'Failed to update group');
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await loadOptions();
  await loadGroup();
});
</script>

<style scoped>
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
</style>
