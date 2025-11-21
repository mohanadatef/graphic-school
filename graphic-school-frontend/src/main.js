import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import i18n from './i18n';
import ToastContainer from './components/common/ToastContainer.vue';
import './style.css';

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

app.mount('#app');
