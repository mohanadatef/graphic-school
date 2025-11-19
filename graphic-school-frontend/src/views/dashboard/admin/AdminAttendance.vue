<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4 flex-wrap">
      <div>
        <h2 class="text-2xl font-bold">سجلات الحضور</h2>
        <p class="text-sm text-slate-500">مراجعة الحضور لكل جلسة.</p>
      </div>
      <input v-model="filters.courseId" placeholder="ID الكورس" class="input w-32" />
      <input v-model="filters.sessionId" placeholder="ID الجلسة" class="input w-32" />
      <button class="px-4 py-2 border rounded-md" @click="loadAttendance">تصفية</button>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-left">الطالب</th>
            <th class="px-4 py-3 text-left">الكورس</th>
            <th class="px-4 py-3 text-left">الجلسة</th>
            <th class="px-4 py-3 text-left">التاريخ</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3 text-left">ملاحظة</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="record in attendance" :key="record.id" class="border-t border-slate-100">
            <td class="px-4 py-3">{{ record.student?.name }}</td>
            <td class="px-4 py-3">{{ record.session?.course?.title }}</td>
            <td class="px-4 py-3">{{ record.session?.title }}</td>
            <td class="px-4 py-3">{{ formatDate(record.session?.session_date) }}</td>
            <td class="px-4 py-3">
              <span
                class="px-2 py-1 rounded-full text-xs"
                :class="record.status === 'present' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
              >
                {{ record.status === 'present' ? 'حاضر' : 'غائب' }}
              </span>
            </td>
            <td class="px-4 py-3 text-xs text-slate-500">{{ record.note }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue';
import api from '../../../api';

const attendance = ref([]);
const filters = reactive({
  courseId: '',
  sessionId: '',
});

async function loadAttendance() {
  const { data } = await api.get('/admin/attendance', {
    params: {
      course_id: filters.courseId || undefined,
      session_id: filters.sessionId || undefined,
    },
  });
  attendance.value = data.data || data;
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ar-EG');
}

onMounted(loadAttendance);
</script>

<style scoped>
.input {
  border: 1px solid #e2e8f0;
  border-radius: 999px;
  padding: 0.4rem 0.9rem;
  font-size: 0.85rem;
}
</style>

