<template>
  <div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-2">التقارير الشاملة</h1>
        <p class="text-slate-600 dark:text-slate-400">تقارير مفصلة عن الكورسات، المدربين، والأداء المالي</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="activeReport = 'courses'"
          class="px-6 py-3 rounded-xl font-bold transition-all duration-300"
          :class="activeReport === 'courses' 
            ? 'bg-primary text-white shadow-lg' 
            : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-2 border-slate-200 dark:border-slate-700 hover:border-primary'"
        >
          تقرير الكورسات
        </button>
        <button
          @click="activeReport = 'instructors'"
          class="px-6 py-3 rounded-xl font-bold transition-all duration-300"
          :class="activeReport === 'instructors' 
            ? 'bg-primary text-white shadow-lg' 
            : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-2 border-slate-200 dark:border-slate-700 hover:border-primary'"
        >
          تقرير المدربين
        </button>
        <button
          @click="activeReport = 'financial'"
          class="px-6 py-3 rounded-xl font-bold transition-all duration-300"
          :class="activeReport === 'financial' 
            ? 'bg-primary text-white shadow-lg' 
            : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-2 border-slate-200 dark:border-slate-700 hover:border-primary'"
        >
          التقرير المالي
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">جاري تحميل التقرير...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="!loading && activeReport === 'courses' && !coursesReport" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400">لا توجد بيانات متاحة</p>
    </div>
    <div v-else-if="!loading && activeReport === 'instructors' && !instructorsReport" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400">لا توجد بيانات متاحة</p>
    </div>
    <div v-else-if="!loading && activeReport === 'financial' && !financialReport" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400">لا توجد بيانات متاحة</p>
    </div>

    <!-- Courses Report -->
    <div v-else-if="activeReport === 'courses' && coursesReport" class="space-y-6">
      <!-- Summary Cards -->
      <div class="grid md:grid-cols-4 gap-4">
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي الكورسات</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ coursesReport.summary?.total_courses || 0 }}</p>
        </div>
        
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي الطلاب</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ coursesReport.summary?.total_students || 0 }}</p>
        </div>
        
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V3m0 9v3m0 3.01V21M6 12H3.98M18 12h2.02M4.212 17.788l-1.424 1.424M19.192 5.01l1.424-1.424M6.344 7.656l-1.424-1.424M17.656 16.344l1.424 1.424" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي الإيرادات</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ formatCurrency(coursesReport.summary?.total_revenue || 0) }}</p>
        </div>
        
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">متوسط التقييم</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ coursesReport.summary?.average_rating || 0 }}</p>
        </div>
      </div>

      <!-- Courses Table -->
      <div class="card-premium p-6">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">تفاصيل الكورسات</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 text-xs uppercase text-slate-600 dark:text-slate-400 font-semibold">
              <tr>
                <th class="px-6 py-4 text-left">الكورس</th>
                <th class="px-6 py-4 text-left">التصنيف</th>
                <th class="px-6 py-4 text-left">الطلاب</th>
                <th class="px-6 py-4 text-left">الجلسات</th>
                <th class="px-6 py-4 text-left">الإيرادات</th>
                <th class="px-6 py-4 text-left">التقييم</th>
                <th class="px-6 py-4 text-left">الحضور</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
              <tr
                v-for="course in coursesReport.report"
                :key="course.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150"
              >
                <td class="px-6 py-4">
                  <div>
                    <p class="font-bold text-slate-900 dark:text-white">{{ course.title }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ course.code }}</p>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                    {{ course.category || '-' }}
                  </span>
                </td>
                <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">
                  {{ course.statistics?.students_enrolled || 0 }}
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm">
                    <span class="font-semibold">{{ course.statistics?.sessions_completed || 0 }}</span>
                    <span class="text-slate-500 dark:text-slate-400"> / {{ course.statistics?.sessions_total || 0 }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 font-semibold text-emerald-600 dark:text-emerald-400">
                  {{ formatCurrency(course.financial?.paid_total || 0) }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="font-semibold">{{ course.performance?.average_rating || 0 }}</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 text-xs rounded-full font-semibold"
                    :class="(course.performance?.attendance_rate || 0) >= 80 
                      ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                      : (course.performance?.attendance_rate || 0) >= 60
                      ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400'
                      : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'"
                  >
                    {{ course.performance?.attendance_rate || 0 }}%
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Instructors Report -->
    <div v-else-if="activeReport === 'instructors' && instructorsReport" class="space-y-6">
      <!-- Summary Cards -->
      <div class="grid md:grid-cols-4 gap-4">
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي المدربين</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ instructorsReport.summary?.total_instructors || 0 }}</p>
        </div>
        
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي الكورسات</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ instructorsReport.summary?.total_courses || 0 }}</p>
        </div>
        
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي الطلاب</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ instructorsReport.summary?.total_students || 0 }}</p>
        </div>
        
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V3m0 9v3m0 3.01V21M6 12H3.98M18 12h2.02M4.212 17.788l-1.424 1.424M19.192 5.01l1.424-1.424M6.344 7.656l-1.424-1.424M17.656 16.344l1.424 1.424" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي الإيرادات</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ formatCurrency(instructorsReport.summary?.total_revenue || 0) }}</p>
        </div>
      </div>

      <!-- Instructors Table -->
      <div class="card-premium p-6">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">تفاصيل المدربين</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 text-xs uppercase text-slate-600 dark:text-slate-400 font-semibold">
              <tr>
                <th class="px-6 py-4 text-left">المدرب</th>
                <th class="px-6 py-4 text-left">الكورسات</th>
                <th class="px-6 py-4 text-left">الطلاب</th>
                <th class="px-6 py-4 text-left">الجلسات</th>
                <th class="px-6 py-4 text-left">الإيرادات</th>
                <th class="px-6 py-4 text-left">التقييم</th>
                <th class="px-6 py-4 text-left">الحضور</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
              <tr
                v-for="instructor in instructorsReport.report"
                :key="instructor.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150"
              >
                <td class="px-6 py-4">
                  <div>
                    <p class="font-bold text-slate-900 dark:text-white">{{ instructor.name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ instructor.email }}</p>
                  </div>
                </td>
                <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">
                  {{ instructor.statistics?.courses_count || 0 }}
                </td>
                <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">
                  {{ instructor.statistics?.total_students || 0 }}
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm">
                    <span class="font-semibold">{{ instructor.statistics?.completed_sessions || 0 }}</span>
                    <span class="text-slate-500 dark:text-slate-400"> / {{ instructor.statistics?.total_sessions || 0 }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 font-semibold text-emerald-600 dark:text-emerald-400">
                  {{ formatCurrency(instructor.financial?.total_revenue || 0) }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="font-semibold">{{ instructor.performance?.average_rating || 0 }}</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 text-xs rounded-full font-semibold"
                    :class="(instructor.performance?.attendance_rate || 0) >= 80 
                      ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                      : (instructor.performance?.attendance_rate || 0) >= 60
                      ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400'
                      : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'"
                  >
                    {{ instructor.performance?.attendance_rate || 0 }}%
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Financial Report -->
    <div v-else-if="activeReport === 'financial' && financialReport" class="space-y-6">
      <!-- Summary Cards -->
      <div class="grid md:grid-cols-4 gap-4">
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V3m0 9v3m0 3.01V21M6 12H3.98M18 12h2.02M4.212 17.788l-1.424 1.424M19.192 5.01l1.424-1.424M6.344 7.656l-1.424-1.424M17.656 16.344l1.424 1.424" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي المبلغ</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ formatCurrency(financialReport.summary?.total_amount || 0) }}</p>
        </div>
        
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-green-500 to-green-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">المدفوع</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ formatCurrency(financialReport.summary?.paid_total || 0) }}</p>
        </div>
        
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">المتبقي</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ formatCurrency(financialReport.summary?.outstanding_amount || 0) }}</p>
        </div>
        
        <div class="card-premium p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
          </div>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">معدل التحصيل</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ financialReport.summary?.collection_rate || 0 }}%</p>
        </div>
      </div>

      <!-- Payment Status Breakdown -->
      <div class="grid md:grid-cols-2 gap-6">
        <div class="card-premium p-6">
          <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">حسب حالة الدفع</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between p-4 rounded-xl bg-green-50 dark:bg-green-900/20">
              <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-green-500">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold text-slate-900 dark:text-white">مدفوع بالكامل</p>
                  <p class="text-sm text-slate-500 dark:text-slate-400">{{ financialReport.by_status?.paid?.count || 0 }} تسجيل</p>
                </div>
              </div>
              <p class="text-xl font-black text-green-600 dark:text-green-400">{{ formatCurrency(financialReport.by_status?.paid?.amount || 0) }}</p>
            </div>
            
            <div class="flex items-center justify-between p-4 rounded-xl bg-orange-50 dark:bg-orange-900/20">
              <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-orange-500">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold text-slate-900 dark:text-white">مدفوع جزئياً</p>
                  <p class="text-sm text-slate-500 dark:text-slate-400">{{ financialReport.by_status?.partially_paid?.count || 0 }} تسجيل</p>
                </div>
              </div>
              <p class="text-xl font-black text-orange-600 dark:text-orange-400">{{ formatCurrency(financialReport.by_status?.partially_paid?.amount || 0) }}</p>
            </div>
            
            <div class="flex items-center justify-between p-4 rounded-xl bg-red-50 dark:bg-red-900/20">
              <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-red-500">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold text-slate-900 dark:text-white">غير مدفوع</p>
                  <p class="text-sm text-slate-500 dark:text-slate-400">{{ financialReport.by_status?.not_paid?.count || 0 }} تسجيل</p>
                </div>
              </div>
              <p class="text-xl font-black text-red-600 dark:text-red-400">{{ formatCurrency(financialReport.by_status?.not_paid?.amount || 0) }}</p>
            </div>
          </div>
        </div>

        <!-- Monthly Revenue Chart -->
        <div class="card-premium p-6">
          <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">الإيرادات الشهرية</h3>
          <div v-if="financialReport.by_month && Array.isArray(financialReport.by_month) && financialReport.by_month.length > 0" class="space-y-3">
            <div
              v-for="month in financialReport.by_month"
              :key="month.month"
              class="space-y-2"
            >
              <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ month.month_name }}</span>
                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(month.paid_amount || 0) }}</span>
              </div>
              <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5">
                <div
                  class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2.5 rounded-full transition-all duration-500"
                  :style="{ width: `${getPercentage(month.paid_amount, financialReport.by_month)}%` }"
                ></div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-slate-500 dark:text-slate-400">
            لا توجد بيانات شهرية متاحة
          </div>
        </div>
      </div>

      <!-- By Course -->
      <div class="card-premium p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">حسب الكورس</h3>
        <div v-if="financialReport.by_course && Array.isArray(financialReport.by_course) && financialReport.by_course.length > 0" class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 text-xs uppercase text-slate-600 dark:text-slate-400 font-semibold">
              <tr>
                <th class="px-6 py-4 text-left">الكورس</th>
                <th class="px-6 py-4 text-left">عدد التسجيلات</th>
                <th class="px-6 py-4 text-left">إجمالي المبلغ</th>
                <th class="px-6 py-4 text-left">المدفوع</th>
                <th class="px-6 py-4 text-left">المتبقي</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
              <tr
                v-for="course in financialReport.by_course"
                :key="course.course_id"
                class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150"
              >
                <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">{{ course.course_title || '-' }}</td>
                <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ course.count || 0 }}</td>
                <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">{{ formatCurrency(course.total_amount || 0) }}</td>
                <td class="px-6 py-4 font-semibold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(course.paid_amount || 0) }}</td>
                <td class="px-6 py-4 font-semibold text-orange-600 dark:text-orange-400">{{ formatCurrency((course.total_amount || 0) - (course.paid_amount || 0)) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-8 text-slate-500 dark:text-slate-400">
          لا توجد بيانات كورسات متاحة
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useI18n } from '../../../composables/useI18n';

const { get } = useApi();
const toast = useToast();
const { locale } = useI18n();

const activeReport = ref('courses');
const loading = ref(false);
const coursesReport = ref(null);
const instructorsReport = ref(null);
const financialReport = ref(null);

async function loadCoursesReport() {
  if (coursesReport.value) return; // Already loaded
  
  try {
    loading.value = true;
    const response = await get('/admin/reports/courses');
    
    if (response && response.data) {
      coursesReport.value = response.data;
    } else {
      coursesReport.value = response;
    }
  } catch (err) {
    console.error('Error loading courses report:', err);
    toast.error('حدث خطأ أثناء تحميل تقرير الكورسات');
  } finally {
    loading.value = false;
  }
}

async function loadInstructorsReport() {
  if (instructorsReport.value) return; // Already loaded
  
  try {
    loading.value = true;
    const response = await get('/admin/reports/instructors');
    
    if (response && response.data) {
      instructorsReport.value = response.data;
    } else {
      instructorsReport.value = response;
    }
  } catch (err) {
    console.error('Error loading instructors report:', err);
    toast.error('حدث خطأ أثناء تحميل تقرير المدربين');
  } finally {
    loading.value = false;
  }
}

async function loadFinancialReport() {
  if (financialReport.value) return; // Already loaded
  
  try {
    loading.value = true;
    const response = await get('/admin/reports/financial');
    
    if (response && response.data) {
      financialReport.value = response.data;
    } else {
      financialReport.value = response;
    }
    
    // Ensure by_month and by_course are arrays
    if (financialReport.value) {
      if (!Array.isArray(financialReport.value.by_month)) {
        financialReport.value.by_month = [];
      }
      if (!Array.isArray(financialReport.value.by_course)) {
        financialReport.value.by_course = [];
      }
    }
  } catch (err) {
    console.error('Error loading financial report:', err);
    toast.error('حدث خطأ أثناء تحميل التقرير المالي');
    // Initialize with empty arrays to prevent errors
    financialReport.value = {
      summary: {},
      by_status: {},
      by_month: [],
      by_course: [],
    };
  } finally {
    loading.value = false;
  }
}

watch(activeReport, (newReport) => {
  if (newReport === 'courses') {
    loadCoursesReport();
  } else if (newReport === 'instructors') {
    loadInstructorsReport();
  } else if (newReport === 'financial') {
    loadFinancialReport();
  }
}, { immediate: true });

function formatCurrency(value) {
  return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-US', {
    style: 'currency',
    currency: 'EGP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value);
}

function getPercentage(value, array) {
  // Safety check: ensure array is actually an array
  if (!Array.isArray(array) || array.length === 0) {
    return 0;
  }
  
  const max = Math.max(...array.map(m => m.paid_amount || 0));
  if (max === 0) return 0;
  return (value / max) * 100;
}

onMounted(() => {
  loadCoursesReport();
});
</script>

