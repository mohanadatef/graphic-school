<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">السلايدر</h2>
        <p class="text-sm text-slate-500">إدارة الشرائح الرئيسية للموقع.</p>
      </div>
      <button class="px-4 py-2 bg-primary text-white rounded-md" @click="openModal()">شريحة جديدة</button>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow p-4 space-y-3">
      <div class="flex flex-wrap gap-3">
        <select v-model="filters.is_active" class="input w-40" @change="reload">
          <option value="">كل السلايدر</option>
          <option value="1">نشط</option>
          <option value="0">مخفي</option>
        </select>
        <select v-model.number="filters.per_page" class="input w-32" @change="changePerPage(filters.per_page)">
          <option :value="5">5</option>
          <option :value="10">10</option>
          <option :value="20">20</option>
        </select>
      </div>
    </div>

    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">الصورة</th>
            <th class="px-4 py-3 text-left">العنوان</th>
            <th class="px-4 py-3 text-left">الرابط</th>
            <th class="px-4 py-3 text-left">الترتيب</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="slider in sliders" :key="slider.id" class="border-t border-slate-100">
            <td class="px-4 py-3">
              <img :src="slider.image_path" alt="slide" class="h-14 w-24 object-cover rounded" />
            </td>
            <td class="px-4 py-3">
              <p class="font-semibold text-slate-900">{{ slider.title || 'بدون عنوان' }}</p>
              <p class="text-xs text-slate-500">{{ slider.subtitle }}</p>
            </td>
            <td class="px-4 py-3 text-xs text-primary">{{ slider.button_url || '-' }}</td>
            <td class="px-4 py-3">{{ slider.sort_order }}</td>
            <td class="px-4 py-3">
              <span :class="slider.is_active ? 'text-green-600' : 'text-slate-400'">
                {{ slider.is_active ? 'نشط' : 'مخفي' }}
              </span>
            </td>
            <td class="px-4 py-3 text-right text-xs">
              <button class="text-primary mr-2" @click="openModal(slider)">تعديل</button>
              <button class="text-red-500" @click="remove(slider.id)">حذف</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!sliders.length" class="text-center py-6 text-sm text-slate-400">لا توجد شرائح.</p>
    </div>

    <PaginationControls
      v-if="pagination.total"
      :meta="pagination"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />

    <dialog ref="dialogRef" class="rounded-2xl p-0 w-full max-w-xl">
      <form class="p-6 space-y-4" @submit.prevent="submit">
        <h3 class="text-xl font-semibold">{{ form.id ? 'تعديل شريحة' : 'شريحة جديدة' }}</h3>
        <div class="space-y-3">
          <div>
            <label class="label">العنوان</label>
            <input v-model="form.title" class="input" />
          </div>
          <div>
            <label class="label">الوصف</label>
            <textarea v-model="form.subtitle" rows="2" class="input"></textarea>
          </div>
          <div class="grid md:grid-cols-2 gap-3">
            <div>
              <label class="label">نص الزر</label>
              <input v-model="form.button_text" class="input" />
            </div>
            <div>
              <label class="label">رابط الزر</label>
              <input v-model="form.button_url" class="input" />
            </div>
          </div>
          <div class="grid md:grid-cols-2 gap-3">
            <div>
              <label class="label">ترتيب العرض</label>
              <input v-model.number="form.sort_order" type="number" class="input" />
            </div>
            <div class="flex items-center gap-2 pt-6">
              <input id="is_active" v-model="form.is_active" type="checkbox" class="h-4 w-4" />
              <label for="is_active" class="text-sm text-slate-600">نشط</label>
            </div>
          </div>
          <div>
            <label class="label">الصورة</label>
            <div class="flex items-center gap-4">
              <img v-if="previewImage" :src="previewImage" alt="preview" class="h-16 w-24 object-cover rounded border" />
              <input type="file" class="input" accept="image/*" @change="handleFile" />
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-3 pt-4">
          <button type="button" class="px-4 py-2 border rounded-md" @click="closeModal">إلغاء</button>
          <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md">حفظ</button>
        </div>
      </form>
    </dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '../../../api';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const sliders = ref([]);
const dialogRef = ref(null);
const imageFile = ref(null);
const previewImage = ref('');

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

const filters = reactive({
  page: 1,
  per_page: 10,
  is_active: '',
});

const form = reactive({
  id: null,
  title: '',
  subtitle: '',
  button_text: '',
  button_url: '',
  sort_order: 1,
  is_active: true,
});

async function loadSliders() {
  const { data } = await api.get('/admin/sliders', {
    params: {
      page: filters.page,
      per_page: filters.per_page,
      is_active: filters.is_active === '' ? undefined : filters.is_active,
    },
  });

  sliders.value = data.data;
  Object.assign(pagination, {
    current_page: data.meta.current_page,
    last_page: data.meta.last_page,
    per_page: data.meta.per_page,
    total: data.meta.total,
  });
}

function reload() {
  filters.page = 1;
  loadSliders();
}

function changePage(page) {
  filters.page = page;
  loadSliders();
}

function changePerPage(perPage) {
  filters.per_page = perPage;
  filters.page = 1;
  loadSliders();
}

function openModal(slider) {
  form.id = slider?.id || null;
  form.title = slider?.title || '';
  form.subtitle = slider?.subtitle || '';
  form.button_text = slider?.button_text || '';
  form.button_url = slider?.button_url || '';
  form.sort_order = slider?.sort_order ?? 1;
  form.is_active = slider?.is_active ?? true;
  previewImage.value = slider?.image_path || '';
  imageFile.value = null;
  dialogRef.value.showModal();
}

function closeModal() {
  dialogRef.value.close();
}

function handleFile(event) {
  const file = event.target.files?.[0];
  if (!file) {
    imageFile.value = null;
    return;
  }
  imageFile.value = file;
  previewImage.value = URL.createObjectURL(file);
}

async function submit() {
  const formData = new FormData();
  formData.append('title', form.title || '');
  formData.append('subtitle', form.subtitle || '');
  formData.append('button_text', form.button_text || '');
  formData.append('button_url', form.button_url || '');
  formData.append('sort_order', form.sort_order ?? 1);
  formData.append('is_active', form.is_active ? 1 : 0);

  if (imageFile.value) {
    formData.append('image', imageFile.value);
  }

  if (form.id) {
    formData.append('_method', 'PUT');
    await api.post(`/admin/sliders/${form.id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
  } else {
    await api.post('/admin/sliders', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
  }

  closeModal();
  loadSliders();
}

async function remove(id) {
  if (!confirm('تأكيد الحذف؟')) return;
  await api.delete(`/admin/sliders/${id}`);
  loadSliders();
}

onMounted(loadSliders);
</script>

<style scoped>
.input {
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.5rem 0.75rem;
  font-size: 0.9rem;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.2rem;
}
</style>

