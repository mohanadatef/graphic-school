import { createApp } from 'vue';
import './style.css';
import App from './App.vue';
import router from './router';
import i18n from './i18n';

const app = createApp(App);
app.use(router);
app.use(i18n);

// Initialize locale after app is created
const savedLocale = localStorage.getItem('locale') || 'ar';
const isRTL = savedLocale === 'ar';
document.documentElement.dir = isRTL ? 'rtl' : 'ltr';
document.documentElement.lang = savedLocale;

// Listen for locale changes
window.addEventListener('locale-changed', (event) => {
  const { locale } = event.detail;
  if (i18n && i18n.global) {
    i18n.global.locale.value = locale;
  }
  const isRTLValue = locale === 'ar';
  document.documentElement.dir = isRTLValue ? 'rtl' : 'ltr';
  document.documentElement.lang = locale;
});

app.mount('#app');
