<template>
  <div class="space-y-8">
    <!-- Page Title -->
    <div v-if="pageData?.title" class="text-center py-12 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 rounded-2xl">
      <h1 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-4">
        {{ pageData.title }}
      </h1>
      <p v-if="pageData.meta_description" class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
        {{ pageData.meta_description }}
      </p>
    </div>

    <!-- Page Content -->
    <div v-if="pageData?.content" class="prose prose-lg dark:prose-invert max-w-none">
      <div v-html="formatContent(pageData.content)"></div>
    </div>

    <!-- CMS Blocks -->
    <div v-if="pageData?.blocks && pageData.blocks.length" class="space-y-12">
      <component
        v-for="block in pageData.blocks"
        :key="block.id"
        :is="getBlockComponent(block.type)"
        :block="block"
      />
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <!-- Error State -->
    <div v-if="error && !loading" class="text-center py-20">
      <p class="text-red-500 dark:text-red-400">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { cmsService } from '../../services/api/cmsService';
import { useI18n } from '../../composables/useI18n';
import HeroBlock from './blocks/HeroBlock.vue';
import FeaturesBlock from './blocks/FeaturesBlock.vue';
import TestimonialsBlock from './blocks/TestimonialsBlock.vue';
import CTABlock from './blocks/CTABlock.vue';
import ContentBlock from './blocks/ContentBlock.vue';

const props = defineProps({
  slug: {
    type: String,
    required: true,
  },
});

const route = useRoute();
const { locale } = useI18n();

const pageData = ref(null);
const loading = ref(false);
const error = ref(null);

function getBlockComponent(type) {
  const components = {
    hero: HeroBlock,
    features: FeaturesBlock,
    testimonials: TestimonialsBlock,
    cta: CTABlock,
    content: ContentBlock,
  };
  return components[type] || ContentBlock;
}

function formatContent(content) {
  if (!content) return '';
  // Convert line breaks to <br> tags
  return content.replace(/\n/g, '<br>');
}

async function loadPage() {
  try {
    loading.value = true;
    error.value = null;
    const response = await cmsService.getPublicPage(props.slug, locale.value);
    pageData.value = response.data || response;
  } catch (err) {
    // 404 is expected when page doesn't exist - don't log it as an error
    if (err.response?.status === 404) {
      error.value = 'Page not found';
      // Don't log 404 errors for public pages as they're expected
    } else {
      console.error('Error loading page:', err);
      error.value = 'Failed to load page';
    }
  } finally {
    loading.value = false;
  }
}

onMounted(loadPage);

// Reload page when locale changes
watch(() => locale.value, () => {
  if (props.slug) {
    loadPage();
  }
});
</script>

