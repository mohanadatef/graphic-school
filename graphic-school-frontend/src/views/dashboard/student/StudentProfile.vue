<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
        {{ $t('student.profile.title') || 'My Profile' }}
      </h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        {{ $t('student.profile.subtitle') || 'Manage your profile information' }}
      </p>
    </div>

    <!-- Stats -->
    <div v-if="profileData && profileData.stats" class="grid md:grid-cols-2 gap-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.profile.enrollments') || 'Enrollments' }}</p>
        <p class="text-3xl font-bold text-slate-900 dark:text-white">
          {{ profileData.stats.enrollments_count || 0 }}
        </p>
      </div>
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.profile.attendanceRate') || 'Attendance Rate' }}</p>
        <p class="text-3xl font-bold text-green-600 dark:text-green-400">
          {{ profileData.stats.attendance?.rate || 0 }}%
        </p>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
          {{ profileData.stats.attendance?.present || 0 }} / {{ profileData.stats.attendance?.total || 0 }}
        </p>
      </div>
    </div>

    <!-- Profile Form -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <div v-if="loading" class="text-center py-20">
        <div class="spinner-lg mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
      </div>

      <form v-else @submit.prevent="submit" class="space-y-4">
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="label">{{ $t('student.profile.name') || 'Name' }}</label>
            <input v-model="form.name" type="text" required class="input" />
          </div>
          <div>
            <label class="label">{{ $t('student.profile.email') || 'Email' }}</label>
            <input :value="form.email" disabled class="input disabled" />
          </div>
          <div>
            <label class="label">{{ $t('student.profile.phone') || 'Phone' }}</label>
            <input v-model="form.phone" type="tel" class="input" />
          </div>
          <div>
            <label class="label">{{ $t('student.profile.address') || 'Address' }}</label>
            <input v-model="form.address" type="text" class="input" />
          </div>
          <div class="md:col-span-2">
            <label class="label">{{ $t('student.profile.bio') || 'Bio' }}</label>
            <textarea v-model="form.bio" rows="3" class="input"></textarea>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button
            type="submit"
            :disabled="saving"
            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50"
          >
            {{ saving ? ($t('common.saving') || 'Saving...') : ($t('common.save') || 'Save') }}
          </button>
        </div>
        <p v-if="saved" class="text-green-600 dark:text-green-400 text-sm">
          {{ $t('student.profile.saved') || 'Profile updated successfully' }}
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useI18n } from '../../../composables/useI18n';

const { get, post } = useApi();
const toast = useToast();
const { t } = useI18n();

const profileData = ref(null);
const loading = ref(false);
const saving = ref(false);
const saved = ref(false);

const form = reactive({
  name: '',
  email: '',
  phone: '',
  address: '',
  bio: '',
});

async function loadProfile() {
  try {
    loading.value = true;
    const response = await get('/student/profile');
    profileData.value = response.data || response;
    
    if (profileData.value.user) {
      Object.assign(form, {
        name: profileData.value.user.name || '',
        email: profileData.value.user.email || '',
        phone: profileData.value.user.phone || '',
        address: profileData.value.user.address || '',
        bio: profileData.value.user.bio || '',
      });
    }
  } catch (err) {
    console.error('Error loading profile:', err);
    toast.error(t('errors.loadError') || 'Failed to load profile');
  } finally {
    loading.value = false;
  }
}

async function submit() {
  try {
    saving.value = true;
    await post('/student/profile', {
      name: form.name,
      phone: form.phone,
      address: form.address,
      bio: form.bio,
    });
    saved.value = true;
    toast.success(t('student.profile.saved') || 'Profile updated successfully');
    setTimeout(() => (saved.value = false), 2000);
    await loadProfile(); // Reload to get updated stats
  } catch (err) {
    console.error('Error saving profile:', err);
    toast.error(err.response?.data?.message || t('errors.saveError') || 'Failed to save profile');
  } finally {
    saving.value = false;
  }
}

onMounted(loadProfile);
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
.input.disabled {
  background: #f8fafc;
  color: #94a3b8;
}
.dark .input.disabled {
  background: #0f172a;
  color: #64748b;
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
