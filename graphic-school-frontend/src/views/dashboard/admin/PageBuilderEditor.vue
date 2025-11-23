<template>
  <div class="h-screen flex flex-col">
    <!-- Toolbar -->
    <div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 p-4 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <router-link to="/dashboard/admin/page-builder" class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
          ← {{ $t('common.back') || 'Back' }}
        </router-link>
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ page?.title || 'Page Editor' }}</h2>
        <select v-model="currentLanguage" @change="switchLanguage" class="px-3 py-1 border rounded-lg text-sm">
          <option value="en">EN</option>
          <option value="ar">AR</option>
        </select>
      </div>
      <div class="flex gap-2">
        <button @click="saveStructure" class="btn-secondary" :disabled="saving">
          {{ saving ? ($t('common.saving') || 'Saving...') : ($t('common.save') || 'Save') }}
        </button>
        <button @click="publishPage" class="btn-primary" :disabled="publishing">
          {{ publishing ? ($t('common.publishing') || 'Publishing...') : ($t('pageBuilder.editor.publish') || 'Publish') }}
        </button>
      </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
      <!-- Blocks Sidebar -->
      <div class="w-64 bg-slate-50 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700 p-4 overflow-y-auto">
        <h3 class="font-semibold mb-4">{{ $t('pageBuilder.editor.blocks') || 'Blocks' }}</h3>
        <div class="space-y-2">
          <button 
            v-for="blockType in blockTypes" 
            :key="blockType.type"
            @click="addBlock(blockType.type)"
            class="w-full text-left px-3 py-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-primary text-sm"
          >
            {{ blockType.label }}
          </button>
        </div>
      </div>

      <!-- Canvas -->
      <div class="flex-1 overflow-y-auto p-8 bg-slate-100 dark:bg-slate-900">
        <div class="max-w-4xl mx-auto space-y-4">
          <div v-if="structure.length === 0" class="text-center py-20 text-slate-500 dark:text-slate-400">
            <p>{{ $t('pageBuilder.editor.noBlocks') || 'No blocks yet. Add a block from the sidebar.' }}</p>
          </div>
          <div 
            v-for="(block, index) in structure" 
            :key="block.id || index"
            class="bg-white dark:bg-slate-800 rounded-lg border-2 border-dashed border-slate-300 dark:border-slate-700 p-4 relative group"
            @click="selectBlock(index)"
            :class="{ 'border-primary': selectedBlockIndex === index }"
          >
            <button 
              v-if="selectedBlockIndex === index"
              @click.stop="removeBlock(index)"
              class="absolute top-2 right-2 text-red-600 hover:text-red-800"
            >
              ✕
            </button>
            <component 
              :is="getBlockComponent(block.type)" 
              :block="block"
              :editing="selectedBlockIndex === index"
              @update="updateBlock(index, $event)"
            />
          </div>
        </div>
      </div>

      <!-- Properties Panel -->
      <div v-if="selectedBlockIndex !== null" class="w-80 bg-white dark:bg-slate-800 border-l border-slate-200 dark:border-slate-700 p-4 overflow-y-auto">
        <h3 class="font-semibold mb-4">{{ $t('pageBuilder.editor.properties') || 'Block Properties' }}</h3>
        <component 
          :is="getPropertiesComponent(structure[selectedBlockIndex]?.type)" 
          :block="structure[selectedBlockIndex]"
          @update="updateBlock(selectedBlockIndex, $event)"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';
import BlockHero from './blocks/BlockHero.vue';
import BlockFeatures from './blocks/BlockFeatures.vue';
import BlockTestimonials from './blocks/BlockTestimonials.vue';
import BlockPrograms from './blocks/BlockPrograms.vue';
import BlockVideo from './blocks/BlockVideo.vue';
import BlockGallery from './blocks/BlockGallery.vue';
import BlockFAQ from './blocks/BlockFAQ.vue';
import BlockHTML from './blocks/BlockHTML.vue';
import BlockContact from './blocks/BlockContact.vue';
import BlockCTA from './blocks/BlockCTA.vue';
import BlockHeroProperties from './blocks/BlockHeroProperties.vue';
import BlockFeaturesProperties from './blocks/BlockFeaturesProperties.vue';

const route = useRoute();
const toast = useToast();
const loading = ref(false);
const saving = ref(false);
const publishing = ref(false);
const page = ref(null);
const structure = ref([]);
const selectedBlockIndex = ref(null);
const currentLanguage = ref('en');

const blockTypes = [
  { type: 'hero', label: 'Hero Section' },
  { type: 'features', label: 'Features' },
  { type: 'testimonials', label: 'Testimonials' },
  { type: 'programs', label: 'Programs' },
  { type: 'video', label: 'Video' },
  { type: 'gallery', label: 'Gallery' },
  { type: 'faq', label: 'FAQ' },
  { type: 'html', label: 'Custom HTML' },
  { type: 'contact', label: 'Contact' },
  { type: 'cta', label: 'Call to Action' },
];

async function loadPage() {
  loading.value = true;
  try {
    const response = await api.get(`/page-builder/pages/${route.params.id}`);
    page.value = response.data.data || response.data;
    if (page.value.structure) {
      structure.value = page.value.structure.structure || [];
    }
    currentLanguage.value = page.value.language || 'en';
  } catch (error) {
    console.error('Error loading page:', error);
    toast.error('Failed to load page');
  } finally {
    loading.value = false;
  }
}

function addBlock(type) {
  const block = {
    type,
    id: `block_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
    config: getDefaultConfig(type),
  };
  structure.value.push(block);
  selectedBlockIndex.value = structure.value.length - 1;
}

function getDefaultConfig(type) {
  const configs = {
    hero: { title: '', subtitle: '', background_image: null, button_text: '', button_link: '' },
    features: { title: '', features: [] },
    testimonials: { title: '', source: 'dynamic' },
    programs: { title: '', category: null },
    video: { url: '', title: '' },
    gallery: { images: [] },
    faq: { title: '', items: [] },
    html: { content: '' },
    contact: { email: '', phone: '', location: '' },
    cta: { title: '', description: '', button_text: '', button_link: '' },
  };
  return configs[type] || {};
}

function selectBlock(index) {
  selectedBlockIndex.value = index;
}

function removeBlock(index) {
  structure.value.splice(index, 1);
  selectedBlockIndex.value = null;
}

function updateBlock(index, updates) {
  structure.value[index] = { ...structure.value[index], ...updates };
}

async function saveStructure() {
  saving.value = true;
  try {
    await api.post(`/page-builder/pages/${route.params.id}/structure`, {
      structure: structure.value,
    });
    toast.success('Structure saved successfully');
  } catch (error) {
    toast.error('Failed to save structure');
  } finally {
    saving.value = false;
  }
}

async function publishPage() {
  publishing.value = true;
  try {
    await api.post(`/page-builder/pages/${route.params.id}/publish`);
    toast.success('Page published successfully');
    await loadPage();
  } catch (error) {
    toast.error('Failed to publish page');
  } finally {
    publishing.value = false;
  }
}

function switchLanguage() {
  // Load page for new language or create new version
  loadPage();
}

function getBlockComponent(type) {
  const components = {
    hero: BlockHero,
    features: BlockFeatures,
    testimonials: BlockTestimonials,
    programs: BlockPrograms,
    video: BlockVideo,
    gallery: BlockGallery,
    faq: BlockFAQ,
    html: BlockHTML,
    contact: BlockContact,
    cta: BlockCTA,
  };
  return components[type] || BlockHTML;
}

function getPropertiesComponent(type) {
  const components = {
    hero: BlockHeroProperties,
    features: BlockFeaturesProperties,
  };
  return components[type] || BlockHTML;
}

onMounted(() => {
  loadPage();
});
</script>

