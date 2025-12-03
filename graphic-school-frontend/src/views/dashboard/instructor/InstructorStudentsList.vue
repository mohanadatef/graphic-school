<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
          {{ $t('instructor.studentsList.title') || 'Students List' }}
        </h2>
        <p v-if="groupData" class="text-sm text-slate-500 dark:text-slate-400">
          {{ groupData.group.name || groupData.group.code }} - {{ groupData.group.course?.title }}
        </p>
      </div>
      <RouterLink
        :to="`/dashboard/instructor/groups/${groupId}`"
        class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
      >
        ← {{ $t('common.back') || 'Back' }}
      </RouterLink>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="!groupData || !groupData.students || groupData.students.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">
        {{ $t('instructor.studentsList.noStudents') || 'No students enrolled in this group' }}
      </p>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-900 text-xs uppercase">
            <tr>
              <th class="px-4 py-3 text-left">{{ $t('instructor.studentsList.name') || 'Name' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('instructor.studentsList.email') || 'Email' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('instructor.studentsList.phone') || 'Phone' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('instructor.studentsList.enrollmentStatus') || 'Enrollment Status' }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="student in groupData.students"
              :key="student.id"
              class="border-t border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50"
            >
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div v-if="student.avatar_path" class="w-10 h-10 rounded-full overflow-hidden">
                    <img :src="student.avatar_path" :alt="student.name" class="w-full h-full object-cover" />
                  </div>
                  <span class="font-medium text-slate-900 dark:text-white">{{ student.name }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                {{ student.email }}
              </td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                {{ student.phone || '-' }}
              </td>
              <td class="px-4 py-3">
                <span
                  :class="{
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': student.enrollment_status === 'approved',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': student.enrollment_status === 'pending',
                  }"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ student.enrollment_status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Summary -->
    <div v-if="groupData && groupData.students" class="grid md:grid-cols-3 gap-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('instructor.studentsList.totalStudents') || 'Total Students' }}</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ groupData.students.length }}</p>
      </div>
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('instructor.studentsList.approved') || 'Approved' }}</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
          {{ groupData.students.filter(s => s.enrollment_status === 'approved').length }}
        </p>
      </div>
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('instructor.studentsList.pending') || 'Pending' }}</p>
        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
          {{ groupData.students.filter(s => s.enrollment_status === 'pending').length }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useI18n } from '../../../composables/useI18n';

const route = useRoute();
const { get } = useApi();
const { t } = useI18n();

const groupId = computed(() => parseInt(route.params.groupId));
const groupData = ref(null);
const loading = ref(false);

async function loadStudents() {
  try {
    loading.value = true;
    const response = await get(`/instructor/groups/${groupId.value}/students`);
    groupData.value = response.data || response;
  } catch (err) {
    console.error('Error loading students:', err);
    groupData.value = null;
  } finally {
    loading.value = false;
  }
}

onMounted(loadStudents);
</script>

