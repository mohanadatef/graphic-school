<template>
  <div>
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
      <div class="max-w-6xl mx-auto px-4 py-16 grid gap-10 lg:grid-cols-[1.1fr,0.9fr] items-center relative z-10">
        <div>
          <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-4 py-1 text-sm mb-4">
            <span class="size-2 bg-emerald-400 rounded-full animate-pulse"></span>
            بث مباشر مع المدربين هذا الأسبوع
          </div>
          <h1 class="text-4xl md:text-5xl font-black leading-tight mb-4">
            منصة تفاعلية لتعلم التصميم الجرافيكي خطوة بخطوة
          </h1>
          <p class="text-slate-200 text-lg leading-relaxed mb-8">
            دروس مباشرة، مراجعات فورية، ومتابعة شخصية من فريق جرافيك سكول. انضم إلى تجربة تعليمية ممتعة تجمع بين الإبداع،
            التكنولوجيا، والمجتمع.
          </p>
          <div class="flex flex-wrap gap-4">
            <RouterLink
              to="/courses"
              class="px-6 py-3 bg-white text-slate-900 font-semibold rounded-xl hover:-translate-y-0.5 transition shadow-lg shadow-white/20"
            >
              استكشف الكورسات
            </RouterLink>
            <RouterLink
              to="/about"
              class="px-6 py-3 border border-white/40 rounded-xl text-white hover:bg-white/10 transition"
            >
              لماذا جرافيك سكول؟
            </RouterLink>
          </div>
          <div class="mt-10 grid gap-4 sm:grid-cols-3 text-center">
            <div class="rounded-2xl bg-white/10 p-4 border border-white/5 backdrop-blur">
              <p class="text-3xl font-bold">{{ heroStats.learners }}</p>
              <p class="text-sm text-slate-300">طلاب نشطون</p>
            </div>
            <div class="rounded-2xl bg-white/10 p-4 border border-white/5 backdrop-blur">
              <p class="text-3xl font-bold">{{ heroStats.liveSessions }}</p>
              <p class="text-sm text-slate-300">جلسات مباشرة</p>
            </div>
            <div class="rounded-2xl bg-white/10 p-4 border border-white/5 backdrop-blur">
              <p class="text-3xl font-bold">{{ heroStats.projects }}</p>
              <p class="text-sm text-slate-300">مشاريع منجزة</p>
            </div>
          </div>
        </div>

        <div class="relative">
          <div class="absolute -top-8 -left-8 w-24 h-24 bg-primary/30 blur-3xl rounded-full"></div>
          <div class="absolute -bottom-10 -right-6 w-32 h-32 bg-secondary/40 blur-3xl rounded-full"></div>
          <div class="relative bg-white/5 border border-white/10 rounded-3xl overflow-hidden shadow-2xl backdrop-blur">
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
              <div class="absolute top-4 right-4 flex gap-2">
                <button
                  v-for="(_, idx) in homeData.sliders"
                  :key="idx"
                  class="size-2 rounded-full transition"
                  :class="idx === activeSlide ? 'bg-white' : 'bg-white/40'"
                  @click="activeSlide = idx"
                />
              </div>
            </div>
            <div v-else class="h-[360px] flex flex-col items-center justify-center text-white/80 px-8 text-center">
              <p class="text-lg font-semibold mb-2">أضف أول بانر لك</p>
              <p class="text-sm text-white/70">يمكنك إدارة البنرات من لوحة التحكم > الإعدادات > البنرات.</p>
            </div>
          </div>
          <div class="mt-4 flex items-center justify-between text-sm text-slate-300">
            <div class="flex items-center gap-2">
              <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
              {{ heroStats.reviews }} تقييم جديد هذا الشهر
            </div>
            <RouterLink class="underline underline-offset-4" to="/contact">
              احجز استشارة مجانية →
            </RouterLink>
          </div>
        </div>
      </div>
      <div class="hero-orb hero-orb--one"></div>
      <div class="hero-orb hero-orb--two"></div>
    </section>

    <section class="max-w-6xl mx-auto px-4 py-12">
      <div class="grid gap-5 md:grid-cols-3">
        <article
          v-for="card in resolvedHighlightCards"
          :key="card.title"
          class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:-translate-y-1 hover:shadow-lg transition"
        >
          <div class="flex items-center justify-between mb-5">
            <div class="size-12 rounded-xl bg-gradient-to-br from-primary/10 to-primary/0 flex items-center justify-center text-2xl">
              {{ card.icon }}
            </div>
            <span class="text-xs px-3 py-1 rounded-full bg-slate-100 text-slate-500">
              {{ card.badge }}
            </span>
          </div>
          <p class="text-sm text-slate-500 mb-1">{{ card.title }}</p>
          <p class="text-3xl font-bold text-slate-900">{{ card.value }}</p>
          <p class="text-xs text-emerald-500 mt-2">{{ card.trend }}</p>
          <p class="text-sm text-slate-500 mt-4 leading-relaxed">{{ card.description }}</p>
        </article>
      </div>
    </section>

    <section class="bg-white py-14">
      <div class="max-w-6xl mx-auto px-4">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
          <div>
            <p class="text-sm text-primary font-semibold">محتوى حديث</p>
            <h2 class="text-3xl font-bold text-slate-900">أحدث الكورسات التفاعلية</h2>
            <p class="text-slate-500 mt-1">يتم تحديث المحتوى أسبوعياً بناءً على احتياجات الطلاب.</p>
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
              class="flex flex-col rounded-3xl border border-slate-100 bg-slate-50/60 p-5 shadow-sm hover:shadow-xl transition"
            >
              <div class="flex items-center justify-between text-xs text-slate-500 mb-4">
                <span class="px-3 py-1 rounded-full bg-white text-primary font-semibold">
                  {{ course.category?.name || 'تصميم' }}
                </span>
                <span>{{ formatDate(course.start_date) || 'حسب المتاح' }}</span>
              </div>
              <h3 class="text-xl font-bold text-slate-900 mb-2">{{ course.title }}</h3>
              <p class="text-sm text-slate-600 flex-1 leading-relaxed line-clamp-4">
                {{ course.description || 'برنامج شامل لتعلم أدوات Adobe مع مشاريع عملية.' }}
              </p>
              <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
                <span>مدة الكورس: {{ course.duration || '6 أسابيع' }}</span>
                <span class="font-semibold text-slate-900">{{ course.price ? `${course.price} ج.م` : 'مجاناً' }}</span>
              </div>
              <RouterLink
                :to="`/courses/${course.id}`"
                class="mt-5 inline-flex items-center justify-between rounded-2xl border border-slate-300 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-white transition"
              >
                تفاصيل الكورس
                <span>→</span>
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
        <div class="mt-10 grid gap-4 md:grid-cols-4 text-sm text-slate-500">
          <div v-for="item in learningPillars" :key="item.title" class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4">
            <p class="text-primary font-semibold mb-1">{{ item.title }}</p>
            <p>{{ item.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 py-14">
      <div class="grid lg:grid-cols-[1.05fr,0.95fr] gap-10 items-center">
        <div class="rounded-3xl border border-slate-200 bg-white/80 p-8 shadow-lg">
          <p class="text-primary text-sm font-semibold mb-2">مجتمع متفاعل</p>
          <h2 class="text-3xl font-bold text-slate-900 mb-4">
            تجربة تعليمية ممتدة بعد انتهاء المحاضرة
          </h2>
          <p class="text-slate-600 leading-relaxed mb-6">
            حصل على دعم مباشر من المدربين، وشارك تقدمك مع زملائك، واحصل على تقييمات عملية لكل مشروع.
          </p>
          <div class="space-y-4">
            <div v-for="feature in communityFeatures" :key="feature.title" class="flex gap-4">
              <div class="size-12 rounded-2xl bg-slate-50 flex items-center justify-center text-2xl">{{ feature.icon }}</div>
              <div>
                <p class="font-semibold text-slate-900">{{ feature.title }}</p>
                <p class="text-sm text-slate-500">{{ feature.description }}</p>
              </div>
            </div>
          </div>
          <RouterLink
            class="mt-8 inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-white"
            to="/register"
          >
            انضم للمجتمع الآن
            <span>→</span>
          </RouterLink>
        </div>

        <div class="rounded-3xl bg-slate-900 text-white p-6 relative overflow-hidden">
          <div class="absolute inset-0 opacity-30 bg-[radial-gradient(circle_at_top,_#38bdf8,_transparent_40%)]"></div>
          <div class="relative">
            <div class="flex items-center justify-between mb-6">
              <div>
                <p class="text-sm text-slate-300">جلسات الأسبوع الحالي</p>
                <p class="text-2xl font-bold">{{ upcomingSessions.length }} بث مباشر</p>
              </div>
              <RouterLink class="text-sm underline underline-offset-4" to="/dashboard/student/sessions">
                عرض الجدول
              </RouterLink>
            </div>
            <div v-if="upcomingSessions.length" class="space-y-4">
              <article
                v-for="session in upcomingSessions"
                :key="session.id"
                class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur"
              >
                <div class="flex items-center justify-between text-sm">
                  <span class="font-semibold">{{ session.courseTitle || session.title }}</span>
                  <span class="text-slate-300">{{ session.dateLabel }}</span>
                </div>
                <p v-if="session.timeLabel" class="text-xs text-slate-300 mt-1">{{ session.timeLabel }}</p>
                <p class="text-xs text-emerald-300 mt-1">{{ session.focus }}</p>
              </article>
            </div>
            <p v-else class="text-sm text-slate-400">لا توجد جلسات مجدولة حالياً.</p>
            <p class="mt-6 text-xs text-slate-400">يمكنك متابعة الحضور والتسجيلات من لوحة التحكم الخاصة بك.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-slate-900 text-white">
      <div class="max-w-6xl mx-auto px-4 py-14">
        <div class="flex items-baseline justify-between gap-6 mb-8">
          <div>
            <p class="text-sm text-primary font-semibold">أصوات الطلاب</p>
            <h2 class="text-3xl font-bold">ماذا يقول مجتمعنا؟</h2>
            <p class="text-slate-400 mt-2">نستمع لكل تعليق ونطور المحتوى باستمرار.</p>
          </div>
          <RouterLink class="text-sm underline underline-offset-4" to="/contact">
            شارك تجربتك
          </RouterLink>
        </div>
        <div class="grid gap-6 lg:grid-cols-3">
          <article
            v-for="testimonial in displayTestimonials"
            :key="testimonial.id"
            class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur"
          >
            <div class="flex items-center justify-between mb-3">
              <div>
                <p class="font-semibold">{{ testimonial.name }}</p>
                <p class="text-xs text-slate-300">{{ testimonial.relation || 'طالب' }}</p>
              </div>
              <span class="text-amber-300 text-sm">⭐ {{ testimonial.rating || '5.0' }}</span>
            </div>
            <p class="text-sm text-slate-200 leading-relaxed line-clamp-5">{{ testimonial.comment }}</p>
            <p class="mt-4 text-xs text-slate-400">منذ {{ testimonial.timeAgo || 'أسبوع' }}</p>
          </article>
          <p v-if="!loading.home && !displayTestimonials.length" class="lg:col-span-3 text-center text-slate-400">
            لم يتم نشر تقييمات بعد.
          </p>
        </div>
      </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 py-16">
      <div class="rounded-3xl bg-gradient-to-br from-primary to-secondary text-white p-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-sm uppercase tracking-[4px] text-white/80">Graphic School</p>
          <h2 class="text-3xl font-bold mb-2">جاهز لتصميم أول براند متكامل لك؟</h2>
          <p class="text-white/80">سجل الآن واحصل على جلسة تقييم مجانية مع أحد المدربين.</p>
        </div>
        <div class="flex flex-wrap gap-3">
          <RouterLink class="px-6 py-3 bg-white text-slate-900 font-semibold rounded-2xl" to="/register">
            ابدأ رحلتك الآن
          </RouterLink>
          <RouterLink class="px-6 py-3 border border-white/60 rounded-2xl" to="/contact">
            تحدث مع فريق القبول
          </RouterLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, reactive, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import api from '../../api';

const loading = reactive({ home: true });
const homeData = reactive({
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
    const { data } = await api.get('/home');
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
  } catch (error) {
    console.error('Home data error', error);
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
