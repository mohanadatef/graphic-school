import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import i18n from './i18n';
import ToastContainer from './components/common/ToastContainer.vue';
import { useBrandingStore } from './stores/branding';
import { setupSelfHealingRouter } from './router/selfHealRouter';
import './style.css';

// Initialize self-healing system (only in development, not in tests)
if (typeof window !== 'undefined' && import.meta.env.DEV && !window.Cypress) {
  try {
    setupSelfHealingRouter(router);
  } catch (error) {
    console.warn('[Self-Heal] Failed to initialize:', error.message);
  }
}

// Initialize theme immediately
if (typeof window !== 'undefined') {
  const savedTheme = localStorage.getItem('gs_theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const theme = savedTheme === 'dark' || savedTheme === 'light' 
    ? savedTheme 
    : (prefersDark ? 'dark' : 'light');
  
  if (theme === 'dark') {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
}

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.use(i18n);
app.component('ToastContainer', ToastContainer);

// Load branding and translations BEFORE mounting app
async function initializeApp() {
  try {
    // 1. Load branding
    const brandingStore = useBrandingStore();
    await brandingStore.fetchBranding();
    brandingStore.applyBrandingToDOM();
    
    // 2. Load translations dynamically
    const savedLocale = localStorage.getItem('locale') || 'ar';
    const { loadDynamicTranslations } = await import('./i18n');
    await loadDynamicTranslations(savedLocale);
    
    // 3. Set document direction and language
    const isRTL = savedLocale === 'ar';
    document.documentElement.dir = isRTL ? 'rtl' : 'ltr';
    document.documentElement.lang = savedLocale;
    if (isRTL) {
      document.body.classList.add('rtl');
      document.body.classList.remove('ltr');
    } else {
      document.body.classList.add('ltr');
      document.body.classList.remove('rtl');
    }
  } catch (error) {
    console.error('Failed to initialize app, using defaults:', error);
    // App will still mount with defaults
  } finally {
    app.mount('#app');
  }
}

initializeApp();
