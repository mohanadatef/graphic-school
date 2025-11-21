<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">{{ editing ? 'تعديل كورس' : 'كورس جديد' }}</h2>
        <p class="text-sm text-slate-500">{{ editing ? 'تعديل بيانات الكورس' : 'إضافة كورس جديد' }}</p>
      </div>
      <RouterLink
        to="/dashboard/admin/courses"
        class="px-4 py-2 border rounded-md hover:bg-slate-50 transition-colors"
      >
        رجوع
      </RouterLink>
    </div>

    <div class="bg-white rounded-2xl shadow border border-slate-100 p-6">
      <form @submit.prevent="submit" class="space-y-4">
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
        <div class="flex justify-end gap-3 pt-4 border-t">
          <RouterLink
            to="/dashboard/admin/courses"
            class="px-4 py-2 border rounded-md hover:bg-slate-50 transition-colors"
          >
            إلغاء
          </RouterLink>
          <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md" :disabled="loading">
            {{ loading ? 'جاري الحفظ...' : 'حفظ' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const { get, post, put } = useApi();

const editing = ref(false);
const loading = ref(false);
const categories = ref([]);
const instructors = ref([]);
const form = reactive({
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

onMounted(async () => {
  await loadOptions();
  
  if (route.params.id) {
    editing.value = true;
    await loadCourse(route.params.id);
  }
});

async function loadOptions() {
  try {
    const [{ data: categoryData }, { data: instructorData }] = await Promise.all([
      get('/admin/categories'),
      get('/instructors'),
    ]);
    categories.value = Array.isArray(categoryData) ? categoryData : (categoryData.data || []);
    instructors.value = Array.isArray(instructorData) ? instructorData : (instructorData.data || []);
    
    if (!form.category_id && categories.value.length) {
      form.category_id = categories.value[0].id;
    }
  } catch (err) {
    console.error('Error loading options:', err);
    toast.error('حدث خطأ أثناء تحميل البيانات');
  }
}

async function loadCourse(id) {
  try {
    loading.value = true;
    const data = await get(`/admin/courses/${id}`);
    const course = Array.isArray(data) ? data[0] : (data.data || data);
    form.title = course.title || '';
    form.category_id = course.category_id || categories.value[0]?.id || null;
    form.description = course.description || '';
    form.price = course.price || 0;
    form.start_date = course.start_date || '';
    form.default_start_time = course.default_start_time || '';
    form.default_end_time = course.default_end_time || '';
    form.session_count = course.session_count || 8;
    form.days_of_week = course.days_of_week ? [...course.days_of_week] : [];
    form.instructors = course.instructors?.map((inst) => inst.id) || [];
  } catch (err) {
    console.error('Error loading course:', err);
    toast.error('حدث خطأ أثناء تحميل بيانات الكورس');
    router.push('/dashboard/admin/courses');
  } finally {
    loading.value = false;
  }
}

async function submit() {
  try {
    loading.value = true;
    const payload = {
      ...form,
      start_date: form.start_date || null,
      default_start_time: form.default_start_time || null,
      default_end_time: form.default_end_time || null,
      days_of_week: [...form.days_of_week],
      instructors: [...form.instructors],
    };
    
    if (editing.value) {
      await put(`/admin/courses/${route.params.id}`, payload);
      toast.success('تم تحديث الكورس بنجاح');
    } else {
      await post('/admin/courses', payload);
      toast.success('تم إنشاء الكورس بنجاح');
    }
    
    router.push('/dashboard/admin/courses');
  } catch (err) {
    console.error('Error saving course:', err);
    toast.error(err.response?.data?.message || 'حدث خطأ أثناء الحفظ');
  } finally {
    loading.value = false;
  }
}
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

