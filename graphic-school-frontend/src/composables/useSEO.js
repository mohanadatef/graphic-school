import { onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useBrandingStore } from '../stores/branding';
import { setMetaTags, setStructuredData, setCourseStructuredData } from '../utils/seo';

/**
 * SEO Composable
 * Manages SEO meta tags and structured data
 */
export function useSEO() {
  const route = useRoute();
  const brandingStore = useBrandingStore();

  function updateSEO(meta) {
    setMetaTags({
      ...meta,
      siteName: brandingStore.displayName,
      url: window.location.href,
    });
  }

  function updateCourseSEO(course) {
    updateSEO({
      title: course.title,
      description: course.description,
      image: course.image_path,
      type: 'course',
    });
    
    setCourseStructuredData(course);
  }

  // Watch route changes and update SEO
  watch(
    () => route.path,
    () => {
      // Default SEO
      updateSEO({
        title: `${brandingStore.displayName} - منصة تعليم التصميم الجرافيكي`,
        description: 'منصة تفاعلية لتعلم التصميم الجرافيكي مع أفضل المدربين',
        keywords: ['تصميم جرافيكي', 'تعليم', 'كورسات', 'Graphic Design'],
      });
    },
    { immediate: true }
  );

  return {
    updateSEO,
    updateCourseSEO,
  };
}

