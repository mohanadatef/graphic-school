<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-slate-900 dark:to-slate-800">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-slate-900 dark:text-white mb-2">
          {{ $t('setup.welcome') || 'Welcome to Graphic School' }}
        </h1>
        <p class="text-slate-600 dark:text-slate-400">
          {{ $t('setup.description') || 'Let\'s set up your academy in a few simple steps' }}
        </p>
      </div>

      <!-- Progress Bar -->
      <div class="max-w-2xl mx-auto mb-8">
        <div class="flex items-center justify-between mb-4">
          <div
            v-for="(step, index) in steps"
            :key="index"
            class="flex items-center"
            :class="{ 'flex-1': index < steps.length - 1 }"
          >
            <div
              class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors"
              :class="
                currentStep > index
                  ? 'bg-primary border-primary text-white'
                  : currentStep === index
                  ? 'bg-primary border-primary text-white'
                  : 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-500'
              "
            >
              <span v-if="currentStep > index">✓</span>
              <span v-else>{{ index + 1 }}</span>
            </div>
            <div
              v-if="index < steps.length - 1"
              class="flex-1 h-1 mx-2 transition-colors"
              :class="currentStep > index ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600'"
            ></div>
          </div>
        </div>
        <p class="text-center text-sm text-slate-600 dark:text-slate-400">
          {{ steps[currentStep].title }}
        </p>
      </div>

      <!-- Wizard Content -->
      <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-8">
          <!-- Step 1: General Information -->
          <WizardGeneral
            v-if="currentStep === 0"
            v-model="formData.general"
            @next="goToNextStep"
            @skip="activateDefault"
          />

          <!-- Step 2: Branding -->
          <WizardBranding
            v-if="currentStep === 1"
            v-model="formData.branding"
            @next="goToNextStep"
            @back="goToPreviousStep"
          />

          <!-- Step 3: Website Pages -->
          <WizardPages
            v-if="currentStep === 2"
            v-model="formData.pages"
            @next="goToNextStep"
            @back="goToPreviousStep"
          />

          <!-- Step 4: Email Setup -->
          <WizardEmail
            v-if="currentStep === 3"
            v-model="formData.email"
            @next="goToNextStep"
            @back="goToPreviousStep"
          />

          <!-- Step 5: Payment Setup -->
          <WizardPayment
            v-if="currentStep === 4"
            v-model="formData.payment"
            @next="goToNextStep"
            @back="goToPreviousStep"
          />

          <!-- Step 6: Launch -->
          <WizardLaunch
            v-if="currentStep === 5"
            :form-data="formData"
            @complete="completeSetup"
            @back="goToPreviousStep"
          />
        </div>

        <!-- Skip to Default -->
        <div class="text-center mt-6">
          <button
            @click="activateDefault"
            class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-sm underline"
          >
            {{ $t('setup.activateDefault') || 'Activate default website and skip setup' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from '../../composables/useToast';
import { useSetupWizardStore } from '../../stores/setupWizard';
import WizardGeneral from '../../components/setup/WizardGeneral.vue';
import WizardBranding from '../../components/setup/WizardBranding.vue';
import WizardPages from '../../components/setup/WizardPages.vue';
import WizardEmail from '../../components/setup/WizardEmail.vue';
import WizardPayment from '../../components/setup/WizardPayment.vue';
import WizardLaunch from '../../components/setup/WizardLaunch.vue';

const router = useRouter();
const toast = useToast();
const store = useSetupWizardStore();

const currentStep = computed(() => store.currentStep);
const steps = [
  { title: 'General Information', key: 'general' },
  { title: 'Branding', key: 'branding' },
  { title: 'Website Pages', key: 'pages' },
  { title: 'Email Setup', key: 'email' },
  { title: 'Payment Setup', key: 'payment' },
  { title: 'Launch', key: 'launch' },
];

const formData = computed(() => store.formData);

// Navigation
function goToNextStep() {
  store.nextStep();
}

function goToPreviousStep() {
  store.previousStep();
}

// Activate default website
async function activateDefault() {
  try {
    await store.activateDefault();
    toast.success('Default website activated successfully');
    router.push('/');
  } catch (error) {
    console.error('Error activating default:', error);
    toast.error('Failed to activate default website');
  }
}

// Complete setup
async function completeSetup() {
  try {
    await store.completeSetup();
    toast.success('Setup completed successfully!');
    router.push('/');
  } catch (error) {
    console.error('Error completing setup:', error);
    toast.error('Failed to complete setup');
  }
}

onMounted(async () => {
  await store.loadStatus();
});
</script>

<style scoped>
/* Add any custom styles here */
</style>

