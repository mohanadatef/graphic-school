<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800 relative">
    <header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl shadow-lg sticky top-0 z-50 relative w-full border-b border-slate-200/60 dark:border-slate-700/60 transition-all duration-300" style="pointer-events: auto;">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-wrap items-center justify-between gap-4">
        <RouterLink to="/" class="flex items-center gap-3 group">
          <div v-if="settings.logo" class="h-14 w-14 rounded-2xl overflow-hidden shadow-lg group-hover:shadow-xl transition-all duration-300 transform group-hover:scale-105 ring-2 ring-slate-200/50 dark:ring-slate-700/50 group-hover:ring-primary/50">
            <img :src="settings.logo" alt="logo" class="h-full w-full object-cover" />
          </div>
          <div>
            <h1 class="text-xl font-extrabold bg-gradient-to-r from-slate-900 to-slate-700 dark:from-white dark:to-slate-200 bg-clip-text text-transparent group-hover:from-primary group-hover:to-primary/80 transition-all duration-300">
              {{ brandingStore.displayName }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ settings.address }}</p>
          </div>
        </RouterLink>
        <nav class="flex flex-wrap items-center gap-2 sm:gap-3 text-sm font-semibold relative z-40" style="pointer-events: auto;">
          <RouterLink
            v-if="enabledPages.home !== false"
            class="px-4 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-300 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20 shadow-md"
            to="/"
          >
            {{ $t('navigation.home') }}
            <span class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-primary rounded-full group-hover:w-3/4 transition-all duration-300"></span>
          </RouterLink>
          <RouterLink
            v-if="enabledPages.programs !== false"
            class="px-4 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-300 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20 shadow-md"
            to="/programs"
          >
            {{ $t('navigation.programs') || 'Programs' }}
            <span class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-primary rounded-full group-hover:w-3/4 transition-all duration-300"></span>
          </RouterLink>
          <RouterLink
            v-if="enabledPages.about !== false"
            class="px-4 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-300 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20 shadow-md"
            to="/about"
          >
            {{ $t('navigation.about') }}
            <span class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-primary rounded-full group-hover:w-3/4 transition-all duration-300"></span>
          </RouterLink>
          <RouterLink
            v-if="enabledPages.contact !== false"
            class="px-4 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-300 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20 shadow-md"
            to="/contact"
          >
            {{ $t('navigation.contact') }}
            <span class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-primary rounded-full group-hover:w-3/4 transition-all duration-300"></span>
          </RouterLink>
          <RouterLink
            v-if="enabledPages.community !== false"
            class="px-4 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-300 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20 shadow-md"
            to="/community"
          >
            {{ $t('navigation.community') || 'Community' }}
            <span class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-primary rounded-full group-hover:w-3/4 transition-all duration-300"></span>
          </RouterLink>
          <RouterLink
            v-if="enabledPages.faq === true"
            class="px-4 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-300 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20 shadow-md"
            to="/faq"
          >
            {{ $t('navigation.faq') || 'FAQ' }}
            <span class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-primary rounded-full group-hover:w-3/4 transition-all duration-300"></span>
          </RouterLink>
          <div class="relative z-50 flex items-center gap-2" style="pointer-events: auto;">
            <div class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-300">
              <ThemeToggle />
            </div>
            <div class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-300">
              <LanguageSwitcher />
            </div>
          </div>
          <RouterLink 
            v-if="!authStore.isAuthenticated" 
            class="px-6 py-2.5 bg-gradient-to-r from-primary via-primary to-primary/90 text-white rounded-xl font-bold hover:shadow-xl hover:shadow-primary/30 hover:scale-105 transition-all duration-300 relative z-50 shadow-lg group/btn"
            style="pointer-events: auto; position: relative;"
            to="/login"
          >
            <span class="relative z-10 flex items-center gap-2">
              {{ $t('auth.login') }}
              <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </span>
          </RouterLink>
          <button
            v-else
            type="button"
            id="dashboard-button"
            class="px-6 py-2.5 bg-gradient-to-r from-slate-100 to-slate-50 dark:from-slate-800 dark:to-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold hover:from-slate-200 hover:to-slate-100 dark:hover:from-slate-700 dark:hover:to-slate-600 active:scale-95 transition-all duration-300 cursor-pointer relative z-50 shadow-md hover:shadow-lg flex items-center gap-2"
            style="pointer-events: auto; position: relative; touch-action: manipulation; -webkit-tap-highlight-color: transparent;"
            @click.stop.prevent="goToDashboard"
            @mousedown.stop.prevent="goToDashboard"
            @touchstart.stop.prevent="goToDashboard"
            @mouseup.stop
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            {{ $t('navigation.dashboard') }}
          </button>
        </nav>
      </div>
    </header>

    <main class="flex-1">
      <RouterView />
    </main>

    <footer class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 text-slate-100 mt-20 overflow-hidden">
      <!-- Background Decoration -->
      <div class="absolute inset-0 overflow-hidden opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500 rounded-full blur-3xl"></div>
      </div>
      
      <!-- Grid Pattern Overlay -->
      <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)] bg-[size:24px_24px]"></div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid gap-12 md:grid-cols-4 text-sm relative z-10">
        <!-- Brand Section -->
        <div class="md:col-span-2 space-y-6">
          <div class="flex items-center gap-4">
            <div v-if="settings.logo" class="h-16 w-16 rounded-2xl overflow-hidden ring-2 ring-primary/30 shadow-xl">
              <img :src="settings.logo" alt="logo" class="h-full w-full object-cover" />
            </div>
            <div>
              <h3 class="text-2xl font-extrabold bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent">
                {{ brandingStore.displayName }}
              </h3>
              <p class="text-xs text-slate-400 mt-1">أكاديمية التصميم الجرافيكي</p>
            </div>
          </div>
          <p class="text-slate-300 leading-relaxed max-w-md text-base">
            {{ settings.about_us || 'أكاديمية متخصصة في تصميم الجرافيك والبراندنج. نقدم تجربة تعليمية شاملة تجمع بين الإبداع والتكنولوجيا.' }}
          </p>
          
          <!-- Social Media Links -->
          <div class="flex items-center gap-4 pt-4">
            <a href="#" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-primary/20 backdrop-blur-md flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-primary/20 group">
              <svg class="w-5 h-5 text-slate-300 group-hover:text-primary transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-primary/20 backdrop-blur-md flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-primary/20 group">
              <svg class="w-5 h-5 text-slate-300 group-hover:text-primary transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-primary/20 backdrop-blur-md flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-primary/20 group">
              <svg class="w-5 h-5 text-slate-300 group-hover:text-primary transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.18.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-primary/20 backdrop-blur-md flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-primary/20 group">
              <svg class="w-5 h-5 text-slate-300 group-hover:text-primary transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
              </svg>
            </a>
          </div>
        </div>
        
        <!-- Contact Info -->
        <div class="space-y-6">
          <h3 class="font-bold text-lg text-white mb-6">معلومات التواصل</h3>
          <ul class="space-y-4 text-slate-300">
            <li class="flex items-start gap-3 group">
              <div class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/30 transition-colors duration-300">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <div>
                <p class="font-semibold text-white mb-1">الهاتف</p>
                <a :href="`tel:${settings.phone || '010000000'}`" class="hover:text-primary transition-colors duration-300">
                  {{ settings.phone || '010000000' }}
                </a>
              </div>
            </li>
            <li class="flex items-start gap-3 group">
              <div class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/30 transition-colors duration-300">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <p class="font-semibold text-white mb-1">البريد الإلكتروني</p>
                <a :href="`mailto:${settings.email || `info@${brandingStore.displayName.toLowerCase().replace(/\s/g, '')}.com`}`" class="hover:text-primary transition-colors duration-300 break-all">
                  {{ settings.email || `info@${brandingStore.displayName.toLowerCase().replace(/\s/g, '')}.com` }}
                </a>
              </div>
            </li>
            <li class="flex items-start gap-3 group">
              <div class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/30 transition-colors duration-300">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div>
                <p class="font-semibold text-white mb-1">العنوان</p>
                <p class="hover:text-primary transition-colors duration-300">
                  {{ settings.address || 'القاهرة - مصر' }}
                </p>
              </div>
            </li>
          </ul>
        </div>
        
        <!-- Quick Links -->
        <div class="space-y-6">
          <h3 class="font-bold text-lg text-white mb-6">روابط سريعة</h3>
          <ul class="space-y-3">
            <li>
              <RouterLink to="/courses" class="text-slate-300 hover:text-primary transition-all duration-300 flex items-center gap-2 group">
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
                الكورسات
              </RouterLink>
            </li>
            <li>
              <RouterLink to="/instructors" class="text-slate-300 hover:text-primary transition-all duration-300 flex items-center gap-2 group">
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
                المدربين
              </RouterLink>
            </li>
            <li>
              <RouterLink to="/about" class="text-slate-300 hover:text-primary transition-all duration-300 flex items-center gap-2 group">
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
                من نحن
              </RouterLink>
            </li>
            <li>
              <RouterLink to="/contact" class="text-slate-300 hover:text-primary transition-all duration-300 flex items-center gap-2 group">
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
                اتصل بنا
              </RouterLink>
            </li>
          </ul>
        </div>
      </div>
      
      <!-- Bottom Bar -->
      <div class="border-t border-slate-800/50 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-6">
          <p class="text-sm text-slate-400">
            جميع الحقوق محفوظة © {{ new Date().getFullYear() }} 
            <span class="text-primary font-semibold">{{ brandingStore.displayName }}</span>
          </p>
          <div class="flex items-center gap-6 text-sm">
            <a href="#" class="text-slate-400 hover:text-primary transition-colors duration-300 font-medium">
              سياسة الخصوصية
            </a>
            <a href="#" class="text-slate-400 hover:text-primary transition-colors duration-300 font-medium">
              شروط الاستخدام
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted, reactive, computed } from 'vue';
import { RouterLink, RouterView, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useBrandingStore } from '../../stores/branding';
import { useWebsiteSettingsStore } from '../../stores/websiteSettings';
import LanguageSwitcher from '../common/LanguageSwitcher.vue';
import ThemeToggle from '../common/ThemeToggle.vue';

const authStore = useAuthStore();
const brandingStore = useBrandingStore();
const websiteStore = useWebsiteSettingsStore();
const router = useRouter();

const settings = computed(() => websiteStore.settings?.general_info || {});
const enabledPages = computed(() => websiteStore.settings?.enabled_pages || {});

function goToDashboard(event) {
  // Prevent any default behavior
  if (event) {
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();
  }
  
  console.log('Dashboard button clicked', event);
  console.log('Auth store state:', {
    isAuthenticated: authStore.isAuthenticated,
    roleName: authStore.roleName,
    user: authStore.user,
    token: authStore.token ? 'exists' : 'missing',
  });
  
  // Force immediate execution
  setTimeout(async () => {
    try {
      // Check if user is authenticated
      if (!authStore.isAuthenticated) {
        console.warn('User not authenticated, redirecting to login');
        router.push('/login');
        return;
      }
      
      // If user data is missing or role is missing, try to load from localStorage first
      if (!authStore.user || !authStore.roleName) {
        // First, try to load from localStorage if available
        const savedUser = localStorage.getItem('gs_user') || localStorage.getItem('user');
        if (savedUser && !authStore.user) {
          try {
            const parsedUser = JSON.parse(savedUser);
            // Normalize role
            if (parsedUser.role && typeof parsedUser.role === 'object' && parsedUser.role.name) {
              parsedUser.role = parsedUser.role.name;
              parsedUser.role_name = parsedUser.role;
            }
            if (parsedUser.role && !parsedUser.role_name) {
              parsedUser.role_name = parsedUser.role;
            }
            // Use setSession to properly update the store
            if (authStore.setSession) {
              authStore.setSession(parsedUser, authStore.token);
            }
          } catch (e) {
            console.warn('Failed to parse saved user data:', e);
          }
        }
        
        // For public pages, prefer localStorage data over API calls
        // Only make API call if we have a token but no user data in localStorage
        // This indicates the token might be valid but user data needs refresh
        if (!authStore.user && authStore.token && !savedUser && authStore.fetchCurrentUser) {
          // Token exists but no localStorage data - try to fetch from API
          try {
            await authStore.fetchCurrentUser();
            // fetchCurrentUser handles 401 and 404 gracefully:
            // - 401: clears session silently (expected when token is invalid)
            // - 404: returns without clearing session (endpoint might not exist)
            // - Other errors: throws error
          } catch (error) {
            // Only handle unexpected errors (not 401 or 404, which are handled by fetchCurrentUser)
            if (error.response?.status !== 401 && error.response?.status !== 404) {
              console.warn('Failed to fetch user data:', error);
            }
          }
        }
        
        // If still no user data after all attempts and we have a token, clear invalid session
        if (!authStore.user && authStore.token && !savedUser) {
          // Token exists but no user data - token is invalid
          if (authStore.clearSession) {
            authStore.clearSession();
          }
          router.push('/login');
          return;
        }
      }
      
      // Get role from auth store
      const role = authStore.roleName || authStore.user?.role_name || authStore.user?.role?.name;
      
      console.log('User role:', role);
      
      // Validate role before using it
      const validRoles = ['admin', 'super_admin', 'instructor', 'student'];
      if (!role || !validRoles.includes(role)) {
        console.warn('Invalid or missing role for user, redirecting to login');
        router.push('/login');
        return;
      }
      
      // Navigate to dashboard based on role
      const dashboardPath = `/dashboard/${role}`;
      console.log('Navigating to:', dashboardPath);
      router.push(dashboardPath).catch(err => {
        console.error('Navigation error:', err);
        router.push('/login');
      });
    } catch (error) {
      console.error('Error navigating to dashboard:', error);
      router.push('/login');
    }
  }, 0);
}

onMounted(async () => {
  // Load website settings
  await websiteStore.loadSettings();
  
  // Load branding
  await brandingStore.fetchBranding();
  
  // Add direct event listener as fallback
  const dashboardButton = document.getElementById('dashboard-button');
  if (dashboardButton) {
    dashboardButton.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      console.log('Direct event listener triggered');
      goToDashboard(e);
    }, { capture: true });
    
    dashboardButton.addEventListener('mousedown', (e) => {
      e.preventDefault();
      e.stopPropagation();
      console.log('Direct mousedown listener triggered');
      goToDashboard(e);
    }, { capture: true });
  }
});
</script>

