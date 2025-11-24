import { ref, computed } from 'vue';
import { useLocale } from './useLocale';
import api from '../api';

// Currency configuration
const currency = ref('EGP');
const currencySymbol = ref('ج.م');
const currencyPosition = ref('after'); // 'before' or 'after'
const loading = ref(false);

// Cache for API availability check (prevents repeated 404s)
let apiChecked = false;
let apiAvailable = null;

// Initialize currency from settings
async function initCurrency() {
  if (typeof window === 'undefined') return;
  
  try {
    loading.value = true;
    // Try to get from localStorage first (for performance)
    const savedCurrency = localStorage.getItem('gs_currency');
    const savedSymbol = localStorage.getItem('gs_currency_symbol');
    const savedPosition = localStorage.getItem('gs_currency_position');
    
    if (savedCurrency && savedSymbol && savedPosition) {
      currency.value = savedCurrency;
      currencySymbol.value = savedSymbol;
      currencyPosition.value = savedPosition;
    }
    
    // Check if API endpoint is available (cache per session)
    if (!apiChecked) {
      const cached = sessionStorage.getItem('currencyApiAvailable');
      if (cached === 'false') {
        apiAvailable = false;
        apiChecked = true;
      } else if (cached === 'true') {
        apiAvailable = true;
        apiChecked = true;
      }
    }
    
    // Skip API call if we know it's not available
    if (apiAvailable === false) {
      return;
    }
    
    // Fetch from API (silently fail if endpoint doesn't exist)
    try {
      const response = await api.get('/system-settings/public');
      if (response && response.data) {
        const data = response.data;
        currency.value = data.default_currency || 'EGP';
        currencySymbol.value = data.currency_symbol || getDefaultSymbol(currency.value);
        currencyPosition.value = data.currency_position || 'after';
        
        // Save to localStorage
        localStorage.setItem('gs_currency', currency.value);
        localStorage.setItem('gs_currency_symbol', currencySymbol.value);
        localStorage.setItem('gs_currency_position', currencyPosition.value);
        
        // Mark API as available
        apiAvailable = true;
        sessionStorage.setItem('currencyApiAvailable', 'true');
      }
    } catch (error) {
      // Silently handle 404 and other errors - endpoint may not exist
      if (error.response?.status === 404 || error.response?.status === 500) {
        apiAvailable = false;
        sessionStorage.setItem('currencyApiAvailable', 'false');
      }
      // Use localStorage values or defaults (already set above)
    }
  } catch (error) {
    // Use defaults if something unexpected happens
    if (!currency.value || currency.value === 'EGP') {
      currency.value = 'EGP';
      currencySymbol.value = 'ج.م';
      currencyPosition.value = 'after';
    }
  } finally {
    loading.value = false;
    apiChecked = true;
  }
}

// Get default symbol for currency
function getDefaultSymbol(curr) {
  const symbols = {
    'EGP': 'ج.م',
    'SAR': 'ر.س',
    'AED': 'د.إ',
    'KWD': 'د.ك',
    'BHD': 'د.ب',
    'OMR': 'ر.ع',
    'QAR': 'ر.ق',
    'USD': '$',
  };
  return symbols[curr] || curr;
}

// Format currency value
function formatCurrency(value, options = {}) {
  const { locale } = useLocale();
  
  const amount = typeof value === 'number' ? value : parseFloat(value) || 0;
  
  // Use Intl.NumberFormat for proper formatting
  const localeCode = locale.value === 'ar' ? 'ar-EG' : 'en-US';
  
  // Format number with proper locale
  const formattedNumber = new Intl.NumberFormat(localeCode, {
    minimumFractionDigits: options.minimumFractionDigits ?? 0,
    maximumFractionDigits: options.maximumFractionDigits ?? 2,
  }).format(amount);
  
  // Add currency symbol based on position
  if (currencyPosition.value === 'before') {
    return `${currencySymbol.value} ${formattedNumber}`;
  } else {
    return `${formattedNumber} ${currencySymbol.value}`;
  }
}

// Format currency with Intl.NumberFormat (alternative method)
function formatCurrencyIntl(value, options = {}) {
  const { locale } = useLocale();
  
  const amount = typeof value === 'number' ? value : parseFloat(value) || 0;
  
  // Map currency to locale-specific currency code
  const currencyMap = {
    'EGP': 'EGP',
    'SAR': 'SAR',
    'AED': 'AED',
    'KWD': 'KWD',
    'BHD': 'BHD',
    'OMR': 'OMR',
    'QAR': 'QAR',
    'USD': 'USD',
  };
  
  const currencyCode = currencyMap[currency.value] || 'EGP';
  const localeCode = locale.value === 'ar' ? 'ar-EG' : 'en-US';
  
  return new Intl.NumberFormat(localeCode, {
    style: 'currency',
    currency: currencyCode,
    minimumFractionDigits: options.minimumFractionDigits ?? 0,
    maximumFractionDigits: options.maximumFractionDigits ?? 2,
  }).format(amount);
}

// Initialize on module load (if in browser)
if (typeof window !== 'undefined') {
  initCurrency();
}

export function useCurrency() {
  return {
    currency: computed(() => currency.value),
    currencySymbol: computed(() => currencySymbol.value),
    currencyPosition: computed(() => currencyPosition.value),
    loading: computed(() => loading.value),
    formatCurrency,
    formatCurrencyIntl,
    initCurrency,
    getDefaultSymbol,
  };
}

