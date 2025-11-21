<template>
  <div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-2">التقارير الاستراتيجية</h1>
        <p class="text-slate-600 dark:text-slate-400">تقارير متقدمة لاتخاذ القرارات الإستراتيجية</p>
      </div>
      <div class="flex gap-2">
        <button
          @click="showFilters = !showFilters"
          class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
          </svg>
          {{ showFilters ? 'إخفاء الفلاتر' : 'الفلاتر' }}
        </button>
        <button
          @click="refreshAll"
          class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors"
          :disabled="refreshing"
        >
          {{ refreshing ? 'جاري التحديث...' : 'تحديث البيانات' }}
        </button>
      </div>
    </div>

    <!-- Filters Panel -->
    <div
      v-if="showFilters"
      class="card-premium p-6 animate-fade-in"
    >
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">فلاتر التقرير</h3>
        <button
          @click="resetFilters"
          class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300"
        >
          إعادة تعيين
        </button>
      </div>
      
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Date Range -->
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">من تاريخ</label>
          <input
            v-model="filters.start_date"
            type="date"
            class="input-enhanced w-full"
            @change="loadReport"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">إلى تاريخ</label>
          <input
            v-model="filters.end_date"
            type="date"
            class="input-enhanced w-full"
            :min="filters.start_date"
            @change="loadReport"
          />
        </div>
        
        <!-- Category Filter -->
        <div v-if="categories.length > 0">
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">التصنيف</label>
          <FilterDropdown
            v-model="filters.category_id"
            :options="categories"
            placeholder="جميع التصنيفات"
            @update:modelValue="loadReport"
          />
        </div>
        
        <!-- Instructor Filter -->
        <div v-if="instructors.length > 0">
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">المدرب</label>
          <FilterDropdown
            v-model="filters.instructor_id"
            :options="instructors"
            placeholder="جميع المدربين"
            @update:modelValue="loadReport"
          />
        </div>
        
        <!-- Course Filter (for specific reports) -->
        <div v-if="activeReport === 'profitability' && courses.length > 0">
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الكورس</label>
          <FilterDropdown
            v-model="filters.course_id"
            :options="courses"
            placeholder="جميع الكورسات"
            @update:modelValue="loadReport"
          />
        </div>
        
        <!-- Period Filter (for performance report) -->
        <div v-if="activeReport === 'performance'">
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الفترة</label>
          <select
            v-model="filters.period"
            @change="loadReport"
            class="input-enhanced w-full"
          >
            <option value="day">يومي</option>
            <option value="week">أسبوعي</option>
            <option value="month">شهري</option>
            <option value="quarter">ربع سنوي</option>
            <option value="year">سنوي</option>
          </select>
        </div>
        
        <!-- Forecast Months -->
        <div v-if="activeReport === 'forecasting'">
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">عدد الأشهر المتوقعة</label>
          <input
            v-model.number="filters.months"
            type="number"
            min="1"
            max="24"
            class="input-enhanced w-full"
            @change="loadReport"
          />
        </div>
      </div>
      
      <!-- Quick Date Presets -->
      <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">فترات سريعة:</p>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="preset in datePresets"
            :key="preset.key"
            @click="applyDatePreset(preset)"
            class="px-3 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
          >
            {{ preset.label }}
          </button>
        </div>
      </div>
    </div>

    <!-- Report Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-700">
      <button
        v-for="report in reports"
        :key="report.key"
        @click="activeReport = report.key; loadReport()"
        class="px-6 py-3 font-bold transition-all duration-300 border-b-2"
        :class="activeReport === report.key
          ? 'border-primary text-primary'
          : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'"
      >
        {{ report.title }}
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">جاري تحميل التقرير...</p>
    </div>

    <!-- Report 1: Performance Report -->
    <div v-else-if="activeReport === 'performance' && performanceData" class="space-y-6">
      <!-- KPIs Cards -->
      <div class="grid md:grid-cols-4 gap-4">
        <div
          v-for="kpi in kpiCards"
          :key="kpi.key"
          class="card-premium p-6 hover-lift"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="p-3 rounded-xl bg-gradient-to-br" :class="kpi.gradient">
              <component :is="'svg'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="kpi.icon" />
              </component>
            </div>
            <span
              v-if="kpi.growth !== undefined"
              class="text-xs font-bold px-2 py-1 rounded"
              :class="kpi.growth >= 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'"
            >
              {{ kpi.growth >= 0 ? '+' : '' }}{{ kpi.growth }}%
            </span>
          </div>
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">{{ kpi.label }}</p>
          <p class="text-2xl font-black text-slate-900 dark:text-white">
            {{ formatValue(performanceData.kpis[kpi.key], kpi.format) }}
          </p>
        </div>
      </div>

      <!-- Alerts -->
      <div v-if="performanceData.alerts?.length" class="space-y-2">
        <div
          v-for="alert in performanceData.alerts"
          :key="alert.title"
          class="p-4 rounded-xl border-l-4"
          :class="alert.type === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-500' : 'bg-blue-50 dark:bg-blue-900/20 border-blue-500'"
        >
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" :class="alert.type === 'warning' ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
              <p class="font-bold text-slate-900 dark:text-white">{{ alert.title }}</p>
              <p class="text-sm text-slate-600 dark:text-slate-400">{{ alert.message }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Trends Chart -->
      <div class="card-premium p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">اتجاهات الأداء</h3>
        <div class="grid md:grid-cols-6 gap-4">
          <div
            v-for="trend in performanceData.trends"
            :key="trend.period"
            class="text-center p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900"
          >
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">{{ trend.label }}</p>
            <p class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ formatCurrency(trend.revenue) }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ trend.enrollments }} تسجيل</p>
          </div>
        </div>
      </div>

      <!-- Recommendations -->
      <div v-if="performanceData.recommendations?.length" class="card-premium p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">التوصيات</h3>
        <ul class="space-y-2">
          <li
            v-for="(rec, index) in performanceData.recommendations"
            :key="index"
            class="flex items-start gap-3 text-slate-700 dark:text-slate-300"
          >
            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">{{ index + 1 }}</span>
            <span>{{ rec }}</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Report 2: Profitability Report -->
    <div v-else-if="activeReport === 'profitability' && profitabilityData" class="space-y-6">
      <!-- Revenue Summary -->
      <div class="grid md:grid-cols-4 gap-4">
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">إجمالي الإيرادات</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ formatCurrency(profitabilityData.revenue?.total || 0) }}</p>
        </div>
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">المحصّل</p>
          <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ formatCurrency(profitabilityData.revenue?.collected || 0) }}</p>
        </div>
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">المتبقي</p>
          <p class="text-3xl font-black text-orange-600 dark:text-orange-400">{{ formatCurrency(profitabilityData.revenue?.outstanding || 0) }}</p>
        </div>
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">معدل التحصيل</p>
          <p class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ profitabilityData.revenue?.collection_rate || 0 }}%</p>
        </div>
      </div>

      <!-- Top Courses by Revenue -->
      <div class="card-premium p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">أكثر الكورسات ربحية</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 text-xs uppercase text-slate-600 dark:text-slate-400 font-semibold">
              <tr>
                <th class="px-6 py-4 text-left">الكورس</th>
                <th class="px-6 py-4 text-left">التسجيلات</th>
                <th class="px-6 py-4 text-left">إجمالي الإيرادات</th>
                <th class="px-6 py-4 text-left">المحصّل</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
              <tr
                v-for="course in profitabilityData.revenue_by_course"
                :key="course.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
              >
                <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">{{ course.title }}</td>
                <td class="px-6 py-4">{{ course.enrollments_count || 0 }}</td>
                <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">{{ formatCurrency(course.total_revenue || 0) }}</td>
                <td class="px-6 py-4 font-semibold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(course.collected_revenue || 0) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Report 3: Student Analytics -->
    <div v-else-if="activeReport === 'student-analytics' && studentData" class="space-y-6">
      <!-- Overview Cards -->
      <div class="grid md:grid-cols-4 gap-4">
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">إجمالي الطلاب</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ studentData.overview?.total_students || 0 }}</p>
        </div>
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">طلاب جدد</p>
          <p class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ studentData.overview?.new_students || 0 }}</p>
        </div>
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">معدل الإتمام</p>
          <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ studentData.completion?.completion_rate || 0 }}%</p>
        </div>
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">معدل الحضور</p>
          <p class="text-3xl font-black text-purple-600 dark:text-purple-400">{{ studentData.attendance?.rate || 0 }}%</p>
        </div>
      </div>

      <!-- Satisfaction -->
      <div class="card-premium p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">رضا الطلاب</h3>
        <div class="grid md:grid-cols-3 gap-4">
          <div class="text-center p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-2">تقييم الكورسات</p>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ studentData.satisfaction?.avg_course_rating || 0 }}</p>
          </div>
          <div class="text-center p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-2">تقييم المدربين</p>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ studentData.satisfaction?.avg_instructor_rating || 0 }}</p>
          </div>
          <div class="text-center p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-2">عدد التقييمات</p>
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ studentData.satisfaction?.total_reviews || 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Report 4: Instructor Performance -->
    <div v-else-if="activeReport === 'instructor-performance' && instructorData" class="space-y-6">
      <!-- Overall Stats -->
      <div class="grid md:grid-cols-4 gap-4">
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">إجمالي المدربين</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ instructorData.overall?.total_instructors || 0 }}</p>
        </div>
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">متوسط التقييم</p>
          <p class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ instructorData.overall?.avg_rating || 0 }}</p>
        </div>
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">معدل الإتمام</p>
          <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ instructorData.overall?.avg_completion_rate || 0 }}%</p>
        </div>
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">إجمالي الإيرادات</p>
          <p class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ formatCurrency(instructorData.overall?.total_revenue || 0) }}</p>
        </div>
      </div>

      <!-- Top Performers -->
      <div class="card-premium p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">أفضل المدربين أداءً</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 text-xs uppercase text-slate-600 dark:text-slate-400 font-semibold">
              <tr>
                <th class="px-6 py-4 text-left">المدرب</th>
                <th class="px-6 py-4 text-left">الكورسات</th>
                <th class="px-6 py-4 text-left">الطلاب</th>
                <th class="px-6 py-4 text-left">التقييم</th>
                <th class="px-6 py-4 text-left">نقاط الأداء</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
              <tr
                v-for="instructor in instructorData.top_performers"
                :key="instructor.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
              >
                <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">{{ instructor.name }}</td>
                <td class="px-6 py-4">{{ instructor.courses_count || 0 }}</td>
                <td class="px-6 py-4">{{ instructor.total_students || 0 }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="font-semibold">{{ instructor.avg_rating || 0 }}</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 text-xs rounded-full font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    {{ instructor.performance_score || 0 }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Report 5: Forecasting -->
    <div v-else-if="activeReport === 'forecasting' && forecastingData" class="space-y-6">
      <!-- Forecast Summary -->
      <div class="grid md:grid-cols-3 gap-4">
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">متوسط الإيرادات المتوقعة</p>
          <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400">
            {{ formatCurrency(calculateAverage(forecastingData.forecasts?.revenue || [])) }}
          </p>
        </div>
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">متوسط التسجيلات المتوقعة</p>
          <p class="text-3xl font-black text-blue-600 dark:text-blue-400">
            {{ Math.round(calculateAverage(forecastingData.forecasts?.enrollments || [])) }}
          </p>
        </div>
        <div class="card-premium p-6">
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">نمو الطلاب المتوقع</p>
          <p class="text-3xl font-black text-purple-600 dark:text-purple-400">
            {{ Math.round(calculateAverage(forecastingData.forecasts?.student_growth || [])) }}
          </p>
        </div>
      </div>

      <!-- Risks -->
      <div v-if="forecastingData.risks?.length" class="card-premium p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">المخاطر المحتملة</h3>
        <div class="space-y-3">
          <div
            v-for="risk in forecastingData.risks"
            :key="risk.type"
            class="p-4 rounded-xl border-l-4 bg-red-50 dark:bg-red-900/20 border-red-500"
          >
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div>
                <p class="font-bold text-red-900 dark:text-red-300">{{ risk.description }}</p>
                <p class="text-xs text-red-700 dark:text-red-400 mt-1">خطورة: {{ risk.severity === 'high' ? 'عالية' : 'متوسطة' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Opportunities -->
      <div v-if="forecastingData.opportunities?.length" class="card-premium p-6">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">الفرص المتاحة</h3>
        <div class="space-y-3">
          <div
            v-for="opp in forecastingData.opportunities"
            :key="opp.type"
            class="p-4 rounded-xl border-l-4 bg-green-50 dark:bg-green-900/20 border-green-500"
          >
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="font-bold text-green-900 dark:text-green-300">{{ opp.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useI18n } from '../../../composables/useI18n';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';

const { get } = useApi();
const toast = useToast();
const { locale } = useI18n();

const activeReport = ref('performance');
const loading = ref(false);
const refreshing = ref(false);
const showFilters = ref(false);

const filters = reactive({
  start_date: '',
  end_date: '',
  category_id: '',
  instructor_id: '',
  course_id: '',
  period: 'month',
  months: 6,
});

const categories = ref([]);
const instructors = ref([]);
const courses = ref([]);

const performanceData = ref(null);
const profitabilityData = ref(null);
const studentData = ref(null);
const instructorData = ref(null);
const forecastingData = ref(null);

const reports = [
  { key: 'performance', title: 'تقرير الأداء الشامل (KPIs)' },
  { key: 'profitability', title: 'تقرير الربحية والمالية' },
  { key: 'student-analytics', title: 'تحليل الطلاب' },
  { key: 'instructor-performance', title: 'أداء المدربين' },
  { key: 'forecasting', title: 'التنبؤات والتوقعات' },
];

const kpiCards = computed(() => {
  if (!performanceData.value) return [];
  
  const growth = performanceData.value.growth || {};
  
  return [
    {
      key: 'revenue',
      label: 'الإيرادات',
      format: 'currency',
      gradient: 'from-emerald-500 to-emerald-600',
      icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V3m0 9v3m0 3.01V21M6 12H3.98M18 12h2.02M4.212 17.788l-1.424 1.424M19.192 5.01l1.424-1.424M6.344 7.656l-1.424-1.424M17.656 16.344l1.424 1.424',
      growth: growth.revenue_growth,
    },
    {
      key: 'enrollments',
      label: 'التسجيلات',
      format: 'number',
      gradient: 'from-blue-500 to-blue-600',
      icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
      growth: growth.enrollment_growth,
    },
    {
      key: 'students',
      label: 'الطلاب',
      format: 'number',
      gradient: 'from-purple-500 to-purple-600',
      icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
      growth: growth.student_growth,
    },
    {
      key: 'attendance',
      label: 'معدل الحضور',
      format: 'percentage',
      gradient: 'from-orange-500 to-orange-600',
      icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    },
  ];
});

function buildQueryParams() {
  const params = new URLSearchParams();
  
  if (filters.start_date) params.append('start_date', filters.start_date);
  if (filters.end_date) params.append('end_date', filters.end_date);
  if (filters.category_id) params.append('category_id', filters.category_id);
  if (filters.instructor_id) params.append('instructor_id', filters.instructor_id);
  if (filters.course_id) params.append('course_id', filters.course_id);
  if (filters.period) params.append('period', filters.period);
  if (filters.months) params.append('months', filters.months);
  
  return params.toString();
}

async function loadReport() {
  if (loading.value) return;
  
  try {
    loading.value = true;
    let response;
    const queryParams = buildQueryParams();
    
    switch (activeReport.value) {
      case 'performance':
        response = await get(`/admin/reports/strategic/performance${queryParams ? '?' + queryParams : ''}`);
        performanceData.value = response.data || response;
        break;
      case 'profitability':
        response = await get(`/admin/reports/strategic/profitability${queryParams ? '?' + queryParams : ''}`);
        profitabilityData.value = response.data || response;
        break;
      case 'student-analytics':
        response = await get(`/admin/reports/strategic/student-analytics${queryParams ? '?' + queryParams : ''}`);
        studentData.value = response.data || response;
        break;
      case 'instructor-performance':
        response = await get(`/admin/reports/strategic/instructor-performance${queryParams ? '?' + queryParams : ''}`);
        instructorData.value = response.data || response;
        break;
      case 'forecasting':
        response = await get(`/admin/reports/strategic/forecasting${queryParams ? '?' + queryParams : ''}`);
        forecastingData.value = response.data || response;
        break;
    }
  } catch (err) {
    console.error('Error loading report:', err);
    toast.error('حدث خطأ أثناء تحميل التقرير');
  } finally {
    loading.value = false;
  }
}

async function refreshAll() {
  if (refreshing.value) return;
  
  try {
    refreshing.value = true;
    // Clear cache by adding timestamp
    const timestamp = Date.now();
    const queryParams = buildQueryParams();
    const separator = queryParams ? '&' : '?';
    
    await Promise.all([
      get(`/admin/reports/strategic/performance${queryParams ? '?' + queryParams : ''}${separator}_t=${timestamp}`),
      get(`/admin/reports/strategic/profitability${queryParams ? '?' + queryParams : ''}${separator}_t=${timestamp}`),
      get(`/admin/reports/strategic/student-analytics${queryParams ? '?' + queryParams : ''}${separator}_t=${timestamp}`),
      get(`/admin/reports/strategic/instructor-performance${queryParams ? '?' + queryParams : ''}${separator}_t=${timestamp}`),
      get(`/admin/reports/strategic/forecasting${queryParams ? '?' + queryParams : ''}${separator}_t=${timestamp}`),
    ]);
    
    toast.success('تم تحديث جميع التقارير بنجاح');
    await loadReport();
  } catch (err) {
    console.error('Error refreshing reports:', err);
    toast.error('حدث خطأ أثناء تحديث التقارير');
  } finally {
    refreshing.value = false;
  }
}

function formatCurrency(value) {
  if (typeof value === 'object' && value !== null) {
    value = value.total || value.collected || 0;
  }
  return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-US', {
    style: 'currency',
    currency: 'EGP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value || 0);
}

function formatValue(value, format) {
  if (typeof value === 'object' && value !== null) {
    value = value.total || value.collected || 0;
  }
  
  switch (format) {
    case 'currency':
      return formatCurrency(value);
    case 'percentage':
      return (value || 0) + '%';
    default:
      return value || 0;
  }
}

function calculateAverage(array) {
  if (!array || array.length === 0) return 0;
  const values = array.map(item => item.predicted_revenue || item.predicted_enrollments || item.predicted_growth || item.revenue || item || 0);
  return values.reduce((a, b) => a + b, 0) / values.length;
}

const datePresets = [
  { key: 'today', label: 'اليوم', start: new Date().toISOString().split('T')[0], end: new Date().toISOString().split('T')[0] },
  { key: 'week', label: 'هذا الأسبوع', start: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], end: new Date().toISOString().split('T')[0] },
  { key: 'month', label: 'هذا الشهر', start: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0], end: new Date().toISOString().split('T')[0] },
  { key: 'last_month', label: 'الشهر الماضي', start: new Date(new Date().getFullYear(), new Date().getMonth() - 1, 1).toISOString().split('T')[0], end: new Date(new Date().getFullYear(), new Date().getMonth(), 0).toISOString().split('T')[0] },
  { key: 'quarter', label: 'هذا الربع', start: new Date(new Date().getFullYear(), Math.floor(new Date().getMonth() / 3) * 3, 1).toISOString().split('T')[0], end: new Date().toISOString().split('T')[0] },
  { key: 'year', label: 'هذا العام', start: new Date(new Date().getFullYear(), 0, 1).toISOString().split('T')[0], end: new Date().toISOString().split('T')[0] },
  { key: 'last_6_months', label: 'آخر 6 أشهر', start: new Date(Date.now() - 180 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], end: new Date().toISOString().split('T')[0] },
  { key: 'last_year', label: 'آخر سنة', start: new Date(Date.now() - 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], end: new Date().toISOString().split('T')[0] },
];

function applyDatePreset(preset) {
  filters.start_date = preset.start;
  filters.end_date = preset.end;
  loadReport();
}

function resetFilters() {
  filters.start_date = '';
  filters.end_date = '';
  filters.category_id = '';
  filters.instructor_id = '';
  filters.course_id = '';
  filters.period = 'month';
  filters.months = 6;
  loadReport();
}

async function loadFilters() {
  try {
    // Load categories
    const categoriesRes = await get('/categories');
    if (categoriesRes && Array.isArray(categoriesRes)) {
      categories.value = categoriesRes.map(c => ({ id: c.id, name: c.name || c.translations?.[0]?.name || 'Unknown' }));
    } else if (categoriesRes?.data && Array.isArray(categoriesRes.data)) {
      categories.value = categoriesRes.data.map(c => ({ id: c.id, name: c.name || c.translations?.[0]?.name || 'Unknown' }));
    }
    
    // Load instructors
    const instructorsRes = await get('/users?role=instructor');
    if (instructorsRes && Array.isArray(instructorsRes)) {
      instructors.value = instructorsRes.map(i => ({ id: i.id, name: i.name }));
    } else if (instructorsRes?.data && Array.isArray(instructorsRes.data)) {
      instructors.value = instructorsRes.data.map(i => ({ id: i.id, name: i.name }));
    }
    
    // Load courses (for profitability report)
    const coursesRes = await get('/courses');
    if (coursesRes && Array.isArray(coursesRes)) {
      courses.value = coursesRes.map(c => ({ id: c.id, name: c.title }));
    } else if (coursesRes?.data && Array.isArray(coursesRes.data)) {
      courses.value = coursesRes.data.map(c => ({ id: c.id, name: c.title }));
    }
  } catch (err) {
    console.error('Error loading filters:', err);
  }
}

onMounted(() => {
  loadFilters();
  loadReport();
});
</script>

