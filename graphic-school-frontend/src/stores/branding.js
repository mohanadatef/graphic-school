import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../services/api/client';

export const useBrandingStore = defineStore('branding', () => {
  const branding = ref(null);
  const loading = ref(false);
  const error = ref(null);
  const isLoaded = computed(() => !!branding.value && !loading.value);

  /**
   * Fetch branding settings from API
   */
  async function fetchBranding() {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await api.get('/branding/frontend');
      branding.value = response.data;
      applyBrandingToDOM(response.data);
      return response.data;
    } catch (err) {
      error.value = err.message || 'Failed to load branding';
      console.error('Branding fetch error:', err);
      // Use defaults if API fails
      applyDefaultBranding();
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Apply branding to DOM via CSS variables
   */
  function applyBrandingToDOM(brandingData) {
    if (!brandingData || typeof document === 'undefined') return;

    const root = document.documentElement;

    // Set CSS variables
    if (brandingData['branding.colors.primary']) {
      root.style.setProperty('--primary', brandingData['branding.colors.primary']);
    }
    if (brandingData['branding.colors.secondary']) {
      root.style.setProperty('--secondary', brandingData['branding.colors.secondary']);
    }
    if (brandingData['branding.colors.background']) {
      root.style.setProperty('--background', brandingData['branding.colors.background']);
    }
    if (brandingData['branding.colors.text']) {
      root.style.setProperty('--text-color', brandingData['branding.colors.text']);
    }

    // Handle fonts based on source
    const fontSource = brandingData['branding.fonts.source'] || brandingData.fonts?.source || 'system';
    const fontMain = brandingData['branding.fonts.main'] || brandingData.fonts?.main || 'Inter';
    const fontHeadings = brandingData['branding.fonts.headings'] || brandingData.fonts?.headings || 'Inter';
    const customFontUrl = brandingData.fonts?.custom_file_url || brandingData['branding.fonts.custom_file'];

    if (fontSource === 'custom' && customFontUrl) {
      // Load custom font
      loadCustomFont(customFontUrl);
      root.style.setProperty('--font-main', '"CustomFont", sans-serif');
      root.style.setProperty('--font-headings', '"CustomFont", sans-serif');
    } else {
      // Load system fonts
      root.style.setProperty('--font-main', `"${fontMain}", sans-serif`);
      root.style.setProperty('--font-headings', `"${fontHeadings}", sans-serif`);
      loadFont(fontMain);
      if (fontMain !== fontHeadings) {
        loadFont(fontHeadings);
      }
    }

    if (brandingData['branding.layout.radius']) {
      root.style.setProperty('--radius', brandingData['branding.layout.radius']);
    }
    if (brandingData['branding.layout.shadow']) {
      root.style.setProperty('--shadow-level', brandingData['branding.layout.shadow']);
    }

    // Update favicon
    if (brandingData['branding.logo.favicon']) {
      updateFavicon(brandingData['branding.logo.favicon']);
    }

    // Update page title
    if (brandingData['branding.name.display']) {
      document.title = brandingData['branding.name.display'];
    }
  }

  /**
   * Load Google Font dynamically
   */
  function loadFont(fontName) {
    if (typeof document === 'undefined') return;
    
    // Check if font is already loaded
    const existingLink = document.querySelector(`link[data-font="${fontName}"]`);
    if (existingLink) return;

    // Extended font map for Google Fonts
    const fontMap = {
      'Inter': 'Inter:wght@400;500;600;700',
      'Roboto': 'Roboto:wght@400;500;700',
      'Poppins': 'Poppins:wght@400;500;600;700',
      'Cairo': 'Cairo:wght@400;500;600;700',
      'Tajawal': 'Tajawal:wght@400;500;700',
      'IBM Plex Sans Arabic': 'IBM+Plex+Sans+Arabic:wght@400;500;600;700',
      'Noto Sans Arabic': 'Noto+Sans+Arabic:wght@400;500;600;700',
      'Almarai': 'Almarai:wght@400;700;800',
      'Open Sans': 'Open+Sans:wght@400;600;700',
      'Lato': 'Lato:wght@400;700',
      'Montserrat': 'Montserrat:wght@400;500;600;700',
      'Source Sans Pro': 'Source+Sans+Pro:wght@400;600;700',
      'Fira Sans': 'Fira+Sans:wght@400;500;600;700',
      'Space Grotesk': 'Space+Grotesk:wght@400;500;600;700',
      'Nunito': 'Nunito:wght@400;600;700',
      'Work Sans': 'Work+Sans:wght@400;500;600;700',
      'Plus Jakarta Sans': 'Plus+Jakarta+Sans:wght@400;500;600;700',
      'Rubik': 'Rubik:wght@400;500;600;700',
      'Raleway': 'Raleway:wght@400;500;600;700',
      'Ubuntu': 'Ubuntu:wght@400;500;700',
      'Playfair Display': 'Playfair+Display:wght@400;600;700',
      'Merriweather': 'Merriweather:wght@400;700',
      'DM Sans': 'DM+Sans:wght@400;500;600;700',
      'Manrope': 'Manrope:wght@400;500;600;700',
      'Outfit': 'Outfit:wght@400;500;600;700',
      'Chivo': 'Chivo:wght@400;700',
      'Sora': 'Sora:wght@400;500;600;700',
      'Vazirmatn': 'Vazirmatn:wght@400;500;600;700',
    };

    // Use mapped font or construct from font name
    let fontFamily = fontMap[fontName];
    if (!fontFamily) {
      // Convert font name to Google Fonts format
      fontFamily = fontName.replace(/\s+/g, '+') + ':wght@400;500;600;700';
    }

    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = `https://fonts.googleapis.com/css2?family=${fontFamily}&display=swap`;
    link.setAttribute('data-font', fontName);
    document.head.appendChild(link);
  }

  /**
   * Load custom font via @font-face
   */
  function loadCustomFont(fontUrl) {
    if (typeof document === 'undefined') return;

    // Check if custom font is already loaded
    const existingStyle = document.getElementById('custom-font-face');
    if (existingStyle) {
      // Update existing @font-face
      const format = fontUrl.endsWith('.woff2') ? 'woff2' : fontUrl.endsWith('.woff') ? 'woff' : 'truetype';
      existingStyle.textContent = `
        @font-face {
          font-family: 'CustomFont';
          src: url('${fontUrl}') format('${format}');
          font-display: swap;
        }
      `;
      return;
    }

    // Determine format from URL
    const format = fontUrl.endsWith('.woff2') ? 'woff2' : fontUrl.endsWith('.woff') ? 'woff' : 'truetype';

    // Create @font-face style
    const style = document.createElement('style');
    style.id = 'custom-font-face';
    style.textContent = `
      @font-face {
        font-family: 'CustomFont';
        src: url('${fontUrl}') format('${format}');
        font-display: swap;
      }
    `;
    document.head.appendChild(style);
  }

  /**
   * Update favicon
   */
  function updateFavicon(faviconUrl) {
    if (typeof document === 'undefined') return;

    // Remove existing favicon
    const existingFavicon = document.querySelector('link[rel="icon"]');
    if (existingFavicon) {
      existingFavicon.remove();
    }

    // Add new favicon
    const link = document.createElement('link');
    link.rel = 'icon';
    link.type = 'image/png';
    link.href = faviconUrl;
    document.head.appendChild(link);
  }

  /**
   * Apply default branding (fallback)
   */
  function applyDefaultBranding() {
    const defaults = {
      'branding.colors.primary': '#3b82f6',
      'branding.colors.secondary': '#0ea5e9',
      'branding.colors.background': '#ffffff',
      'branding.colors.text': '#111111',
      'branding.fonts.main': 'Inter',
      'branding.fonts.headings': 'Inter',
      'branding.layout.radius': '0.5rem',
      'branding.layout.shadow': '0',
      'branding.name.display': 'Graphic School',
    };
    applyBrandingToDOM(defaults);
  }

  /**
   * Get branding value by key
   */
  function get(key, defaultValue = null) {
    if (!branding.value) return defaultValue;
    return branding.value[key] ?? defaultValue;
  }

  /**
   * Get display name
   */
  const displayName = computed(() => {
    return get('branding.name.display', 'Graphic School');
  });

  /**
   * Get logo URL
   */
  const logoUrl = computed(() => {
    const isDark = document.documentElement.classList.contains('dark');
    const logoKey = isDark && get('branding.logo.dark') 
      ? 'branding.logo.dark' 
      : 'branding.logo.default';
    return get(logoKey);
  });

  /**
   * Get primary color
   */
  const primaryColor = computed(() => {
    return get('branding.colors.primary', '#3b82f6');
  });

  /**
   * Get secondary color
   */
  const secondaryColor = computed(() => {
    return get('branding.colors.secondary', '#0ea5e9');
  });

  return {
    branding,
    loading,
    error,
    isLoaded,
    displayName,
    logoUrl,
    primaryColor,
    secondaryColor,
    fetchBranding,
    get,
    applyBrandingToDOM,
  };
});

