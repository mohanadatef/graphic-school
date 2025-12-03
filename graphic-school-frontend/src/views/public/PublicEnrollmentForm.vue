<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-12 px-4">
    <div class="max-w-2xl mx-auto">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
          {{ $t('public.enrollment.title') || 'Enroll in Course' }}
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
          {{ $t('public.enrollment.subtitle') || 'Fill out the form below to enroll' }}
        </p>

        <form @submit.prevent="submitEnrollment" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('public.enrollment.course') || 'Course' }}
            </label>
            <select
              v-model="form.course_id"
              required
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
            >
              <option value="">{{ $t('public.enrollment.selectCourse') || 'Select Course' }}</option>
              <option v-for="course in courses" :key="course.id" :value="course.id">
                {{ course.title }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('public.enrollment.name') || 'Full Name' }}
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('public.enrollment.email') || 'Email' }}
            </label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('public.enrollment.phone') || 'Phone' }}
            </label>
            <input
              v-model="form.phone"
              type="tel"
              required
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('public.enrollment.message') || 'Message (Optional)' }}
            </label>
            <textarea
              v-model="form.message"
              rows="4"
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
            ></textarea>
          </div>

          <button type="submit" :disabled="submitting" class="btn-primary w-full">
            <span v-if="submitting">{{ $t('common.submitting') || 'Submitting...' }}</span>
            <span v-else>{{ $t('public.enrollment.submit') || 'Submit Enrollment' }}</span>
          </button>
        </form>

        <div v-if="success" class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
          <p class="text-green-800 dark:text-green-400">
            {{ $t('public.enrollment.success') || 'Enrollment submitted successfully! We will contact you soon.' }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useApi } from '../../composables/useApi';
import { useToast } from '../../composables/useToast';

const { get, post } = useApi();
const toast = useToast();
const submitting = ref(false);
const success = ref(false);
const courses = ref([]);
const form = reactive({
  course_id: null,
  name: '',
  email: '',
  phone: '',
  message: '',
});

async function loadCourses() {
  try {
    const { data } = await get('/public/courses');
    courses.value = data.data || data || [];
  } catch (error) {
    console.error('Error loading courses:', error);
    toast.error('Failed to load courses');
  }
}

async function submitEnrollment() {
  submitting.value = true;
  try {
    await post('/enroll', form);
    success.value = true;
    toast.success('Enrollment submitted successfully');
    // Reset form
    Object.assign(form, {
      course_id: null,
      name: '',
      email: '',
      phone: '',
      message: '',
    });
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to submit enrollment');
  } finally {
    submitting.value = false;
  }
}

onMounted(() => {
  loadCourses();
});
</script>
