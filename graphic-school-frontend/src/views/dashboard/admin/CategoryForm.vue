<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ editing ? 'تعديل التصنيف' : 'تصنيف جديد' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ editing ? 'تعديل بيانات التصنيف' : 'إضافة تصنيف جديد' }}</p>
      </div>
      <RouterLink
        to="/dashboard/admin/categories"
        class="btn-secondary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        رجوع
      </RouterLink>
    </div>

    <div v-if="loading && editing" class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6">
      <div class="text-center py-12">
        <div class="spinner mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">جاري تحميل بيانات التصنيف...</p>
      </div>
    </div>
    <div v-else class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-8">
      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="label mb-4 block">أسماء التصنيف باللغات</label>
          <div class="space-y-4">
            <div
              v-for="lang in availableLanguages"
              :key="lang.code"
              class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50"
            >
              <div class="flex items-center gap-3 mb-3">
                <img
                  v-if="lang.image_url || lang.image_path"
                  :src="lang.image_url || lang.image_path"
                  :alt="lang.native_name"
                  class="w-6 h-6 object-cover rounded"
                />
                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ lang.native_name }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">({{ lang.code }})</span>
              </div>
              <input
                v-model="form.translations[lang.code]"
                :placeholder="`اسم التصنيف بال${lang.native_name}`"
                class="input-enhanced"
                :required="lang.code === 'ar'"
                :disabled="loading"
              />
            </div>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">
            * اللغة العربية مطلوبة
          </p>
        </div>

        <div>
          <label class="label mb-3 block">الحالة</label>
          <label class="flex items-center gap-3 cursor-pointer">
            <input
              type="checkbox"
              v-model="form.is_active"
              class="w-5 h-5 text-primary border-slate-300 rounded focus:ring-primary focus:ring-2 cursor-pointer"
              :disabled="loading"
            />
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">نشط</span>
          </label>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
          <RouterLink
            to="/dashboard/admin/categories"
            class="btn-secondary"
            :class="{ 'pointer-events-none opacity-50': loading }"
          >
            إلغاء
          </RouterLink>
          <button 
            type="submit" 
            class="btn-primary" 
            :disabled="loading || !hasValidTranslations"
          >
            <span v-if="loading" class="inline-flex items-center gap-2">
              <div class="spinner"></div>
              جاري الحفظ...
            </span>
            <span v-else class="inline-flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              حفظ
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const { get, post, put } = useApi();

const editing = ref(false);
const loading = ref(false);
const availableLanguages = ref([]);
const form = reactive({
  translations: {},
  is_active: true,
});

onMounted(async () => {
  await loadLanguages();
  
  if (route.params.id) {
    editing.value = true;
    await loadCategory(route.params.id);
  } else {
    // Initialize translations with empty strings for all languages
    availableLanguages.value.forEach(lang => {
      form.translations[lang.code] = '';
    });
  }
});

async function loadLanguages() {
  try {
    const response = await get('/locales');
    let languages = [];
    
    if (response && response.data && response.data.locales) {
      languages = response.data.locales;
    } else if (response && response.locales) {
      languages = response.locales;
    } else if (Array.isArray(response)) {
      languages = response;
    }
    
    // Fallback to default languages
    if (languages.length === 0) {
      languages = [
        { code: 'ar', name: 'Arabic', native_name: 'العربية', image_path: null, is_active: true },
        { code: 'en', name: 'English', native_name: 'English', image_path: null, is_active: true },
      ];
    }
    
    availableLanguages.value = languages.filter(lang => lang.is_active);
  } catch (err) {
    console.error('Error loading languages:', err);
    // Fallback to default languages
    availableLanguages.value = [
      { code: 'ar', name: 'Arabic', native_name: 'العربية', image_path: null, is_active: true },
      { code: 'en', name: 'English', native_name: 'English', image_path: null, is_active: true },
    ];
  }
}

async function loadCategory(id) {
  try {
    loading.value = true;
    const response = await get(`/admin/categories/${id}?include_translations=1`);
    
    // Handle unified API response format
    let category;
    if (response && response.data) {
      category = response.data;
    } else if (Array.isArray(response)) {
      category = response[0];
    } else {
      category = response;
    }
    
    if (!category) {
      throw new Error('Category not found');
    }
    
    form.is_active = category.is_active ?? true;
    
    // Load translations
    if (category.translations) {
      // If translations is an object with locale keys
      Object.keys(category.translations).forEach(locale => {
        form.translations[locale] = category.translations[locale];
      });
    } else if (category.translations && Array.isArray(category.translations)) {
      // If translations is an array
      category.translations.forEach(trans => {
        form.translations[trans.locale] = trans.name;
      });
    }
    
    // Initialize missing languages with empty strings
    availableLanguages.value.forEach(lang => {
      if (!form.translations[lang.code]) {
        form.translations[lang.code] = '';
      }
    });
  } catch (err) {
    console.error('Error loading category:', err);
    toast.error(err.response?.data?.message || 'حدث خطأ أثناء تحميل بيانات التصنيف');
    router.push('/dashboard/admin/categories');
  } finally {
    loading.value = false;
  }
}

const hasValidTranslations = computed(() => {
  // At least Arabic must be filled
  return form.translations.ar && form.translations.ar.trim().length > 0;
});

async function submit() {
  if (!hasValidTranslations.value) {
    toast.error('يجب إدخال اسم التصنيف باللغة العربية على الأقل');
    return;
  }
  
  try {
    loading.value = true;
    const payload = {
      translations: form.translations,
      is_active: form.is_active,
    };
    
    if (editing.value) {
      await put(`/admin/categories/${route.params.id}`, payload);
      toast.success('تم تحديث التصنيف بنجاح');
    } else {
      await post('/admin/categories', payload);
      toast.success('تم إنشاء التصنيف بنجاح');
    }
    
    router.push('/dashboard/admin/categories');
  } catch (err) {
    console.error('Error saving category:', err);
    toast.error(err.response?.data?.message || 'حدث خطأ أثناء الحفظ');
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 0.5rem;
}

.dark .label {
  color: #94a3b8;
}
</style>

