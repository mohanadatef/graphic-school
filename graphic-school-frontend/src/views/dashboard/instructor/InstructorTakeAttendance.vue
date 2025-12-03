<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
          {{ $t('instructor.takeAttendance.title') || 'Take Attendance' }}
        </h2>
        <p v-if="sessionData" class="text-sm text-slate-500 dark:text-slate-400">
          {{ sessionData.session.title }} - {{ formatDate(sessionData.session.session_date) }}
        </p>
      </div>
      <RouterLink
        :to="`/dashboard/instructor/groups/${sessionData?.session?.group?.id}/sessions`"
        class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
      >
        ← {{ $t('common.back') || 'Back' }}
      </RouterLink>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="!sessionData || !sessionData.attendance" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">
        {{ $t('instructor.takeAttendance.noData') || 'No data available' }}
      </p>
    </div>

    <form v-else @submit.prevent="submitAttendance" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 space-y-4">
      <div class="space-y-3">
        <div
          v-for="item in sessionData.attendance"
          :key="item.student_id"
          class="flex items-center gap-4 p-4 border border-slate-200 dark:border-slate-700 rounded-lg"
        >
          <div v-if="item.student.avatar_path" class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0">
            <img :src="item.student.avatar_path" :alt="item.student.name" class="w-full h-full object-cover" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-medium text-slate-900 dark:text-white">{{ item.student.name }}</p>
            <p v-if="item.student.email" class="text-xs text-slate-500 dark:text-slate-400 truncate">
              {{ item.student.email }}
            </p>
          </div>
          <div class="flex gap-2">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                type="radio"
                :name="`attendance_${item.student_id}`"
                :value="'present'"
                v-model="attendanceForm[item.student_id].status"
                class="w-4 h-4"
              />
              <span class="text-sm text-green-600 dark:text-green-400">{{ $t('instructor.takeAttendance.present') || 'Present' }}</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                type="radio"
                :name="`attendance_${item.student_id}`"
                :value="'absent'"
                v-model="attendanceForm[item.student_id].status"
                class="w-4 h-4"
              />
              <span class="text-sm text-red-600 dark:text-red-400">{{ $t('instructor.takeAttendance.absent') || 'Absent' }}</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                type="radio"
                :name="`attendance_${item.student_id}`"
                :value="'late'"
                v-model="attendanceForm[item.student_id].status"
                class="w-4 h-4"
              />
              <span class="text-sm text-yellow-600 dark:text-yellow-400">{{ $t('instructor.takeAttendance.late') || 'Late' }}</span>
            </label>
          </div>
          <input
            v-model="attendanceForm[item.student_id].note"
            type="text"
            :placeholder="$t('instructor.takeAttendance.note') || 'Note (optional)'"
            class="text-sm px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white w-40"
          />
        </div>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
        <button
          type="button"
          @click="$router.back()"
          class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
        >
          {{ $t('common.cancel') || 'Cancel' }}
        </button>
        <button
          type="submit"
          :disabled="saving"
          class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50"
        >
          {{ saving ? ($t('common.saving') || 'Saving...') : ($t('common.save') || 'Save Attendance') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useI18n } from '../../../composables/useI18n';

const route = useRoute();
const { get, post } = useApi();
const toast = useToast();
const { t } = useI18n();

const sessionId = parseInt(route.params.sessionId);
const sessionData = ref(null);
const loading = ref(false);
const saving = ref(false);
const attendanceForm = reactive({});

async function loadSessionAttendance() {
  try {
    loading.value = true;
    const response = await get(`/instructor/sessions/${sessionId}/attendance`);
    sessionData.value = response.data || response;

    // Initialize form
    if (sessionData.value.attendance) {
      sessionData.value.attendance.forEach(item => {
        attendanceForm[item.student_id] = {
          status: item.attendance?.status || 'present',
          note: item.attendance?.note || '',
        };
      });
    }
  } catch (err) {
    console.error('Error loading session attendance:', err);
    toast.error(t('errors.loadError') || 'Failed to load attendance data');
  } finally {
    loading.value = false;
  }
}

async function submitAttendance() {
  try {
    saving.value = true;

    // Build attendance array
    const attendance = Object.keys(attendanceForm).map(studentId => ({
      student_id: parseInt(studentId),
      status: attendanceForm[studentId].status,
      note: attendanceForm[studentId].note || null,
    }));

    await post(`/instructor/sessions/${sessionId}/attendance`, { attendance });
    toast.success(t('instructor.takeAttendance.saved') || 'Attendance saved successfully');
    
    // Reload to show updated data
    await loadSessionAttendance();
  } catch (err) {
    console.error('Error saving attendance:', err);
    toast.error(err.response?.data?.message || t('errors.saveError') || 'Failed to save attendance');
  } finally {
    saving.value = false;
  }
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

onMounted(loadSessionAttendance);
</script>

