<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">التصنيفات</h2>
        <p class="text-sm text-slate-500">تنظيم الكورسات حسب التخصص.</p>
      </div>
      <div class="flex gap-3">
        <input
          v-model="form.name"
          placeholder="اسم التصنيف"
          class="input"
          @keyup.enter="save"
          :disabled="loading"
        />
        <button
          class="px-4 py-2 bg-primary text-white rounded-md"
          @click="save"
          :disabled="loading || !form.name.trim()"
        >
          {{ loading ? 'جاري الحفظ...' : form.id ? 'تحديث' : 'إضافة' }}
        </button>
        <button
          v-if="form.id"
          class="px-4 py-2 border rounded-md"
          @click="resetForm"
          :disabled="loading"
        >
          إلغاء
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12 text-slate-400">جاري التحميل...</div>
    <div v-else-if="error" class="text-center py-12 text-red-500">{{ error }}</div>
    <div v-else-if="!categories.length" class="text-center py-12 text-slate-400">لا توجد تصنيفات.</div>
    <div v-else class="grid md:grid-cols-3 gap-4">
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
import { useApi } from '../../../composables/useApi';

const categories = ref([]);
const form = reactive({ id: null, name: '' });

const { get, post, put, delete: del, loading, error } = useApi();

async function loadCategories() {
  try {
    const data = await get('/admin/categories');
    categories.value = data || [];
  } catch (err) {
    console.error('Error loading categories:', err);
    categories.value = [];
  }
}

function edit(category) {
  form.id = category.id;
  form.name = category.name;
}

function resetForm() {
  form.id = null;
  form.name = '';
}

async function save() {
  if (!form.name.trim()) {
    alert('يرجى إدخال اسم التصنيف');
    return;
  }
  
  try {
    if (form.id) {
      await put(`/admin/categories/${form.id}`, { name: form.name });
    } else {
      await post('/admin/categories', { name: form.name });
    }
    resetForm();
    await loadCategories();
  } catch (err) {
    alert(error.value || 'حدث خطأ أثناء الحفظ');
  }
}

async function remove(id) {
  if (!confirm('تأكيد الحذف؟')) return;
  try {
    await del(`/admin/categories/${id}`);
    await loadCategories();
  } catch (err) {
    alert(error.value || 'حدث خطأ أثناء الحذف');
  }
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

