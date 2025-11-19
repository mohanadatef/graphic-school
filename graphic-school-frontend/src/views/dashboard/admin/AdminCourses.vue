<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">الكورسات</h2>
        <p class="text-sm text-slate-500">إنشاء كورسات جديدة وتعيين المدربين والتحكم في الجلسات.</p>
      </div>
      <button class="px-4 py-2 bg-primary text-white rounded-md" @click="openModal()">كورس جديد</button>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow p-4 space-y-3">
      <div class="flex flex-wrap gap-3">
        <input v-model="filtersState.search" class="input w-48" placeholder="بحث بالعنوان" />
        <select v-model="filtersState.status" class="input w-40">
          <option value="">كل الحالات</option>
          <option value="draft">مسودة</option>
          <option value="upcoming">قادمة</option>
          <option value="running">قيد التنفيذ</option>
          <option value="completed">منتهية</option>
        </select>
        <select v-model.number="filtersState.per_page" class="input w-32" @change="changePerPage(filtersState.per_page)">
          <option :value="10">10</option>
          <option :value="20">20</option>
          <option :value="50">50</option>
        </select>
        <button class="px-4 py-2 border rounded-md" @click="loadCourses">تحديث</button>
      </div>
    </div>

    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">العنوان</th>
            <th class="px-4 py-3 text-left">التصنيف</th>
            <th class="px-4 py-3 text-left">البداية</th>
            <th class="px-4 py-3 text-left">الوقت</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3 text-left">المدربين</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="course in courses" :key="course.id" class="border-t border-slate-100">
            <td class="px-4 py-3 font-medium text-slate-900">{{ course.title }}</td>
            <td class="px-4 py-3">{{ course.category?.name }}</td>
            <td class="px-4 py-3">{{ formatDate(course.start_date) }}</td>
            <td class="px-4 py-3 text-xs">
              {{ formatTime(course.default_start_time) }} - {{ formatTime(course.default_end_time) }}
            </td>
            <td class="px-4 py-3 text-xs uppercase">{{ course.status }}</td>
            <td class="px-4 py-3 text-xs">
              <span
                v-for="inst in course.instructors"
                :key="inst.id"
                class="inline-block bg-slate-100 px-2 py-1 rounded-full mr-1 mb-1"
              >
                {{ inst.name }}
              </span>
            </td>
            <td class="px-4 py-3 text-right text-xs">
              <button class="text-primary mr-2" @click="openModal(course)">تعديل</button>
              <button class="text-red-500" @click="remove(course.id)">حذف</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!courses.length" class="text-center py-6 text-sm text-slate-400">لا توجد بيانات.</p>
    </div>

    <PaginationControls
      v-if="pagination.total"
      :meta="pagination"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />

    <dialog ref="dialogRef" class="rounded-2xl p-0 w-full max-w-2xl">
      <form class="p-6 space-y-4 max-h-[90vh] overflow-y-auto" @submit.prevent="submit">
        <h3 class="text-xl font-semibold text-slate-900">{{ form.id ? 'تعديل كورس' : 'كورس جديد' }}</h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="label">العنوان</label>
            <input v-model="form.title" required class="input" />
          </div>
          <div>
            <label class="label">التصنيف</label>
            <select v-model="form.category_id" class="input" required>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="label">السعر</label>
            <input v-model.number="form.price" type="number" class="input" />
          </div>
          <div>
            <label class="label">تاريخ البداية</label>
            <input v-model="form.start_date" type="date" class="input" />
          </div>
          <div>
            <label class="label">وقت البداية</label>
            <input v-model="form.default_start_time" type="time" class="input" />
          </div>
          <div>
            <label class="label">وقت النهاية</label>
            <input v-model="form.default_end_time" type="time" class="input" />
          </div>
          <div>
            <label class="label">عدد المحاضرات</label>
            <input v-model.number="form.session_count" type="number" min="1" class="input" />
          </div>
          <div>
            <label class="label">الأيام</label>
            <select v-model="form.days_of_week" multiple class="input h-32">
              <option v-for="(label, key) in days" :key="key" :value="key">{{ label }}</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="label">الوصف</label>
            <textarea v-model="form.description" rows="3" class="input"></textarea>
          </div>
          <div class="md:col-span-2">
            <label class="label">المدربون</label>
            <select v-model="form.instructors" multiple class="input h-32">
              <option v-for="instructor in instructors" :key="instructor.id" :value="instructor.id">
                {{ instructor.name }}
              </option>
            </select>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" class="px-4 py-2 border rounded-md" @click="closeModal">إلغاء</button>
          <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md">حفظ</button>
        </div>
      </form>
    </dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import api from '../../../api';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const courses = ref([]);
const categories = ref([]);
const instructors = ref([]);
const dialogRef = ref(null);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

const filtersState = reactive({
  page: 1,
  per_page: 10,
  status: '',
  search: '',
});

const form = reactive({
  id: null,
  title: '',
  category_id: null,
  description: '',
  price: 0,
  start_date: '',
  default_start_time: '',
  default_end_time: '',
  session_count: 8,
  days_of_week: [],
  instructors: [],
});

const days = {
  mon: 'الاثنين',
  tue: 'الثلاثاء',
  wed: 'الأربعاء',
  thu: 'الخميس',
  fri: 'الجمعة',
  sat: 'السبت',
  sun: 'الأحد',
};

async function loadOptions() {
  const [{ data: categoryData }, { data: instructorData }] = await Promise.all([
    api.get('/admin/categories'),
    api.get('/instructors'),
  ]);
  categories.value = categoryData;
  instructors.value = instructorData;
}

async function loadCourses() {
  const { data } = await api.get('/admin/courses', {
    params: {
      page: filtersState.page,
      per_page: filtersState.per_page,
      status: filtersState.status || undefined,
      search: filtersState.search || undefined,
    },
  });

  courses.value = data.data;
  Object.assign(pagination, {
    current_page: data.meta.current_page,
    last_page: data.meta.last_page,
    per_page: data.meta.per_page,
    total: data.meta.total,
  });
}

function openModal(course) {
  form.id = course?.id || null;
  form.title = course?.title || '';
  form.category_id = course?.category_id || categories.value[0]?.id || null;
  form.description = course?.description || '';
  form.price = course?.price || 0;
  form.start_date = course?.start_date || '';
  form.default_start_time = course?.default_start_time || '';
  form.default_end_time = course?.default_end_time || '';
  form.session_count = course?.session_count || 8;
  form.days_of_week = course?.days_of_week ? [...course.days_of_week] : [];
  form.instructors = course?.instructors?.map((inst) => inst.id) || [];
  dialogRef.value.showModal();
}

function closeModal() {
  dialogRef.value.close();
}

async function submit() {
  const payload = {
    ...form,
    start_date: form.start_date || null,
    default_start_time: form.default_start_time || null,
    default_end_time: form.default_end_time || null,
    days_of_week: [...form.days_of_week],
    instructors: [...form.instructors],
  };
  if (form.id) {
    await api.put(`/admin/courses/${form.id}`, payload);
  } else {
    await api.post('/admin/courses', payload);
  }
  closeModal();
  loadCourses();
}

async function remove(id) {
  if (!confirm('تأكيد الحذف؟')) return;
  await api.delete(`/admin/courses/${id}`);
  loadCourses();
}

function formatDate(date) {
  if (!date) return 'غير محدد';
  return new Date(date).toLocaleDateString('ar-EG');
}

function formatTime(time) {
  if (!time) return '--:--';
  return time.slice(0, 5);
}

function changePage(page) {
  filtersState.page = page;
  loadCourses();
}

function changePerPage(perPage) {
  filtersState.per_page = perPage;
  filtersState.page = 1;
  loadCourses();
}

watch(
  () => [filtersState.status, filtersState.search],
  () => {
    filtersState.page = 1;
    loadCourses();
  },
);

onMounted(async () => {
  await loadOptions();
  loadCourses();
});
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.65rem 0.9rem;
  font-size: 0.95rem;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.2rem;
}
</style>
