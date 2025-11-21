<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">{{ editing ? 'تعديل شريحة' : 'شريحة جديدة' }}</h2>
        <p class="text-sm text-slate-500">{{ editing ? 'تعديل بيانات الشريحة' : 'إضافة شريحة جديدة' }}</p>
      </div>
      <RouterLink
        to="/dashboard/admin/sliders"
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
            <input v-model="form.title" class="input" />
          </div>
          <div>
            <label class="label">الوصف</label>
            <textarea v-model="form.subtitle" rows="2" class="input"></textarea>
          </div>
          <div>
            <label class="label">نص الزر</label>
            <input v-model="form.button_text" class="input" />
          </div>
          <div>
            <label class="label">رابط الزر</label>
            <input v-model="form.button_url" class="input" />
          </div>
          <div>
            <label class="label">الترتيب</label>
            <input v-model.number="form.sort_order" type="number" class="input" />
          </div>
          <div>
            <label class="label">الحالة</label>
            <select v-model="form.is_active" class="input">
              <option :value="true">نشط</option>
              <option :value="false">مخفي</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="label">الصورة</label>
            <div class="flex items-center gap-4">
              <img
                v-if="previewImage || form.image_path"
                :src="previewImage || form.image_path"
                alt="preview"
                class="h-24 w-32 object-cover rounded border"
              />
              <div class="flex-1">
                <input
                  type="file"
                  accept="image/*"
                  @change="handleFile"
                  class="input"
                />
                <p class="text-xs text-slate-400 mt-1">أو أدخل مسار الصورة</p>
                <input
                  v-model="form.image_path"
                  type="text"
                  class="input mt-2"
                  placeholder="مسار الصورة"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t">
          <RouterLink
            to="/dashboard/admin/sliders"
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
const { get, post } = useApi();

const editing = ref(false);
const loading = ref(false);
const imageFile = ref(null);
const previewImage = ref('');
const form = reactive({
  title: '',
  subtitle: '',
  description: '',
  button_text: '',
  button_url: '',
  link: '',
  image_path: '',
  sort_order: 0,
  is_active: true,
});

onMounted(async () => {
  if (route.params.id) {
    editing.value = true;
    await loadSlider(route.params.id);
  }
});

async function loadSlider(id) {
  try {
    loading.value = true;
    const data = await get(`/admin/sliders/${id}`);
    const slider = Array.isArray(data) ? data[0] : (data.data || data);
    form.title = slider.title || '';
    form.subtitle = slider.subtitle || '';
    form.description = slider.description || '';
    form.button_text = slider.button_text || '';
    form.button_url = slider.button_url || '';
    form.link = slider.link || '';
    form.image_path = slider.image_path || '';
    form.sort_order = slider.sort_order || 0;
    form.is_active = slider.is_active ?? true;
    previewImage.value = slider.image_path || '';
  } catch (err) {
    console.error('Error loading slider:', err);
    toast.error('حدث خطأ أثناء تحميل بيانات الشريحة');
    router.push('/dashboard/admin/sliders');
  } finally {
    loading.value = false;
  }
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
  try {
    loading.value = true;
    const formData = new FormData();
    formData.append('title', form.title || '');
    formData.append('subtitle', form.subtitle || '');
    formData.append('button_text', form.button_text || '');
    formData.append('button_url', form.button_url || form.link || '');
    formData.append('sort_order', form.sort_order ?? 0);
    formData.append('is_active', form.is_active ? 1 : 0);
    
    if (imageFile.value) {
      formData.append('image', imageFile.value);
    } else if (form.image_path) {
      formData.append('image_path', form.image_path);
    }
    
    if (editing.value) {
      formData.append('_method', 'PUT');
      await post(`/admin/sliders/${route.params.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      toast.success('تم تحديث الشريحة بنجاح');
    } else {
      await post('/admin/sliders', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      toast.success('تم إنشاء الشريحة بنجاح');
    }
    router.push('/dashboard/admin/sliders');
  } catch (err) {
    console.error('Error saving slider:', err);
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
  font-size: 0.9rem;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.15rem;
}
</style>

