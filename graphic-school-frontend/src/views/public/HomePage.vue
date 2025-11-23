<template>
  <div>
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 text-white min-h-[90vh] flex items-center">
      <!-- Animated Background Elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-secondary/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 grid gap-12 lg:grid-cols-[1.1fr,0.9fr] items-center relative z-10">
        <div class="animate-fade-in-up">
          <div class="inline-flex items-center gap-2 glass-strong rounded-full px-5 py-2 text-sm mb-6 hover:scale-105 transition-transform duration-300">
            <span class="size-2.5 bg-emerald-400 rounded-full animate-pulse shadow-lg shadow-emerald-400/50"></span>
            <span class="font-semibold">بث مباشر مع المدربين هذا الأسبوع</span>
          </div>
          <h1 class="text-5xl md:text-6xl lg:text-7xl font-black leading-[1.1] mb-6 animate-fade-in-up" style="animation-delay: 0.1s;">
            <span class="block mb-2">منصة تفاعلية</span>
            <span class="block gradient-text">لتعلم التصميم الجرافيكي</span>
            <span class="block text-4xl md:text-5xl lg:text-6xl mt-2">خطوة بخطوة</span>
          </h1>
          <p class="text-slate-200 dark:text-slate-300 text-xl md:text-2xl leading-relaxed mb-10 max-w-2xl animate-fade-in-up" style="animation-delay: 0.2s;">
            دروس مباشرة، مراجعات فورية، ومتابعة شخصية من فريق جرافيك سكول. انضم إلى تجربة تعليمية ممتعة تجمع بين الإبداع،
            التكنولوجيا، والمجتمع.
          </p>
          <div class="flex flex-wrap gap-4 mb-12 animate-fade-in-up" style="animation-delay: 0.3s;">
            <RouterLink
              to="/courses"
              class="group relative inline-flex items-center gap-3 px-10 py-5 bg-white text-slate-900 font-bold rounded-2xl hover:-translate-y-2 hover:shadow-2xl transition-all duration-500 shadow-xl shadow-white/30 overflow-hidden"
            >
              <span class="relative z-10">استكشف الكورسات</span>
              <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform duration-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
              <div class="absolute inset-0 bg-gradient-to-r from-primary/10 to-secondary/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            </RouterLink>
            <RouterLink
              to="/about"
              class="px-10 py-5 border-2 border-white/40 rounded-2xl text-white font-bold hover:bg-white/10 hover:border-white/80 hover:scale-105 transition-all duration-500 backdrop-blur-md glass"
            >
              لماذا جرافيك سكول؟
            </RouterLink>
          </div>
          <div class="grid gap-4 sm:grid-cols-3 animate-fade-in-up" style="animation-delay: 0.4s;">
            <div class="group rounded-2xl glass-strong p-6 border border-white/10 hover:border-white/30 transition-all duration-500 hover:scale-105 cursor-default">
              <p class="text-4xl font-black mb-2 bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent">{{ heroStats.learners }}</p>
              <p class="text-sm text-slate-300 font-medium">طلاب نشطون</p>
            </div>
            <div class="group rounded-2xl glass-strong p-6 border border-white/10 hover:border-white/30 transition-all duration-500 hover:scale-105 cursor-default">
              <p class="text-4xl font-black mb-2 bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent">{{ heroStats.liveSessions }}</p>
              <p class="text-sm text-slate-300 font-medium">جلسات مباشرة</p>
            </div>
            <div class="group rounded-2xl glass-strong p-6 border border-white/10 hover:border-white/30 transition-all duration-500 hover:scale-105 cursor-default">
              <p class="text-4xl font-black mb-2 bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent">{{ heroStats.projects }}</p>
              <p class="text-sm text-slate-300 font-medium">مشاريع منجزة</p>
            </div>
          </div>
        </div>

        <div class="relative animate-scale-in" style="animation-delay: 0.5s;">
          <div class="absolute -top-8 -left-8 w-32 h-32 bg-primary/30 blur-3xl rounded-full animate-pulse-glow"></div>
          <div class="absolute -bottom-10 -right-6 w-40 h-40 bg-secondary/40 blur-3xl rounded-full animate-pulse-glow" style="animation-delay: 1s;"></div>
          <div v-if="homeData.sections.slider" class="relative glass-strong rounded-3xl overflow-hidden shadow-2xl hover:shadow-[0_40px_80px_rgba(0,0,0,0.3)] transition-all duration-700 border-2 border-white/20">
            <div v-if="loading.home" class="h-[360px] flex items-center justify-center">
              <div class="size-16 rounded-full border-4 border-white/20 border-t-white animate-spin"></div>
            </div>
            <div v-else-if="homeData.sliders.length" class="relative h-[360px]">
              <transition name="fade" mode="out-in">
                <div :key="activeSlide" class="absolute inset-0">
                  <img
                    class="h-full w-full object-cover"
                    :src="homeData.sliders[activeSlide]?.image_path"
                    :alt="homeData.sliders[activeSlide]?.title || 'برومو الأكاديمية'"
                  />
                  <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/90 to-transparent p-6">
                    <p class="text-primary text-sm font-semibold mb-1">{{ homeData.sliders[activeSlide]?.subtitle }}</p>
                    <h3 class="text-2xl font-bold">{{ homeData.sliders[activeSlide]?.title }}</h3>
                    <p class="text-sm text-slate-200 mt-1 line-clamp-2">{{ homeData.sliders[activeSlide]?.description }}</p>
                  </div>
                </div>
              </transition>
              <div class="absolute top-4 right-4 flex gap-2 z-20">
                <button
                  v-for="(_, idx) in homeData.sliders"
                  :key="idx"
                  class="size-3 rounded-full transition-all duration-300 hover:scale-125"
                  :class="idx === activeSlide ? 'bg-white shadow-lg shadow-white/50 w-8' : 'bg-white/40 hover:bg-white/60'"
                  @click="activeSlide = idx"
                  :aria-label="`Slide ${idx + 1}`"
                />
              </div>
            </div>
            <div v-else class="h-[360px] flex flex-col items-center justify-center text-white/80 px-8 text-center">
              <p class="text-lg font-semibold mb-2">أضف أول بانر لك</p>
              <p class="text-sm text-white/70">يمكنك إدارة البنرات من لوحة التحكم > الإعدادات > البنرات.</p>
            </div>
          </div>
          <div class="mt-6 flex items-center justify-between text-sm">
            <div class="flex items-center gap-3 glass rounded-full px-4 py-2">
              <span class="size-2.5 rounded-full bg-emerald-400 animate-pulse shadow-lg shadow-emerald-400/50"></span>
              <span class="font-semibold text-slate-200">{{ heroStats.reviews }} تقييم جديد هذا الشهر</span>
            </div>
            <RouterLink 
              class="group inline-flex items-center gap-2 px-4 py-2 glass rounded-full hover:bg-white/20 transition-all duration-300 font-medium"
              to="/contact"
            >
              <span>احجز استشارة مجانية</span>
              <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </RouterLink>
          </div>
        </div>
      </div>
      <div class="hero-orb hero-orb--one"></div>
      <div class="hero-orb hero-orb--two"></div>
    </section>

    <section v-if="homeData.sections.statistics" class="max-w-6xl mx-auto px-4 py-12">
        <div class="grid gap-6 md:grid-cols-3">
        <article
          v-for="(card, index) in resolvedHighlightCards"
          :key="card.title"
          class="group relative card-premium p-8 hover-lift overflow-hidden animate-fade-in-up"
          :style="{ animationDelay: `${index * 0.1}s` }"
        >
          <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-primary/10 via-primary/5 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-secondary/5 to-transparent rounded-tr-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-6">
              <div class="size-20 rounded-2xl bg-gradient-to-br from-primary/20 via-primary/10 to-primary/5 dark:from-primary/30 dark:via-primary/20 dark:to-primary/10 flex items-center justify-center text-4xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-xl">
                {{ card.icon }}
              </div>
              <span class="text-xs px-4 py-2 rounded-full bg-gradient-to-r from-primary/15 to-primary/10 dark:from-primary/25 dark:to-primary/15 text-primary font-bold border-2 border-primary/30 dark:border-primary/40 shadow-md">
                {{ card.badge }}
              </span>
            </div>
            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wider">{{ card.title }}</p>
            <p class="text-5xl font-black mb-3 group-hover:scale-110 transition-transform duration-500">
              <span class="gradient-text">{{ card.value }}</span>
            </p>
            <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 mb-5 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
              {{ card.trend }}
            </p>
            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ card.description }}</p>
          </div>
        </article>
      </div>
    </section>

    <section v-if="homeData.sections.featured_courses" class="bg-white dark:bg-slate-900 py-14">
      <div class="max-w-6xl mx-auto px-4">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
          <div>
            <p class="text-sm text-primary font-semibold">محتوى حديث</p>
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white">أحدث الكورسات التفاعلية</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-1">يتم تحديث المحتوى أسبوعياً بناءً على احتياجات الطلاب.</p>
          </div>
          <RouterLink to="/courses" class="text-sm text-primary font-medium">
            عرض كل الكورسات
          </RouterLink>
        </div>
        <div class="grid gap-6 lg:grid-cols-3">
          <template v-if="!loading.home && topCourses.length">
            <article
              v-for="course in topCourses"
              :key="course.id"
              class="group flex flex-col rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 overflow-hidden"
            >
              <div class="relative h-48 mb-6 rounded-xl overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                <div v-if="course.image_path" class="absolute inset-0">
                  <img :src="course.image_path" :alt="course.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                </div>
                <div class="absolute top-4 right-4">
                  <span class="px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-sm text-xs font-bold text-primary shadow-md">
                    {{ course.category?.name || 'تصميم' }}
                  </span>
                </div>
              </div>
              <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-primary transition-colors duration-200">{{ course.title }}</h3>
              <p class="text-sm text-slate-600 dark:text-slate-300 flex-1 leading-relaxed line-clamp-3 mb-4">
                {{ course.description || 'برنامج شامل لتعلم أدوات Adobe مع مشاريع عملية.' }}
              </p>
              <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-200">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ course.session_count || 0 }} جلسة
                </div>
                <span class="text-xl font-bold text-primary">{{ course.price ? `${course.price} ج.م` : 'مجاناً' }}</span>
              </div>
              <RouterLink
                :to="`/courses/${course.id}`"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-primary/90 text-white px-6 py-3 text-sm font-semibold hover:shadow-lg hover:scale-105 transition-all duration-200"
              >
                تفاصيل الكورس
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </RouterLink>
            </article>
          </template>
          <div v-else-if="loading.home" class="lg:col-span-3 grid gap-6 lg:grid-cols-3 animate-pulse">
            <div v-for="n in 3" :key="n" class="rounded-3xl border border-slate-100 bg-white p-5 h-64"></div>
          </div>
          <p v-else class="lg:col-span-3 text-center text-slate-500 py-10">
            لا توجد كورسات منشورة حالياً.
          </p>
        </div>
        <div class="mt-16 grid gap-6 md:grid-cols-4">
          <div 
            v-for="(item, index) in learningPillars" 
            :key="item.title" 
            class="group rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800 dark:to-slate-900 p-6 hover:border-primary/50 hover:shadow-xl transition-all duration-500 hover:-translate-y-2"
            :style="{ animationDelay: `${index * 0.1}s` }"
          >
            <div class="size-12 rounded-xl bg-gradient-to-br from-primary/20 to-primary/10 dark:from-primary/30 dark:to-primary/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p class="text-primary font-bold mb-2 text-base">{{ item.title }}</p>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ item.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-primary/5 via-transparent to-secondary/5"></div>
      <div class="grid lg:grid-cols-[1.05fr,0.95fr] gap-12 items-center relative z-10">
        <div class="card-premium p-10 animate-fade-in-up">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 dark:bg-primary/20 mb-6">
            <span class="size-2 rounded-full bg-primary animate-pulse"></span>
            <p class="text-primary text-sm font-bold">مجتمع متفاعل</p>
          </div>
          <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-6 leading-tight">
            <span class="block">تجربة تعليمية</span>
            <span class="gradient-text">ممتدة بعد انتهاء المحاضرة</span>
          </h2>
          <p class="text-lg text-slate-600 dark:text-slate-300 leading-relaxed mb-8">
            حصل على دعم مباشر من المدربين، وشارك تقدمك مع زملائك، واحصل على تقييمات عملية لكل مشروع.
          </p>
          <div class="space-y-5 mb-10">
            <div 
              v-for="(feature, index) in communityFeatures" 
              :key="feature.title" 
              class="flex gap-5 group hover:bg-slate-50 dark:hover:bg-slate-700/50 p-4 rounded-xl transition-all duration-300"
              :style="{ animationDelay: `${index * 0.1}s` }"
            >
              <div class="size-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/10 dark:from-primary/30 dark:to-primary/20 flex items-center justify-center text-3xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-lg">
                {{ feature.icon }}
              </div>
              <div class="flex-1">
                <p class="font-bold text-lg text-slate-900 dark:text-white mb-1">{{ feature.title }}</p>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ feature.description }}</p>
              </div>
            </div>
          </div>
          <RouterLink
            class="group/btn inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-700 dark:to-slate-600 text-white rounded-2xl font-bold hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden"
            to="/register"
          >
            <span class="relative z-10">انضم للمجتمع الآن</span>
            <svg class="w-5 h-5 group-hover/btn:translate-x-2 transition-transform duration-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
          </RouterLink>
        </div>

        <div class="rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 text-white p-8 relative overflow-hidden shadow-2xl animate-fade-in-up" style="animation-delay: 0.2s;">
          <div class="absolute inset-0 opacity-40 bg-[radial-gradient(circle_at_top,_#38bdf8,_transparent_50%)]"></div>
          <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-8">
              <div>
                <p class="text-sm text-slate-300 dark:text-slate-400 mb-2 font-medium">جلسات الأسبوع الحالي</p>
                <p class="text-4xl font-black bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent">
                  {{ upcomingSessions.length }} بث مباشر
                </p>
              </div>
              <RouterLink 
                class="group inline-flex items-center gap-2 px-4 py-2 glass rounded-xl hover:bg-white/20 transition-all duration-300 text-sm font-semibold"
                to="/dashboard/student/sessions"
              >
                <span>عرض الجدول</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </RouterLink>
            </div>
            <div v-if="upcomingSessions.length" class="space-y-3">
              <article
                v-for="session in upcomingSessions"
                :key="session.id"
                class="group rounded-2xl border-2 border-white/20 glass-strong px-5 py-4 hover:border-white/40 hover:bg-white/10 transition-all duration-300 hover:scale-105"
              >
                <div class="flex items-center justify-between mb-2">
                  <span class="font-bold text-lg">{{ session.courseTitle || session.title }}</span>
                  <span class="px-3 py-1 rounded-full glass text-xs font-semibold">{{ session.dateLabel }}</span>
                </div>
                <p v-if="session.timeLabel" class="text-sm text-slate-300 mb-2 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ session.timeLabel }}
                </p>
                <p class="text-xs text-emerald-300 font-medium flex items-center gap-2">
                  <span class="size-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                  {{ session.focus }}
                </p>
              </article>
            </div>
            <p v-else class="text-sm text-slate-400 text-center py-8">لا توجد جلسات مجدولة حالياً.</p>
            <p class="mt-8 text-xs text-slate-400 text-center">يمكنك متابعة الحضور والتسجيلات من لوحة التحكم الخاصة بك.</p>
          </div>
        </div>
      </div>
    </section>

    <section v-if="homeData.sections.testimonials" class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 text-white py-20 overflow-hidden">
      <div class="absolute inset-0">
        <div class="absolute top-1/4 left-0 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-0 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
      </div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-baseline justify-between gap-6 mb-12">
          <div class="animate-fade-in-up">
            <p class="text-sm text-primary font-bold mb-3 uppercase tracking-wider">أصوات الطلاب</p>
            <h2 class="text-4xl md:text-5xl font-black mb-4">
              <span class="block">ماذا يقول</span>
              <span class="gradient-text">مجتمعنا؟</span>
            </h2>
            <p class="text-slate-300 dark:text-slate-400 mt-2 text-lg">نستمع لكل تعليق ونطور المحتوى باستمرار.</p>
          </div>
          <RouterLink 
            class="group inline-flex items-center gap-2 px-6 py-3 glass rounded-xl hover:bg-white/20 transition-all duration-300 font-semibold animate-fade-in-up"
            style="animation-delay: 0.1s;"
            to="/contact"
          >
            <span>شارك تجربتك</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </RouterLink>
        </div>
        <div class="grid gap-6 lg:grid-cols-3">
          <article
            v-for="(testimonial, index) in displayTestimonials"
            :key="testimonial.id"
            class="group card-premium glass-strong p-8 hover-lift animate-fade-in-up"
            :style="{ animationDelay: `${index * 0.15}s` }"
          >
            <div class="flex items-center justify-between mb-5">
              <div class="flex items-center gap-3">
                <div class="size-12 rounded-full bg-gradient-to-br from-primary/30 to-primary/10 flex items-center justify-center text-xl font-black text-white">
                  {{ testimonial.name?.charAt(0) || 'ط' }}
                </div>
                <div>
                  <p class="font-bold text-lg">{{ testimonial.name }}</p>
                  <p class="text-xs text-slate-300 dark:text-slate-400">{{ testimonial.relation || 'طالب' }}</p>
                </div>
              </div>
              <div class="flex items-center gap-1">
                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="text-amber-300 font-bold">{{ testimonial.rating || '5.0' }}</span>
              </div>
            </div>
            <p class="text-sm text-slate-200 dark:text-slate-300 leading-relaxed line-clamp-5 mb-4">{{ testimonial.comment }}</p>
            <p class="text-xs text-slate-400 flex items-center gap-2">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              منذ {{ testimonial.timeAgo || 'أسبوع' }}
            </p>
          </article>
          <p v-if="!loading.home && !displayTestimonials.length" class="lg:col-span-3 text-center text-slate-400 py-12">
            لم يتم نشر تقييمات بعد.
          </p>
        </div>
      </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="relative rounded-3xl bg-gradient-to-br from-primary via-primary/90 to-secondary overflow-hidden shadow-2xl animate-fade-in-up">
        <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.05%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <div class="relative p-10 md:p-16 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
          <div class="flex-1">
            <p class="text-sm uppercase tracking-[4px] text-white/90 font-bold mb-4">{{ brandingStore.displayName }}</p>
            <h2 class="text-4xl md:text-5xl font-black mb-4 leading-tight">
              <span class="block">جاهز لتصميم</span>
              <span class="block">أول براند متكامل لك؟</span>
            </h2>
            <p class="text-white/90 text-lg">سجل الآن واحصل على جلسة تقييم مجانية مع أحد المدربين.</p>
          </div>
          <div class="flex flex-wrap gap-4">
            <RouterLink 
              class="group/btn inline-flex items-center gap-2 px-8 py-4 bg-white text-slate-900 font-bold rounded-2xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden"
              to="/register"
            >
              <span class="relative z-10">ابدأ رحلتك الآن</span>
              <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform duration-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
              <div class="absolute inset-0 bg-gradient-to-r from-primary/10 to-secondary/10 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
            </RouterLink>
            <RouterLink 
              class="px-8 py-4 border-2 border-white/60 rounded-2xl text-white font-bold hover:bg-white/10 hover:border-white/80 transition-all duration-300 backdrop-blur-sm glass"
              to="/contact"
            >
              تحدث مع فريق القبول
            </RouterLink>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, reactive, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import { useApi } from '../../composables/useApi';
import { useBrandingStore } from '../../stores/branding';

const brandingStore = useBrandingStore();

const { get, loading: apiLoading } = useApi();
const loading = reactive({ home: true });
const homeData = reactive({
  sections: {
    slider: true,
    testimonials: true,
    featured_courses: true,
    statistics: true,
    faq: true,
  },
  sliders: [],
  courses: [],
  testimonials: [],
  stats: null,
  highlightCards: [],
  learningPillars: [],
  communityFeatures: [],
  upcomingSessions: [],
});

const activeSlide = ref(0);
const sliderTimer = ref(null);

const defaultLearningPillars = [
  { title: 'مشاريع أسبوعية', description: 'تنفيذ مشروع قصير كل أسبوع مع مراجعة جماعية.' },
  { title: 'تغذية راجعة فورية', description: 'تعليقات مسجلة من المدرب لكل ملف تقوم برفعه.' },
  { title: 'مجتمع خاص', description: 'قنوات نقاش مغلقة وتحديات يتم نشرها يوم الاثنين.' },
  { title: 'أرشيف دروس', description: 'تسجيلات متاحة 30 يوماً بعد انتهاء المسار.' },
];

const defaultCommunityFeatures = [
  { icon: '💬', title: 'قنوات نقاش متخصصة', description: 'غرف للبراندنج، الموشن، والواجهات مع مشرفين.' },
  { icon: '🎯', title: 'تحديات أسبوعية', description: 'بناء عادات تصميم مستمرة مع مكافآت رقمية.' },
  { icon: '🧑‍🏫', title: 'مراجعات فردية', description: 'جلسات قصيرة لكل طالب قبل تسليم المشروع النهائي.' },
];

const heroStats = computed(() => {
  const stats = homeData.stats ?? {};
  return {
    learners: Intl.NumberFormat('ar-EG').format(stats.learners ?? 0),
    liveSessions: stats.live_sessions ?? 0,
    projects: stats.projects ?? 0,
    reviews: stats.reviews ?? 0,
  };
});

const resolvedHighlightCards = computed(() => {
  if (homeData.highlightCards?.length) {
    return homeData.highlightCards;
  }

  const stats = homeData.stats ?? {};
  const liveSessions = stats.live_sessions ?? 0;
  const projects = stats.projects ?? 0;
  const tracks = homeData.courses.length ? new Set(homeData.courses.map((course) => course.category?.name)).size : 0;

  return [
    {
      title: 'ورش مباشرة',
      value: liveSessions,
      trend: `${liveSessions} جلسة قادمة`,
      badge: 'Live',
      icon: '🎬',
      description: 'جلسات تفاعلية مع إمكانية رفع الأسئلة واستعراض الشاشات.',
    },
    {
      title: 'ملفات جاهزة',
      value: projects * 6 || 0,
      trend: 'مواد تدريبية محدثة بعد كل جلسة',
      badge: 'Resources',
      icon: '📂',
      description: 'ملفات عمل، قوالب عروض، ومرجع اختيار الألوان للتحميل.',
    },
    {
      title: 'مسارات معتمدة',
      value: tracks || 0,
      trend: 'يشمل UI/UX وموشن وبراندنج',
      badge: 'Tracks',
      icon: '🚀',
      description: 'مسارات محددة المدة مع متابعة حضور وتقارير أداء.',
    },
  ];
});

const learningPillars = computed(() =>
  homeData.learningPillars?.length ? homeData.learningPillars : defaultLearningPillars,
);

const communityFeatures = computed(() =>
  homeData.communityFeatures?.length ? homeData.communityFeatures : defaultCommunityFeatures,
);

const topCourses = computed(() => homeData.courses.slice(0, 3));
const displayTestimonials = computed(() => homeData.testimonials.slice(0, 3));
const upcomingSessions = computed(() => homeData.upcomingSessions);

function formatDate(date) {
  if (!date) return null;
  return new Date(date).toLocaleDateString('ar-EG', { month: 'short', day: 'numeric' });
}

function startSlider() {
  stopSlider();
  if (!homeData.sliders.length) return;
  sliderTimer.value = setInterval(() => {
    activeSlide.value = (activeSlide.value + 1) % homeData.sliders.length;
  }, 6000);
}

function stopSlider() {
  if (sliderTimer.value) {
    clearInterval(sliderTimer.value);
  }
  sliderTimer.value = null;
}

async function fetchHomepageData() {
  loading.home = true;
  try {
    const data = await get('/home');
    if (data) {
      homeData.sections = data.sections ?? {
        slider: true,
        testimonials: true,
        featured_courses: true,
        statistics: true,
        faq: true,
      };
      homeData.sliders = data.sliders ?? [];
      homeData.courses = data.courses ?? [];
      homeData.testimonials = data.testimonials ?? [];
      homeData.stats = data.stats ?? null;
      homeData.highlightCards = data.highlight_cards ?? [];
      homeData.learningPillars = data.learning_pillars ?? [];
      homeData.communityFeatures = data.community_features ?? [];
      homeData.upcomingSessions = (data.upcoming_sessions ?? []).map((session) => ({
        ...session,
        dateLabel: session.date_label,
        timeLabel: session.time_label,
        courseTitle: session.course_title,
      }));
    }
  } catch (error) {
    console.error('Home data error', error);
    // Set empty defaults on error
    homeData.sliders = [];
    homeData.courses = [];
    homeData.testimonials = [];
    homeData.stats = null;
    homeData.highlightCards = [];
    homeData.learningPillars = [];
    homeData.communityFeatures = [];
    homeData.upcomingSessions = [];
  } finally {
    loading.home = false;
    startSlider();
  }
}

watch(
  () => homeData.sliders.length,
  () => startSlider(),
);

onMounted(() => {
  fetchHomepageData();
});

onUnmounted(() => {
  stopSlider();
});
</script>

<style scoped>
.hero-orb {
  position: absolute;
  border-radius: 999px;
  filter: blur(120px);
  opacity: 0.35;
  animation: float 14s ease-in-out infinite;
}
.hero-orb--one {
  width: 320px;
  height: 280px;
  top: -140px;
  left: -40px;
  background: rgba(56, 189, 248, 0.7);
}
.hero-orb--two {
  width: 260px;
  height: 260px;
  bottom: -120px;
  right: -60px;
  background: rgba(248, 113, 113, 0.5);
}
@keyframes float {
  0% {
    transform: translate3d(0, 0, 0) scale(1);
  }
  50% {
    transform: translate3d(30px, -30px, 0) scale(1.05);
  }
  100% {
    transform: translate3d(0, 0, 0) scale(1);
  }
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
