import { ref, computed } from 'vue';

// Create a reactive theme state
const theme = ref('light');

// Initialize theme from localStorage or system preference
function initTheme() {
  if (typeof window === 'undefined') return;
  
  const savedTheme = localStorage.getItem('gs_theme');
  if (savedTheme === 'dark' || savedTheme === 'light') {
    theme.value = savedTheme;
  } else {
    // Check system preference
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    theme.value = prefersDark ? 'dark' : 'light';
  }
  applyTheme(theme.value);
}

// Apply theme to document
function applyTheme(newTheme) {
  if (typeof document === 'undefined') return;
  
  const root = document.documentElement;
  if (newTheme === 'dark') {
    root.classList.add('dark');
  } else {
    root.classList.remove('dark');
  }
  if (typeof localStorage !== 'undefined') {
    localStorage.setItem('gs_theme', newTheme);
  }
}

// Toggle theme
function toggleTheme() {
  theme.value = theme.value === 'dark' ? 'light' : 'dark';
  applyTheme(theme.value);
}

// Set theme explicitly
function setTheme(newTheme) {
  if (newTheme === 'dark' || newTheme === 'light') {
    theme.value = newTheme;
    applyTheme(newTheme);
  }
}

// Watch for system theme changes
if (typeof window !== 'undefined') {
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
  mediaQuery.addEventListener('change', (e) => {
    // Only auto-switch if user hasn't manually set a preference
    if (!localStorage.getItem('gs_theme')) {
      theme.value = e.matches ? 'dark' : 'light';
      applyTheme(theme.value);
    }
  });
  
  // Initialize on load
  initTheme();
}

export function useTheme() {
  return {
    theme: computed(() => theme.value),
    toggleTheme,
    setTheme,
    isDark: computed(() => theme.value === 'dark'),
    isLight: computed(() => theme.value === 'light'),
  };
}

