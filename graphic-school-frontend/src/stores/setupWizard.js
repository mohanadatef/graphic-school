import { defineStore } from 'pinia';
import { ref, reactive } from 'vue';
import api from '../api';

export const useSetupWizardStore = defineStore('setupWizard', () => {
  const currentStep = ref(0);
  const loading = ref(false);
  const error = ref(null);
  const isActivated = ref(null); // null = not checked yet
  const shouldRunSetup = ref(null); // null = not checked yet

  // Form data for all steps
  const formData = reactive({
    general: {
      academy_name: '',
      country: '',
      default_language: 'en',
      timezone: 'UTC',
      default_currency: 'USD',
    },
    branding: {
      logo: null,
      primary_color: '#3b82f6',
      secondary_color: '#6366f1',
      font_main: 'Cairo',
      font_headings: 'Poppins',
      default_theme: 'light',
    },
    pages: {
      homepage_template: 'template-a',
      enabled_pages: {
        home: true,
        about: true,
        contact: true,
        programs: true,
        community: true,
        faq: false,
      },
    },
    email: {
      smtp_host: '',
      smtp_port: '587',
      smtp_username: '',
      smtp_password: '',
      smtp_encryption: 'tls',
    },
    payment: {
      stripe_key: '',
      stripe_secret: '',
      paymob_api_key: '',
      paytabs_secret: '',
    },
  });

  /**
   * Load setup status from API
   */
  async function loadStatus() {
    try {
      loading.value = true;
      error.value = null;
      const { data } = await api.get('/setup/status');
      
      isActivated.value = data.is_activated || false;
      shouldRunSetup.value = data.should_run_setup !== false;

      // Load existing settings if available
      if (data.settings) {
        if (data.settings.general_info) {
          formData.general.academy_name = data.settings.general_info.academy_name || '';
          formData.general.country = data.settings.general_info.country || '';
        }
        if (data.settings.default_language) {
          formData.general.default_language = data.settings.default_language;
        }
        if (data.settings.default_currency) {
          formData.general.default_currency = data.settings.default_currency;
        }
        if (data.settings.timezone) {
          formData.general.timezone = data.settings.timezone;
        }
        if (data.settings.branding) {
          Object.assign(formData.branding, data.settings.branding);
        }
        if (data.settings.enabled_pages) {
          formData.pages.enabled_pages = { ...formData.pages.enabled_pages, ...data.settings.enabled_pages };
        }
        if (data.settings.email_settings) {
          Object.assign(formData.email, data.settings.email_settings);
        }
        if (data.settings.payment_settings) {
          Object.assign(formData.payment, data.settings.payment_settings);
        }
      }

      return data;
    } catch (err) {
      error.value = err.message || 'Failed to load setup status';
      console.error('Error loading setup status:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Save step data
   */
  async function saveStep(stepNumber, payload) {
    try {
      loading.value = true;
      error.value = null;
      
      const { data } = await api.post(`/admin/setup/save-step/${stepNumber}`, payload);
      
      return data;
    } catch (err) {
      error.value = err.message || 'Failed to save step';
      console.error('Error saving step:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Complete setup
   */
  async function completeSetup() {
    try {
      loading.value = true;
      error.value = null;

      const payload = {
        general_info: {
          academy_name: formData.general.academy_name,
          country: formData.general.country,
        },
        default_language: formData.general.default_language,
        default_currency: formData.general.default_currency,
        timezone: formData.general.timezone,
        branding: formData.branding,
        enabled_pages: formData.pages.enabled_pages,
        homepage_template: formData.pages.homepage_template,
        email_settings: formData.email,
        payment_settings: formData.payment,
      };

      const { data } = await api.post('/admin/setup/complete', payload);
      
      isActivated.value = true;
      shouldRunSetup.value = false;
      
      return data;
    } catch (err) {
      error.value = err.message || 'Failed to complete setup';
      console.error('Error completing setup:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Activate default website
   */
  async function activateDefault() {
    try {
      loading.value = true;
      error.value = null;

      const { data } = await api.post('/admin/setup/activate-default');
      
      isActivated.value = true;
      shouldRunSetup.value = false;
      
      return data;
    } catch (err) {
      error.value = err.message || 'Failed to activate default website';
      console.error('Error activating default:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Test email configuration
   */
  async function testEmail(email) {
    try {
      loading.value = true;
      error.value = null;

      const { data } = await api.post('/admin/setup/test-email', { email });
      
      return data;
    } catch (err) {
      error.value = err.message || 'Failed to send test email';
      console.error('Error testing email:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Set current step
   */
  function setStep(step) {
    currentStep.value = step;
  }

  /**
   * Next step
   */
  function nextStep() {
    if (currentStep.value < 5) {
      currentStep.value++;
    }
  }

  /**
   * Previous step
   */
  function previousStep() {
    if (currentStep.value > 0) {
      currentStep.value--;
    }
  }

  return {
    // State
    currentStep,
    loading,
    error,
    isActivated,
    shouldRunSetup,
    formData,
    
    // Actions
    loadStatus,
    saveStep,
    completeSetup,
    activateDefault,
    testEmail,
    setStep,
    nextStep,
    previousStep,
  };
});

