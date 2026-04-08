<template>
  <div class="animate-fade-in">
    <div class="mb-8">
      <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
        Панель <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-500 to-purple-500">преподавателя</span>
      </h1>
      <p class="text-slate-500 mt-2 text-lg">Управление курсами, лекциями и тестами</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
      <div v-for="(stat, i) in statCards" :key="stat.label"
        class="card stat-card p-6 animate-slide-up"
        :style="{ animationDelay: `${i * 0.07}s` }"
      >
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="stat.iconBg">
            <span v-html="stat.icon"></span>
          </div>
          <span v-if="stat.badge" class="text-xs font-medium px-2 py-0.5 rounded-full" :class="stat.badgeClass">
            {{ stat.badge }}
          </span>
        </div>
        <div v-if="statsLoading" class="h-8 w-16 bg-slate-100 rounded-md animate-pulse mb-1"></div>
        <div v-else class="text-3xl font-bold" :class="stat.valueClass">{{ stat.value }}</div>
        <div class="text-slate-500 font-medium text-sm mt-0.5">{{ stat.label }}</div>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
      <router-link
        to="/teacher/courses"
        class="group relative overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-sky-50 to-blue-50 border border-sky-100 hover:border-sky-300 transition-all duration-300 hover:shadow-lg animate-slide-up stagger-2"
      >
        <div class="absolute top-0 right-0 w-28 h-28 bg-sky-200/40 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-400 to-cyan-500 flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <h3 class="font-bold text-sky-800 text-lg mb-1">Управление курсами</h3>
          <p class="text-sky-600 text-sm">Создавайте и редактируйте курсы</p>
          <div class="mt-3 text-sm font-semibold text-sky-700">
            {{ stats.courses }} {{ pluralCourses(stats.courses) }} · {{ stats.published_courses }} опубл.
          </div>
        </div>
      </router-link>

      <router-link
        to="/teacher/lectures"
        class="group relative overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-100 hover:border-emerald-300 transition-all duration-300 hover:shadow-lg animate-slide-up stagger-3"
      >
        <div class="absolute top-0 right-0 w-28 h-28 bg-emerald-200/40 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
          </div>
          <h3 class="font-bold text-emerald-800 text-lg mb-1">Управление лекциями</h3>
          <p class="text-emerald-600 text-sm">Добавляйте и редактируйте лекции</p>
          <div class="mt-3 text-sm font-semibold text-emerald-700">{{ stats.lectures }} лекций</div>
        </div>
      </router-link>

      <router-link
        to="/teacher/tests"
        class="group relative overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-violet-50 to-purple-50 border border-violet-100 hover:border-violet-300 transition-all duration-300 hover:shadow-lg animate-slide-up stagger-4"
      >
        <div class="absolute top-0 right-0 w-28 h-28 bg-violet-200/40 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="font-bold text-violet-800 text-lg mb-1">Управление тестами</h3>
          <p class="text-violet-600 text-sm">Создавайте тесты для студентов</p>
          <div class="mt-3 text-sm font-semibold text-violet-700">{{ stats.tests }} тестов</div>
        </div>
      </router-link>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- Recent courses -->
      <div class="card p-6 animate-slide-up stagger-3">
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            Мои курсы
          </h2>
          <router-link to="/teacher/courses" class="text-sm text-sky-600 hover:text-sky-700 font-medium">Все курсы →</router-link>
        </div>

        <div v-if="statsLoading" class="space-y-3">
          <div v-for="i in 3" :key="i" class="h-14 bg-slate-100 rounded-xl animate-pulse"></div>
        </div>

        <div v-else-if="recentCourses.length === 0" class="text-center py-8 text-slate-400 text-sm">
          Нет курсов. <router-link to="/teacher/courses" class="text-sky-600 font-medium">Создать первый →</router-link>
        </div>

        <div v-else class="space-y-2.5">
          <router-link
            v-for="course in recentCourses"
            :key="course.id"
            :to="`/courses/${course.id}`"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group"
          >
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
              :class="course.status === 'published' ? 'bg-emerald-100' : 'bg-amber-100'">
              <svg class="w-4 h-4" :class="course.status === 'published' ? 'text-emerald-600' : 'text-amber-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-semibold text-slate-800 truncate group-hover:text-sky-700">{{ course.title }}</div>
              <div class="text-xs text-slate-400">{{ course.lectures_count }} лекц. · {{ course.enrollments_count }} студ.</div>
            </div>
            <span class="text-xs px-2 py-0.5 rounded-full" :class="course.status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
              {{ course.status === 'published' ? 'Опубл.' : 'Черновик' }}
            </span>
          </router-link>
        </div>
      </div>

      <!-- Recent activity -->
      <div class="card p-6 animate-slide-up stagger-4">
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Последние действия
          </h2>
        </div>

        <div v-if="statsLoading" class="space-y-3">
          <div v-for="i in 4" :key="i" class="h-12 bg-slate-100 rounded-xl animate-pulse"></div>
        </div>

        <div v-else-if="recentActivity.length === 0" class="text-center py-8 text-slate-400 text-sm">
          Действий пока нет
        </div>

        <div v-else class="space-y-2.5">
          <div
            v-for="activity in recentActivity"
            :key="activity.id"
            class="flex items-center gap-3 p-3 rounded-xl"
          >
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :class="activityBg(activity.type)">
              <span v-html="activityIcon(activity.type)" class="w-4 h-4"></span>
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium text-slate-800 truncate">{{ activity.description }}</div>
              <div class="text-xs text-slate-400">{{ activity.title }} · {{ timeAgo(activity.created_at) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const statsLoading = ref(true)
const stats = ref({ courses: 0, published_courses: 0, lectures: 0, tests: 0, enrolled_students: 0 })
const recentCourses = ref([])
const recentActivity = ref([])

const statCards = computed(() => [
  {
    label: 'Ваши курсы',
    value: stats.value.courses,
    valueClass: 'text-sky-600',
    iconBg: 'bg-gradient-to-br from-sky-400 to-cyan-500',
    icon: '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>',
    badge: stats.value.published_courses ? `${stats.value.published_courses} опубл.` : null,
    badgeClass: 'bg-emerald-100 text-emerald-700',
  },
  {
    label: 'Лекции',
    value: stats.value.lectures,
    valueClass: 'text-emerald-600',
    iconBg: 'bg-gradient-to-br from-emerald-400 to-green-500',
    icon: '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>',
    badge: null,
    badgeClass: '',
  },
  {
    label: 'Тесты',
    value: stats.value.tests,
    valueClass: 'text-violet-600',
    iconBg: 'bg-gradient-to-br from-violet-400 to-purple-500',
    icon: '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    badge: null,
    badgeClass: '',
  },
  {
    label: 'Студентов',
    value: stats.value.enrolled_students,
    valueClass: 'text-amber-600',
    iconBg: 'bg-gradient-to-br from-amber-400 to-orange-500',
    icon: '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
    badge: null,
    badgeClass: '',
  },
])

const pluralCourses = (n) => {
  if (n % 10 === 1 && n % 100 !== 11) return 'курс'
  if ([2, 3, 4].includes(n % 10) && ![12, 13, 14].includes(n % 100)) return 'курса'
  return 'курсов'
}

const activityBg = (type) => ({
  course: 'bg-sky-100',
  lecture: 'bg-emerald-100',
  test: 'bg-violet-100',
}[type] || 'bg-slate-100')

const activityIcon = (type) => ({
  course: '<svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13" /></svg>',
  lecture: '<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>',
  test: '<svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
}[type] || '')

const timeAgo = (iso) => {
  const diff = (Date.now() - new Date(iso).getTime()) / 1000
  if (diff < 60) return 'только что'
  if (diff < 3600) return `${Math.floor(diff / 60)} мин. назад`
  if (diff < 86400) return `${Math.floor(diff / 3600)} ч. назад`
  return new Date(iso).toLocaleDateString('ru-RU')
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/teacher/stats')
    stats.value = data.stats
    recentCourses.value = data.recent_courses
    recentActivity.value = data.recent_activity
  } catch {
    // silently fail, show zeros
  } finally {
    statsLoading.value = false
  }
})
</script>
