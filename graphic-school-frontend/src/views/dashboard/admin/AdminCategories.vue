<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">التصنيفات</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">تنظيم الكورسات حسب التخصص.</p>
      </div>
      <RouterLink
        to="/dashboard/admin/categories/new"
        class="btn-primary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        تصنيف جديد
      </RouterLink>
    </div>

    <div v-if="loading" class="text-center py-12 text-slate-400 dark:text-slate-500">
      <div class="spinner mx-auto mb-4"></div>
      جاري التحميل...
    </div>
    <div v-else-if="error" class="text-center py-12 text-red-500 dark:text-red-400">{{ error }}</div>
    <div v-else-if="!categories.length" class="text-center py-12 text-slate-400 dark:text-slate-500">
      <p class="mb-4">لا توجد تصنيفات.</p>
      <RouterLink to="/dashboard/admin/categories/new" class="btn-primary inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        إضافة أول تصنيف
      </RouterLink>
    </div>
    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="category in categories"
        :key="category.id"
        class="card-premium p-6 hover-lift"
      >
        <div class="flex items-center justify-between mb-4">
          <div class="flex-1">
            <p class="font-bold text-lg text-slate-900 dark:text-white mb-1">{{ category.name || category.localized_name }}</p>
            <div class="flex items-center gap-2">
              <span
                class="px-2 py-1 text-xs rounded-full"
                :class="category.is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'"
              >
                {{ category.is_active ? 'نشط' : 'غير نشط' }}
              </span>
            </div>
          </div>
        </div>
        <div class="flex gap-2 pt-4 border-t border-slate-200 dark:border-slate-700">
          <RouterLink
            :to="`/dashboard/admin/categories/${category.id}/edit`"
            class="flex-1 btn-secondary text-center text-sm py-2"
          >
            تعديل
          </RouterLink>
          <button
            class="flex-1 px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors text-sm font-medium"
            @click="remove(category.id)"
          >
            حذف
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';

const categories = ref([]);
const { get, delete: del, loading, error } = useApi();
const toast = useToast();

async function loadCategories() {
  try {
    const data = await get('/admin/categories');
    // Handle both unified format and direct array
    if (Array.isArray(data)) {
      categories.value = data;
    } else if (data && Array.isArray(data.data)) {
      categories.value = data.data;
    } else if (data && data.data && Array.isArray(data.data)) {
      categories.value = data.data;
    } else {
      categories.value = [];
    }
  } catch (err) {
    console.error('Error loading categories:', err);
    categories.value = [];
    toast.error('حدث خطأ أثناء تحميل التصنيفات');
  }
}

async function remove(id) {
  if (!confirm('هل أنت متأكد من حذف هذا التصنيف؟')) return;
  try {
    await del(`/admin/categories/${id}`);
    toast.success('تم حذف التصنيف بنجاح');
    await loadCategories();
  } catch (err) {
    toast.error(error.value || 'حدث خطأ أثناء الحذف');
  }
}

onMounted(loadCategories);
</script>


