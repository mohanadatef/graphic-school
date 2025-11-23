<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.programs.edit') || 'Edit Program' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.programs.editSubtitle') || 'Update program details' }}</p>
      </div>
      <RouterLink
        to="/dashboard/admin/programs"
        class="btn-secondary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ $t('common.back') || 'Back' }}
      </RouterLink>
    </div>

    <div v-if="loading" class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6">
      <div class="text-center py-12">
        <div class="spinner mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
      </div>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-8">
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Basic Fields -->
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="label">{{ $t('admin.programs.type') || 'Type' }}</label>
            <select v-model="form.type" class="input" required>
              <option value="bootcamp">Bootcamp</option>
              <option value="track">Track</option>
              <option value="workshop">Workshop</option>
              <option value="course">Course</option>
            </select>
          </div>
          <div>
            <label class="label">{{ $t('admin.programs.level') || 'Level' }}</label>
            <select v-model="form.level" class="input">
              <option value="beginner">Beginner</option>
              <option value="intermediate">Intermediate</option>
              <option value="advanced">Advanced</option>
            </select>
          </div>
          <div>
            <label class="label">{{ $t('admin.programs.durationWeeks') || 'Duration (Weeks)' }}</label>
            <input v-model.number="form.duration_weeks" type="number" min="1" class="input" />
          </div>
          <div>
            <label class="label">{{ $t('admin.programs.price') || 'Price' }}</label>
            <input v-model.number="form.price" type="number" min="0" step="0.01" class="input" />
          </div>
          <div>
            <label class="label">{{ $t('admin.programs.image') || 'Image' }}</label>
            <input @change="handleImageChange" type="file" accept="image/*" class="input" />
            <p v-if="program?.image_path" class="text-xs text-slate-500 dark:text-slate-400 mt-1">
              Current: {{ program.image_path }}
            </p>
          </div>
          <div>
            <label class="label">{{ $t('admin.programs.sortOrder') || 'Sort Order' }}</label>
            <input v-model.number="form.sort_order" type="number" class="input" />
          </div>
        </div>

        <div>
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" v-model="form.is_active" class="w-5 h-5 text-primary border-slate-300 rounded focus:ring-primary focus:ring-2" />
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('common.active') || 'Active' }}</span>
          </label>
        </div>

        <!-- Translations -->
        <div>
          <label class="label mb-4 block">{{ $t('admin.programs.translations') || 'Translations' }}</label>
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
              </div>
              <div class="space-y-3">
                <div>
                  <label class="label text-sm">{{ $t('admin.programs.title') || 'Title' }}</label>
                  <input
                    v-model="form.translations[lang.code].title"
                    class="input"
                    :required="lang.code === 'ar'"
                  />
                </div>
                <div>
                  <label class="label text-sm">{{ $t('admin.programs.description') || 'Description' }}</label>
                  <textarea
                    v-model="form.translations[lang.code].description"
                    class="input"
                    rows="4"
                  ></textarea>
                </div>
                <div>
                  <label class="label text-sm">{{ $t('admin.programs.metaTitle') || 'Meta Title' }}</label>
                  <input
                    v-model="form.translations[lang.code].meta_title"
                    class="input"
                  />
                </div>
                <div>
                  <label class="label text-sm">{{ $t('admin.programs.metaDescription') || 'Meta Description' }}</label>
                  <textarea
                    v-model="form.translations[lang.code].meta_description"
                    class="input"
                    rows="2"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
          <RouterLink to="/dashboard/admin/programs" class="btn-secondary">
            {{ $t('common.cancel') || 'Cancel' }}
          </RouterLink>
          <button type="submit" class="btn-primary" :disabled="loading || !hasValidTranslations">
            <span v-if="loading" class="inline-flex items-center gap-2">
              <div class="spinner"></div>
              {{ $t('common.saving') || 'Saving...' }}
            </span>
            <span v-else>{{ $t('common.save') || 'Save' }}</span>
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
const { get, put } = useApi();

const loading = ref(false);
const program = ref(null);
const availableLanguages = ref([]);
const imageFile = ref(null);

const form = reactive({
  type: 'course',
  duration_weeks: null,
  price: null,
  level: 'beginner',
  is_active: true,
  sort_order: 0,
  translations: {},
});

const hasValidTranslations = computed(() => {
  return availableLanguages.value.every(lang => {
    const translation = form.translations[lang.code];
    return translation && translation.title && translation.title.trim() !== '';
  });
});

onMounted(async () => {
  await loadLanguages();
  await loadProgram();
});

async function loadLanguages() {
  try {
    const response = await get('/locales');
    let languages = [];
    
    if (response?.data?.locales) {
      languages = response.data.locales;
    } else if (response?.locales) {
      languages = response.locales;
    } else if (Array.isArray(response)) {
      languages = response;
    }
    
    if (languages.length === 0) {
      languages = [
        { code: 'en', name: 'English', native_name: 'English' },
        { code: 'ar', name: 'Arabic', native_name: 'العربية' },
      ];
    }
    
    availableLanguages.value = languages;
  } catch (error) {
    console.error('Error loading languages:', error);
    availableLanguages.value = [
      { code: 'en', name: 'English', native_name: 'English' },
      { code: 'ar', name: 'Arabic', native_name: 'العربية' },
    ];
  }
}

async function loadProgram() {
  try {
    loading.value = true;
    const response = await get(`/admin/programs/${route.params.id}?include_translations=true`);
    program.value = response?.data || response;
    
    // Populate form
    form.type = program.value.type;
    form.duration_weeks = program.value.duration_weeks;
    form.price = program.value.price;
    form.level = program.value.level;
    form.is_active = program.value.is_active;
    form.sort_order = program.value.sort_order;
    
    // Populate translations
    if (program.value.translations) {
      program.value.translations.forEach(trans => {
        if (!form.translations[trans.locale]) {
          form.translations[trans.locale] = {
            locale: trans.locale,
            title: trans.title || '',
            description: trans.description || '',
            meta_title: trans.meta_title || '',
            meta_description: trans.meta_description || '',
          };
        }
      });
    }
    
    // Ensure all languages have translations
    availableLanguages.value.forEach(lang => {
      if (!form.translations[lang.code]) {
        form.translations[lang.code] = {
          locale: lang.code,
          title: '',
          description: '',
          meta_title: '',
          meta_description: '',
        };
      }
    });
  } catch (error) {
    toast.error('Failed to load program');
    console.error(error);
    router.push({ name: 'admin-programs' });
  } finally {
    loading.value = false;
  }
}

function handleImageChange(event) {
  imageFile.value = event.target.files[0];
}

async function submit() {
  try {
    loading.value = true;
    
    const formData = new FormData();
    formData.append('type', form.type);
    if (form.duration_weeks) formData.append('duration_weeks', form.duration_weeks);
    if (form.price) formData.append('price', form.price);
    if (form.level) formData.append('level', form.level);
    formData.append('is_active', form.is_active ? '1' : '0');
    formData.append('sort_order', form.sort_order);
    
    if (imageFile.value) {
      formData.append('image', imageFile.value);
    }
    
    const translationsArray = availableLanguages.value.map(lang => form.translations[lang.code]);
    formData.append('translations', JSON.stringify(translationsArray));
    
    await put(`/admin/programs/${route.params.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    
    toast.success('Program updated successfully');
    router.push({ name: 'admin-programs' });
  } catch (error) {
    toast.error('Failed to update program');
    console.error(error);
  } finally {
    loading.value = false;
  }
}
</script>

