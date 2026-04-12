<template>
  <div class="animate-fade-in">

    <!-- ── TEACHER DASHBOARD ── -->
    <template v-if="authStore.isTeacher">
      <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
          Добро пожаловать, <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-500">{{ user?.name }}</span>!
        </h1>
        <p class="text-slate-500 mt-2 text-lg inline-flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
          Панель преподавателя
        </p>
      </div>

      <div class="grid grid-cols-1 gap-5 mb-8 md:grid-cols-3">
        <div class="card p-6 animate-slide-up stagger-1">
          <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
          </div>
          <div class="text-3xl font-bold text-sky-600">{{ teacherStats.courses }}</div>
          <div class="text-slate-500 font-medium">Курсов</div>
        </div>
        <div class="card p-6 animate-slide-up stagger-2">
          <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
            </div>
          </div>
          <div class="text-3xl font-bold text-emerald-600">{{ teacherStats.lectures }}</div>
          <div class="text-slate-500 font-medium">Лекций</div>
        </div>
        <div class="card p-6 animate-slide-up stagger-3">
          <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-400 to-violet-600 flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
          </div>
          <div class="text-3xl font-bold text-violet-600">{{ teacherStats.tests }}</div>
          <div class="text-slate-500 font-medium">Тестов</div>
        </div>
      </div>

      <div class="card p-6 md:p-8 animate-slide-up stagger-2">
        <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
          <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
          Быстрые действия
        </h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <router-link to="/teacher/courses" class="quick-action-card quick-action-card--sky">
            <div class="quick-action-blob"></div>
            <h3 class="font-bold text-sky-800 text-lg mb-1">Управление курсами</h3>
            <p class="text-sky-600 text-sm">Создавайте и редактируйте учебные программы</p>
          </router-link>
          <router-link to="/teacher/lectures" class="quick-action-card quick-action-card--emerald">
            <div class="quick-action-blob"></div>
            <h3 class="font-bold text-emerald-800 text-lg mb-1">Управление лекциями</h3>
            <p class="text-emerald-600 text-sm">Добавляйте лекции и учебные материалы</p>
          </router-link>
          <router-link to="/teacher/tests" class="quick-action-card quick-action-card--violet">
            <div class="quick-action-blob"></div>
            <h3 class="font-bold text-violet-800 text-lg mb-1">Управление тестами</h3>
            <p class="text-violet-600 text-sm">Создавайте тесты и вопросы для проверки знаний</p>
          </router-link>
          <router-link to="/admin" class="quick-action-card quick-action-card--slate">
            <div class="quick-action-blob"></div>
            <h3 class="font-bold text-slate-800 text-lg mb-1">Админ-панель</h3>
            <p class="text-slate-600 text-sm">Управление пользователями, ролями и доступом</p>
          </router-link>
        </div>
      </div>
    </template>

    <!-- ── STUDENT DASHBOARD ── -->
    <template v-else>

      <!-- Greeting -->
      <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <div class="text-sm font-medium text-sky-500 mb-1">{{ timeGreeting }}</div>
          <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
            {{ user?.name }}!
          </h1>
          <p class="text-slate-500 mt-1">Продолжай обучение там, где остановился</p>
        </div>
        <div class="flex gap-3">
          <router-link to="/my-courses" class="btn btn-primary gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            Мои курсы
          </router-link>
          <router-link to="/courses" class="btn btn-secondary gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            Каталог
          </router-link>
        </div>
      </div>

      <!-- Stats Row -->
      <div v-if="loadingDashboard" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div v-for="i in 4" :key="i" class="card p-5 animate-pulse"><div class="h-12 bg-slate-100 rounded-xl"></div></div>
      </div>
      <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="card p-5 animate-slide-up stagger-1">
          <div class="text-3xl font-bold text-sky-600 mb-1">{{ stats.enrolled_courses }}</div>
          <div class="text-sm text-slate-500">Записан на курсы</div>
          <div class="mt-2 text-xs text-slate-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            всего курсов
          </div>
        </div>
        <div class="card p-5 animate-slide-up stagger-2">
          <div class="text-3xl font-bold text-amber-600 mb-1">{{ stats.in_progress }}</div>
          <div class="text-sm text-slate-500">В процессе</div>
          <div class="mt-2 text-xs text-slate-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            активных
          </div>
        </div>
        <div class="card p-5 animate-slide-up stagger-3">
          <div class="text-3xl font-bold text-emerald-600 mb-1">{{ stats.completed }}</div>
          <div class="text-sm text-slate-500">Завершено</div>
          <div class="mt-2 text-xs text-slate-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            пройдено
          </div>
        </div>
        <div class="card p-5 animate-slide-up stagger-4">
          <div class="text-3xl font-bold text-violet-600 mb-1">{{ stats.avg_test_score }}%</div>
          <div class="text-sm text-slate-500">Средний балл</div>
          <div class="mt-2 text-xs text-slate-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 8H5a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2v-8a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            по тестам
          </div>
        </div>
      </div>

      <!-- Continue Learning Banner -->
      <div v-if="continueLearning" class="continue-banner mb-8 animate-slide-up">
        <div class="continue-banner-content">
          <div class="continue-label">Продолжить обучение</div>
          <h2 class="continue-title">{{ continueLearning.course_title }}</h2>
          <div class="flex items-center gap-4 mt-3">
            <div class="flex-1 max-w-xs">
              <div class="flex justify-between text-xs text-white/70 mb-1">
                <span>Прогресс</span>
                <span>{{ continueProgress }}%</span>
              </div>
              <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-white rounded-full transition-all" :style="{ width: continueProgress + '%' }"></div>
              </div>
            </div>
            <router-link :to="`/courses/${continueLearning.course_id}`" class="btn-continue">
              Перейти к курсу
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </router-link>
          </div>
        </div>
        <div class="continue-banner-art">
          <svg class="w-24 h-24 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
        </div>
      </div>
      <!-- Empty state - not enrolled anywhere -->
      <div v-else-if="!loadingDashboard && stats.enrolled_courses === 0" class="enroll-cta mb-8">
        <div class="enroll-cta-icon">📚</div>
        <h2 class="enroll-cta-title">Начни своё обучение</h2>
        <p class="enroll-cta-text">Изучи каталог курсов и запишись на первый курс прямо сейчас</p>
        <router-link to="/courses" class="btn btn-primary mt-4">Открыть каталог курсов</router-link>
      </div>

      <!-- Active Courses Grid -->
      <div v-if="activeCourses.length > 0" class="mb-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            Активные курсы
          </h2>
          <router-link to="/my-courses" class="text-sm text-sky-500 hover:text-sky-700 font-medium flex items-center gap-1 transition-colors">
            Все мои курсы
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </router-link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <router-link
            v-for="(course, i) in activeCourses"
            :key="course.id"
            :to="`/courses/${course.id}`"
            class="course-progress-card animate-slide-up"
            :style="{ animationDelay: `${i * 0.06}s` }"
          >
            <div class="course-progress-bar-track">
              <div class="course-progress-bar-fill" :style="{ width: course.progress + '%' }"></div>
            </div>
            <div class="p-4">
              <div class="flex items-start justify-between mb-2">
                <h3 class="font-bold text-slate-800 text-base line-clamp-1 flex-1 mr-2">{{ course.title }}</h3>
                <span class="course-progress-pct" :class="course.progress >= 100 ? 'text-emerald-600' : 'text-sky-600'">
                  {{ course.progress }}%
                </span>
              </div>
              <p class="text-slate-500 text-sm line-clamp-1 mb-3">{{ course.description }}</p>
              <div class="flex items-center justify-between text-xs text-slate-400">
                <span>{{ course.viewed_lectures }}/{{ course.total_lectures }} лекций</span>
                <span v-if="course.last_accessed_at">{{ formatDate(course.last_accessed_at) }}</span>
                <span v-else>Начать</span>
              </div>
            </div>
          </router-link>
        </div>
      </div>

      <!-- Recent Test Results -->
      <div v-if="recentResults.length > 0" class="card p-6 animate-slide-up stagger-3">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 8H5a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2v-8a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
          Последние тесты
        </h2>
        <div class="space-y-3">
          <div
            v-for="result in recentResults"
            :key="result.id"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors"
          >
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
              :class="result.passed ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-500'">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="result.passed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="font-medium text-slate-800 text-sm truncate">{{ result.test_title }}</div>
              <div class="text-xs text-slate-400">{{ formatDate(result.completed_at) }}</div>
            </div>
            <div class="text-right shrink-0">
              <div class="font-bold" :class="result.passed ? 'text-emerald-600' : 'text-red-500'">
                {{ result.percentage }}%
              </div>
              <div class="text-xs text-slate-400">{{ result.score }}/{{ result.total_points }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recommended Courses -->
      <div v-if="recommendedCourses.length > 0" class="mb-8 mt-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
            Рекомендуемые курсы
          </h2>
          <router-link to="/courses" class="text-sm text-indigo-500 hover:text-indigo-700 font-medium flex items-center gap-1 transition-colors">
            Все курсы
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </router-link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <router-link
            v-for="(course, i) in recommendedCourses"
            :key="course.id"
            :to="`/courses/${course.id}`"
            class="course-progress-card hover:border-indigo-400 p-4 animate-slide-up flex flex-col justify-between shadow-sm"
            :style="{ animationDelay: `${i * 0.06}s` }"
          >
            <div>
              <h3 class="font-bold text-slate-800 text-base line-clamp-2 mb-2">{{ course.title }}</h3>
              <p class="text-slate-500 text-xs line-clamp-3 mb-3">{{ course.description }}</p>
            </div>
            <div class="flex items-center justify-between text-xs text-slate-400 mt-auto pt-3 border-t border-slate-100">
              <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg> {{ course.lectures_count }}</span>
              <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 8H5a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2v-8a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg> {{ course.tests_count }}</span>
            </div>
          </router-link>
        </div>
      </div>

    </template>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useCourseStore } from '../stores/courses'
import { useStudentStore } from '../stores/studentStore'

const authStore    = useAuthStore()
const courseStore  = useCourseStore()
const studentStore = useStudentStore()

const user = computed(() => authStore.user)

const loadingDashboard = ref(true)

/* ── Teacher stats ── */
const teacherStats = ref({ courses: 0, lectures: 0, tests: 0 })

/* ── Student data (derived from studentStore.dashboard) ── */
const stats = computed(() => studentStore.dashboard?.stats ?? {
  enrolled_courses: 0, in_progress: 0, completed: 0, avg_test_score: 0,
})
const continueLearning = computed(() => studentStore.dashboard?.continue_learning)
const activeCourses    = computed(() => studentStore.dashboard?.active_courses ?? [])
const recentResults    = computed(() => studentStore.dashboard?.recent_results ?? [])
const recommendedCourses = computed(() => studentStore.dashboard?.recommended_courses ?? [])

const continueProgress = computed(() => {
  const cl = continueLearning.value
  if (!cl || !cl.total_lectures) return 0
  return Math.round((cl.progress / cl.total_lectures) * 100)
})

const timeGreeting = computed(() => {
  const h = new Date().getHours()
  if (h < 12) return 'Доброе утро,'
  if (h < 18) return 'Добрый день,'
  return 'Добрый вечер,'
})

const formatDate = (iso) => {
  if (!iso) return ''
  return new Date(iso).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' })
}

onMounted(async () => {
  if (authStore.isTeacher) {
    await courseStore.fetchCourses()
    teacherStats.value = {
      courses:  courseStore.courses.length,
      lectures: courseStore.courses.reduce((s, c) => s + (c.lectures_count || 0), 0),
      tests:    courseStore.courses.reduce((s, c) => s + (c.tests_count || 0), 0),
    }
  } else {
    try { await studentStore.fetchDashboard() } catch { /* handle gracefully */ }
  }
  loadingDashboard.value = false
})
</script>

<style scoped>
.stagger-1 { animation-delay: 0.05s; }
.stagger-2 { animation-delay: 0.10s; }
.stagger-3 { animation-delay: 0.15s; }
.stagger-4 { animation-delay: 0.20s; }

/* Quick action cards */
.quick-action-card {
  position: relative;
  overflow: hidden;
  padding: 1.25rem;
  border-radius: 1rem;
  border-width: 1px;
  border-style: solid;
  transition: all 0.3s ease;
}
.quick-action-card:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
.quick-action-card .quick-action-blob {
  position: absolute;
  top: 0; right: 0;
  width: 6rem; height: 6rem;
  border-radius: 50%;
  transform: translateY(-50%) translateX(50%);
  transition: transform 0.5s ease;
}
.quick-action-card:hover .quick-action-blob { transform: translateY(-50%) translateX(50%) scale(1.5); }
.quick-action-card--sky  { background: linear-gradient(135deg, #f0f9ff, #eff6ff); border-color: #e0f2fe; }
.quick-action-card--sky:hover { border-color: #7dd3fc; }
.quick-action-card--sky .quick-action-blob { background: rgba(186,230,253,0.4); }
.quick-action-card--emerald { background: linear-gradient(135deg, #ecfdf5, #f0fdf4); border-color: #d1fae5; }
.quick-action-card--emerald:hover { border-color: #6ee7b7; }
.quick-action-card--emerald .quick-action-blob { background: rgba(167,243,208,0.4); }
.quick-action-card--violet { background: linear-gradient(135deg, #f5f3ff, #faf5ff); border-color: #ede9fe; }
.quick-action-card--violet:hover { border-color: #c4b5fd; }
.quick-action-card--violet .quick-action-blob { background: rgba(196,181,253,0.4); }
.quick-action-card--slate { background: linear-gradient(135deg, #f8fafc, #fafafa); border-color: #e2e8f0; }
.quick-action-card--slate:hover { border-color: #94a3b8; }
.quick-action-card--slate .quick-action-blob { background: rgba(226,232,240,0.4); }

/* Continue Learning Banner */
.continue-banner {
  position: relative;
  overflow: hidden;
  border-radius: 20px;
  padding: 2rem;
  background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}
.continue-banner-content { flex: 1; min-width: 0; }
.continue-label { font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.25rem; }
.continue-title { font-size: 1.5rem; font-weight: 800; color: white; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.continue-banner-art { display: none; }
@media (min-width: 640px) { .continue-banner-art { display: flex; } }

.btn-continue {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  border-radius: 12px;
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(4px);
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
  border: 1px solid rgba(255,255,255,0.25);
  white-space: nowrap;
  transition: all 0.2s ease;
}
.btn-continue:hover { background: rgba(255,255,255,0.25); }

/* Enroll CTA */
.enroll-cta {
  text-align: center;
  padding: 3rem 2rem;
  background: white;
  border-radius: 20px;
  border: 2px dashed #e2e8f0;
}
.enroll-cta-icon { font-size: 3rem; margin-bottom: 0.75rem; }
.enroll-cta-title { font-size: 1.5rem; font-weight: 800; color: #0f172a; }
.enroll-cta-text { color: #64748b; margin-top: 0.5rem; }

/* Course Progress Card */
.course-progress-card {
  background: white;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  transition: all 0.2s ease;
  text-decoration: none;
  display: block;
}
.course-progress-card:hover {
  border-color: #38bdf8;
  box-shadow: 0 4px 20px rgba(56,189,248,0.15);
  transform: translateY(-2px);
}
.course-progress-bar-track {
  height: 4px;
  background: #f1f5f9;
}
.course-progress-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #38bdf8, #818cf8);
  border-radius: 2px;
  transition: width 0.5s ease;
}
.course-progress-pct {
  font-size: 0.875rem;
  font-weight: 700;
  white-space: nowrap;
}

.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
</style>
