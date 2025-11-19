<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">التصنيفات</h2>
        <p class="text-sm text-slate-500">تنظيم الكورسات حسب التخصص.</p>
      </div>
      <div class="flex gap-3">
        <input v-model="form.name" placeholder="اسم التصنيف" class="input" />
        <button class="px-4 py-2 bg-primary text-white rounded-md" @click="save">
          {{ form.id ? 'تحديث' : 'إضافة' }}
        </button>
      </div>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
      <div
        v-for="category in categories"
        :key="category.id"
        class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center justify-between"
      >
        <div>
          <p class="font-semibold text-slate-900">{{ category.name }}</p>
        </div>
        <div class="flex gap-2 text-xs">
          <button class="text-primary" @click="edit(category)">تعديل</button>
          <button class="text-red-500" @click="remove(category.id)">حذف</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '../../../api';

const categories = ref([]);
const form = reactive({ id: null, name: '' });

async function loadCategories() {
  const { data } = await api.get('/admin/categories');
  categories.value = data;
}

function edit(category) {
  form.id = category.id;
  form.name = category.name;
}

async function save() {
  if (!form.name) return;
  if (form.id) {
    await api.put(`/admin/categories/${form.id}`, { name: form.name });
  } else {
    await api.post('/admin/categories', { name: form.name });
  }
  form.id = null;
  form.name = '';
  loadCategories();
}

async function remove(id) {
  if (!confirm('تأكيد الحذف؟')) return;
  await api.delete(`/admin/categories/${id}`);
  loadCategories();
}

onMounted(loadCategories);
</script>

<style scoped>
.input {
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.6rem 0.9rem;
  font-size: 0.9rem;
}
</style>

