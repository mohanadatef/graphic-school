<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">
          {{ pageId ? 'تعديل الصفحة' : 'صفحة جديدة' }}
        </h2>
        <p class="text-sm text-slate-500">إدارة محتوى الصفحة وإعداداتها</p>
      </div>
      <RouterLink
        to="/dashboard/admin/pages"
        class="px-4 py-2 border border-slate-300 rounded-md hover:bg-slate-50"
      >
        رجوع
      </RouterLink>
    </div>

    <form @submit.prevent="submit" class="space-y-6">
      <div class="bg-white border border-slate-100 rounded-2xl shadow p-6 space-y-4">
        <h3 class="text-lg font-semibold text-slate-900">معلومات أساسية</h3>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            الرابط (Slug) <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.slug"
            type="text"
            required
            class="input w-full"
            placeholder="about-us"
            :disabled="!!pageId"
          />
          <p class="text-xs text-slate-500 mt-1">
            الرابط الذي سيظهر في المتصفح (مثال: /about-us)
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            العنوان <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.title"
            type="text"
            required
            class="input w-full"
            placeholder="عن الصفحة"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            المحتوى
          </label>
          <textarea
            v-model="form.content"
            rows="10"
            class="input w-full"
            placeholder="محتوى الصفحة (HTML أو Markdown)"
          ></textarea>
          <p class="text-xs text-slate-500 mt-1">
            يمكنك استخدام HTML أو Markdown لتنسيق المحتوى
          </p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
              القالب (Template)
            </label>
            <select v-model="form.template" class="input w-full">
              <option value="default">افتراضي</option>
              <option value="home">الصفحة الرئيسية</option>
              <option value="about">عن الشركة</option>
              <option value="contact">تواصل معنا</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
              الحالة
            </label>
            <select v-model="form.is_active" class="input w-full">
              <option :value="true">نشطة</option>
              <option :value="false">مخفية</option>
            </select>
          </div>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-2xl shadow p-6 space-y-4">
        <h3 class="text-lg font-semibold text-slate-900">التحكم في الأقسام (Sections)</h3>
        <p class="text-sm text-slate-500 mb-4">اختر الأقسام التي تريد إظهارها في هذه الصفحة</p>

        <div class="grid grid-cols-2 gap-4">
          <label class="flex items-center space-x-2 cursor-pointer">
            <input
              v-model="form.sections.slider"
              type="checkbox"
              class="w-4 h-4 text-primary rounded"
            />
            <span class="text-sm font-medium text-slate-700">السلايدر (Slider)</span>
          </label>

          <label class="flex items-center space-x-2 cursor-pointer">
            <input
              v-model="form.sections.testimonials"
              type="checkbox"
              class="w-4 h-4 text-primary rounded"
            />
            <span class="text-sm font-medium text-slate-700">الشهادات (Testimonials)</span>
          </label>

          <label class="flex items-center space-x-2 cursor-pointer">
            <input
              v-model="form.sections.featured_courses"
              type="checkbox"
              class="w-4 h-4 text-primary rounded"
            />
            <span class="text-sm font-medium text-slate-700">الكورسات المميزة</span>
          </label>

          <label class="flex items-center space-x-2 cursor-pointer">
            <input
              v-model="form.sections.statistics"
              type="checkbox"
              class="w-4 h-4 text-primary rounded"
            />
            <span class="text-sm font-medium text-slate-700">الإحصائيات</span>
          </label>

          <label class="flex items-center space-x-2 cursor-pointer">
            <input
              v-model="form.sections.faq"
              type="checkbox"
              class="w-4 h-4 text-primary rounded"
            />
            <span class="text-sm font-medium text-slate-700">الأسئلة الشائعة (FAQ)</span>
          </label>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-2xl shadow p-6 space-y-4">
        <h3 class="text-lg font-semibold text-slate-900">إعدادات SEO</h3>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Meta Title
          </label>
          <input
            v-model="form.meta_title"
            type="text"
            class="input w-full"
            placeholder="عنوان الصفحة في محركات البحث"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Meta Description
          </label>
          <textarea
            v-model="form.meta_description"
            rows="3"
            class="input w-full"
            placeholder="وصف الصفحة في محركات البحث"
          ></textarea>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3">
        <RouterLink
          to="/dashboard/admin/pages"
          class="px-4 py-2 border border-slate-300 rounded-md hover:bg-slate-50"
        >
          إلغاء
        </RouterLink>
        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-2 bg-primary text-white rounded-md hover:bg-primary-dark disabled:opacity-50"
        >
          {{ loading ? 'جاري الحفظ...' : 'حفظ' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { RouterLink, useRoute, useRouter } from 'vue-router';
import { cmsService } from '../../../services/api/cmsService';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const pageId = route.params.id;
const loading = ref(false);

const form = reactive({
  slug: '',
  title: '',
  content: '',
  template: 'default',
  sections: {
    slider: true,
    testimonials: true,
    featured_courses: true,
    statistics: true,
    faq: true,
  },
  is_active: true,
  meta_title: '',
  meta_description: '',
});

const loadPage = async () => {
  if (!pageId) return;

  try {
    loading.value = true;
    // Get all pages and find by ID
    const response = await cmsService.getPages({ per_page: 1000 });
    if (response.success && response.data) {
      const pages = response.data.data || response.data || [];
      const page = pages.find((p) => p.id === parseInt(pageId));
      if (page) {
        form.slug = page.slug;
        form.title = page.title;
        form.content = page.content || '';
        form.template = page.template || 'default';
        form.sections = page.sections || {
          slider: true,
          testimonials: true,
          featured_courses: true,
          statistics: true,
          faq: true,
        };
        form.is_active = page.is_active ?? true;
        form.meta_title = page.meta_title || '';
        form.meta_description = page.meta_description || '';
      } else {
        toast.error('الصفحة غير موجودة');
        router.push('/dashboard/admin/pages');
      }
    }
  } catch (error) {
    console.error('Error loading page:', error);
    toast.error('حدث خطأ أثناء تحميل الصفحة');
    router.push('/dashboard/admin/pages');
  } finally {
    loading.value = false;
  }
};

const submit = async () => {
  try {
    loading.value = true;

    const data = {
      slug: form.slug,
      title: form.title,
      content: form.content,
      template: form.template,
      sections: form.sections,
      is_active: form.is_active,
      meta_title: form.meta_title,
      meta_description: form.meta_description,
    };

    if (pageId) {
      await cmsService.updatePage(pageId, data);
      toast.success('تم تحديث الصفحة بنجاح');
    } else {
      await cmsService.createPage(data);
      toast.success('تم إنشاء الصفحة بنجاح');
    }

    router.push('/dashboard/admin/pages');
  } catch (error) {
    console.error('Error saving page:', error);
    const message = error.response?.data?.message || 'حدث خطأ أثناء حفظ الصفحة';
    toast.error(message);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadPage();
});
</script>

