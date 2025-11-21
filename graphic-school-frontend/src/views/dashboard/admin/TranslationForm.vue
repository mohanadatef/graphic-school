<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">{{ editing ? 'تعديل ترجمة' : 'ترجمة جديدة' }}</h2>
        <p class="text-sm text-slate-500">{{ editing ? 'تعديل بيانات الترجمة' : 'إضافة ترجمة جديدة' }}</p>
      </div>
      <RouterLink
        to="/dashboard/admin/translations"
        class="px-4 py-2 border rounded-md hover:bg-slate-50 transition-colors"
      >
        رجوع
      </RouterLink>
    </div>

    <div class="bg-white rounded-2xl shadow border border-slate-100 p-6">
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="label">{{ $t('admin.key') }}</label>
            <input
              v-model="form.key"
              type="text"
              required
              class="input"
              placeholder="auth.login"
            />
            <p class="text-xs text-slate-400 mt-1">{{ $t('admin.keyHint') }}</p>
          </div>
          <div>
            <label class="label">{{ $t('admin.locale') }}</label>
            <select v-model="form.locale" class="input" required>
              <option value="en">English (en)</option>
              <option value="ar">العربية (ar)</option>
            </select>
          </div>
          <div>
            <label class="label">{{ $t('admin.group') }}</label>
            <input
              v-model="form.group"
              type="text"
              class="input"
              placeholder="messages"
            />
            <p class="text-xs text-slate-400 mt-1">{{ $t('admin.groupHint') }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="label">{{ $t('admin.value') }}</label>
            <textarea
              v-model="form.value"
              required
              class="input"
              rows="3"
              :placeholder="$t('admin.valuePlaceholder')"
            ></textarea>
          </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t">
          <RouterLink
            to="/dashboard/admin/translations"
            class="px-4 py-2 border rounded-md hover:bg-slate-50 transition-colors"
          >
            {{ $t('common.cancel') }}
          </RouterLink>
          <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md" :disabled="saving">
            {{ saving ? $t('common.loading') : $t('common.save') }}
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
import { useI18n } from '../../../composables/useI18n';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const { t } = useI18n();
const { get, post, put } = useApi();

const editing = ref(false);
const saving = ref(false);
const form = reactive({
  key: '',
  locale: 'en',
  value: '',
  group: 'messages',
});

onMounted(async () => {
  if (route.params.id) {
    editing.value = true;
    await loadTranslation(route.params.id);
  }
});

async function loadTranslation(id) {
  try {
    saving.value = true;
    const data = await get(`/admin/translations/${id}`);
    const translation = Array.isArray(data) ? data[0] : (data.data || data);
    form.key = translation.key || '';
    form.locale = translation.locale || 'en';
    form.value = translation.value || '';
    form.group = translation.group || 'messages';
  } catch (err) {
    console.error('Error loading translation:', err);
    toast.error('حدث خطأ أثناء تحميل بيانات الترجمة');
    router.push('/dashboard/admin/translations');
  } finally {
    saving.value = false;
  }
}

async function submit() {
  try {
    saving.value = true;
    if (editing.value) {
      await put(`/admin/translations/${route.params.id}`, form);
      toast.success('تم تحديث الترجمة بنجاح');
    } else {
      await post('/admin/translations', form);
      toast.success('تم إنشاء الترجمة بنجاح');
    }
    router.push('/dashboard/admin/translations');
  } catch (err) {
    console.error('Error saving translation:', err);
    toast.error(err.response?.data?.message || 'حدث خطأ أثناء الحفظ');
  } finally {
    saving.value = false;
  }
}
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  outline: none;
}

.input:focus {
  border-color: #1d4ed8;
  box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
}

.label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.25rem;
}
</style>

