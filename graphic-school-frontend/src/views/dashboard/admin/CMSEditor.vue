<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
          {{ $t('admin.cms.editor') || 'CMS Editor' }}
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          {{ $t('admin.cms.editPageContent') || 'Edit page content and sections' }}
        </p>
      </div>
      <select
        v-model="selectedPageId"
        @change="loadPage"
        class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
      >
        <option value="">{{ $t('admin.cms.selectPage') || 'Select Page' }}</option>
        <option v-for="page in pages" :key="page.id" :value="page.id">
          {{ getPageTitle(page) }}
        </option>
      </select>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="!currentPage" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">
        {{ $t('admin.cms.selectPageToEdit') || 'Please select a page to edit' }}
      </p>
    </div>

    <div v-else class="space-y-6">
      <!-- Page Info -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
          {{ $t('admin.cms.pageInfo') || 'Page Information' }}
        </h3>
        <div class="space-y-4">
          <div>
            <label class="label">{{ $t('admin.cms.slug') || 'Slug' }}</label>
            <input
              v-model="pageForm.slug"
              type="text"
              class="input"
              disabled
            />
          </div>
          <div>
            <label class="label">{{ $t('admin.cms.title') || 'Title' }}</label>
            <MultiLanguageInput
              v-model="pageForm.title"
              field-name="title"
              :placeholder="$t('admin.cms.pageTitle') || 'Page Title'"
            />
          </div>
          <div>
            <label class="label">{{ $t('admin.cms.content') || 'Content' }}</label>
            <MultiLanguageTextarea
              v-model="pageForm.content"
              field-name="content"
              :rows="4"
              :placeholder="$t('admin.cms.pageContent') || 'Page Content'"
            />
          </div>
          <div>
            <label class="label">{{ $t('admin.cms.metaDescription') || 'Meta Description' }}</label>
            <MultiLanguageTextarea
              v-model="pageForm.meta_description"
              field-name="meta_description"
              :rows="2"
              :placeholder="$t('admin.cms.seoDescription') || 'SEO Description'"
            />
          </div>
          <div class="flex items-center gap-2">
            <input
              v-model="pageForm.is_active"
              type="checkbox"
              class="w-4 h-4"
            />
            <label>{{ $t('admin.cms.isActive') || 'Page is Active' }}</label>
          </div>
        </div>
      </div>

      <!-- Page Blocks -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ $t('admin.cms.sections') || 'Page Sections' }}
          </h3>
          <button
            @click="addBlock"
            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm"
          >
            + {{ $t('admin.cms.addSection') || 'Add Section' }}
          </button>
        </div>

        <div class="space-y-4">
          <div
            v-for="(block, index) in blocks"
            :key="block.id || `new-${index}`"
            class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 space-y-4"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-slate-600 dark:text-slate-400">
                  {{ $t('admin.cms.section') || 'Section' }} {{ index + 1 }}
                </span>
                <select
                  v-model="block.type"
                  class="text-sm px-3 py-1 border border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-700"
                >
                  <option value="hero">{{ $t('admin.cms.blockTypes.hero') || 'Hero' }}</option>
                  <option value="features">{{ $t('admin.cms.blockTypes.features') || 'Features' }}</option>
                  <option value="testimonials">{{ $t('admin.cms.blockTypes.testimonials') || 'Testimonials' }}</option>
                  <option value="cta">{{ $t('admin.cms.blockTypes.cta') || 'Call to Action' }}</option>
                  <option value="content">{{ $t('admin.cms.blockTypes.content') || 'Content' }}</option>
                </select>
              </div>
              <div class="flex items-center gap-2">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="block.is_enabled"
                    type="checkbox"
                    class="w-4 h-4"
                  />
                  <span class="text-sm">{{ $t('admin.cms.enabled') || 'Enabled' }}</span>
                </label>
                <button
                  @click="removeBlock(index)"
                  class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>

            <div class="space-y-3">
              <div>
                <label class="label text-sm">{{ $t('admin.cms.sectionTitle') || 'Section Title' }}</label>
                <MultiLanguageInput
                  v-model="block.title"
                  :field-name="`block_${index}_title`"
                  :placeholder="$t('admin.cms.sectionTitle') || 'Section Title'"
                />
              </div>
              <div>
                <label class="label text-sm">{{ $t('admin.cms.sectionContent') || 'Section Content' }}</label>
                <MultiLanguageTextarea
                  v-model="block.content"
                  :field-name="`block_${index}_content`"
                  :rows="3"
                  :placeholder="$t('admin.cms.sectionContent') || 'Section Content'"
                />
              </div>
            </div>
          </div>

          <p v-if="blocks.length === 0" class="text-center py-8 text-slate-500 dark:text-slate-400">
            {{ $t('admin.cms.noSections') || 'No sections yet. Click "Add Section" to create one.' }}
          </p>
        </div>
      </div>

      <!-- Save Button -->
      <div class="flex justify-end gap-3">
        <button
          @click="resetForm"
          class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
        >
          {{ $t('common.cancel') || 'Cancel' }}
        </button>
        <button
          @click="savePage"
          :disabled="saving"
          class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50"
        >
          {{ saving ? ($t('common.saving') || 'Saving...') : ($t('common.save') || 'Save') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useI18n } from '../../../composables/useI18n';
import { useLanguageStore } from '../../../stores/language';
import MultiLanguageInput from '../../../components/common/MultiLanguageInput.vue';
import MultiLanguageTextarea from '../../../components/common/MultiLanguageTextarea.vue';

const { get, put, post } = useApi();
const toast = useToast();
const { t } = useI18n();
const languageStore = useLanguageStore();

const pages = ref([]);
const selectedPageId = ref('');
const currentPage = ref(null);
const loading = ref(false);
const saving = ref(false);

const pageForm = ref({
  slug: '',
  title: {},
  content: {},
  meta_description: {},
  is_active: true,
});

const blocks = ref([]);

onMounted(async () => {
  await languageStore.init();
  await loadPages();
});

async function loadPages() {
  try {
    loading.value = true;
    const response = await get('/admin/pages', { params: { per_page: 100 } });
    pages.value = Array.isArray(response) ? response : (response?.data || []);
  } catch (err) {
    console.error('Error loading pages:', err);
    toast.error(t('errors.loadError') || 'Failed to load pages');
  } finally {
    loading.value = false;
  }
}

async function loadPage() {
  if (!selectedPageId.value) {
    currentPage.value = null;
    return;
  }

  try {
    loading.value = true;
    const response = await get(`/admin/pages/${selectedPageId.value}`);
    currentPage.value = response.data || response;
    
    // Populate form
    pageForm.value = {
      slug: currentPage.value.slug,
      title: currentPage.value.title || {},
      content: currentPage.value.content || {},
      meta_description: currentPage.value.meta_description || {},
      is_active: currentPage.value.is_active ?? true,
    };

    // Load blocks
    blocks.value = (currentPage.value.blocks || []).map(block => ({
      id: block.id,
      type: block.type,
      title: block.title || {},
      content: block.content || {},
      config: block.config || {},
      is_enabled: block.is_enabled ?? true,
      sort_order: block.sort_order || 0,
    }));
  } catch (err) {
    console.error('Error loading page:', err);
    toast.error(t('errors.loadError') || 'Failed to load page');
  } finally {
    loading.value = false;
  }
}

function getPageTitle(page) {
  if (typeof page.title === 'object') {
    return page.title[languageStore.getDefaultLanguage?.code || 'en'] || page.title.en || page.slug;
  }
  return page.title || page.slug;
}

function addBlock() {
  blocks.value.push({
    type: 'content',
    title: {},
    content: {},
    config: {},
    is_enabled: true,
    sort_order: blocks.value.length,
  });
}

function removeBlock(index) {
  blocks.value.splice(index, 1);
}

function resetForm() {
  selectedPageId.value = '';
  currentPage.value = null;
  pageForm.value = {
    slug: '',
    title: {},
    content: {},
    meta_description: {},
    is_active: true,
  };
  blocks.value = [];
}

async function savePage() {
  if (!currentPage.value) return;

  try {
    saving.value = true;

    // Update page
    await put(`/admin/pages/${currentPage.value.id}`, pageForm.value);

    // Update blocks
    await put(`/admin/pages/${currentPage.value.id}/blocks`, {
      blocks: blocks.value.map((block, index) => ({
        ...block,
        sort_order: index,
      })),
    });

    toast.success(t('admin.cms.saved') || 'Page saved successfully');
    await loadPage(); // Reload to get updated data
  } catch (err) {
    console.error('Error saving page:', err);
    toast.error(err.response?.data?.message || t('errors.saveError') || 'Failed to save page');
  } finally {
    saving.value = false;
  }
}
</script>

<style scoped>
.label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.5rem;
}
.dark .label {
  color: #cbd5e1;
}
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 0.65rem 0.9rem;
  font-size: 0.95rem;
  background: white;
}
.dark .input {
  background: #1e293b;
  border-color: #334155;
  color: #f1f5f9;
}
</style>

