<template>
  <div class="animate-fade-in">
    <div class="mb-8">
      <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
        Добро пожаловать, <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-500 to-cyan-500">{{ user?.name }}</span>!
      </h1>
      <p class="text-slate-500 mt-2 text-lg">
        <span v-if="authStore.isTeacher" class="inline-flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          Панель преподавателя
        </span>
        <span v-else class="inline-flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-sky-500"></span>
          Панель студента
        </span>
      </p>
    </div>

    <div class="grid grid-cols-1 gap-5 mb-8 md:grid-cols-3">
      <div class="card stat-card p-6 text-slate-800 animate-slide-up stagger-1">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
        </div>
        <div class="text-3xl font-bold text-sky-600">{{ stats.courses }}</div>
        <div class="text-slate-500 font-medium">Курсы</div>
      </div>

      <div class="card stat-card p-6 text-slate-800 animate-slide-up stagger-2">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
          </div>
        </div>
        <div class="text-3xl font-bold text-emerald-600">{{ stats.lectures }}</div>
        <div class="text-slate-500 font-medium">Лекции</div>
      </div>

      <div class="card stat-card p-6 text-slate-800 animate-slide-up stagger-3">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-400 to-violet-600 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <div class="text-3xl font-bold text-violet-600">{{ stats.tests }}</div>
        <div class="text-slate-500 font-medium">Тесты</div>
      </div>
    </div>

    <div class="card p-6 md:p-8 animate-slide-up stagger-2">
      <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
        <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        Быстрые действия
      </h2>

      <div v-if="authStore.isTeacher" class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <router-link
          to="/teacher/courses"
          class="group relative overflow-hidden p-5 rounded-2xl bg-gradient-to-br from-sky-50 to-blue-50 border border-sky-100 hover:border-sky-300 transition-all duration-300 hover:shadow-lg"
        >
          <div class="absolute top-0 right-0 w-24 h-24 bg-sky-200/40 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
          <div class="relative">
            <h3 class="font-bold text-sky-800 text-lg mb-1">Управление курсами</h3>
            <p class="text-sky-600 text-sm">Создавайте и редактируйте учебные программы</p>
          </div>
        </router-link>

        <router-link
          to="/teacher/lectures"
          class="group relative overflow-hidden p-5 rounded-2xl bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-100 hover:border-emerald-300 transition-all duration-300 hover:shadow-lg"
        >
          <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-200/40 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
          <div class="relative">
            <h3 class="font-bold text-emerald-800 text-lg mb-1">Управление лекциями</h3>
            <p class="text-emerald-600 text-sm">Добавляйте лекции и учебные материалы</p>
          </div>
        </router-link>

        <router-link
          to="/teacher/tests"
          class="group relative overflow-hidden p-5 rounded-2xl bg-gradient-to-br from-violet-50 to-purple-50 border border-violet-100 hover:border-violet-300 transition-all duration-300 hover:shadow-lg"
        >
          <div class="absolute top-0 right-0 w-24 h-24 bg-violet-200/40 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
          <div class="relative">
            <h3 class="font-bold text-violet-800 text-lg mb-1">Управление тестами</h3>
            <p class="text-violet-600 text-sm">Создавайте тесты и вопросы для проверки знаний</p>
          </div>
        </router-link>

        <router-link
          to="/admin"
          class="group relative overflow-hidden p-5 rounded-2xl bg-gradient-to-br from-slate-50 to-zinc-50 border border-slate-200 hover:border-slate-400 transition-all duration-300 hover:shadow-lg"
        >
          <div class="absolute top-0 right-0 w-24 h-24 bg-slate-200/40 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
          <div class="relative">
            <h3 class="font-bold text-slate-800 text-lg mb-1">Админ-панель</h3>
            <p class="text-slate-600 text-sm">Управление пользователями, ролями и доступом</p>
          </div>
        </router-link>
      </div>

      <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <router-link
          to="/courses"
          class="group relative overflow-hidden p-5 rounded-2xl bg-gradient-to-br from-sky-50 to-blue-50 border border-sky-100 hover:border-sky-300 transition-all duration-300 hover:shadow-lg"
        >
          <div class="absolute top-0 right-0 w-24 h-24 bg-sky-200/40 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
          <div class="relative">
            <h3 class="font-bold text-sky-800 text-lg mb-1">Мои курсы</h3>
            <p class="text-sky-600 text-sm">Просмотр доступных материалов и занятий</p>
          </div>
        </router-link>

        <router-link
          to="/courses"
          class="group relative overflow-hidden p-5 rounded-2xl bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-100 hover:border-emerald-300 transition-all duration-300 hover:shadow-lg"
        >
          <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-200/40 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform duration-500"></div>
          <div class="relative">
            <h3 class="font-bold text-emerald-800 text-lg mb-1">Прогресс обучения</h3>
            <p class="text-emerald-600 text-sm">Следите за лекциями, тестами и успехами</p>
          </div>
        </router-link>
      </div>
    </div>

    <div class="card p-6 md:p-8 mt-6 animate-slide-up stagger-3">
      <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        Последние курсы
      </h2>

      <div v-if="recentCourses.length > 0" class="space-y-3">
        <div
          v-for="(course, index) in recentCourses"
          :key="course.id"
          class="group flex flex-col gap-4 rounded-2xl border border-slate-200/80 p-5 md:flex-row md:items-center md:justify-between hover:border-sky-300 hover:bg-sky-50/50 transition-all duration-300"
        >
          <div class="flex-1 min-w-0">
            <h3 class="font-bold text-slate-800 text-lg truncate">{{ course.title }}</h3>
            <p class="text-slate-500 text-sm mt-1 line-clamp-1">{{ course.description }}</p>
          </div>

          <router-link
            :to="`/courses/${course.id}`"
            class="btn btn-primary shrink-0"
          >
            Открыть
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </router-link>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
          <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <p class="text-slate-500">Нет доступных курсов</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useCourseStore } from '../stores/courses'

const authStore = useAuthStore()
const courseStore = useCourseStore()

const user = computed(() => authStore.user)
const recentCourses = computed(() => courseStore.courses.slice(0, 3))

const stats = ref({
  courses: 0,
  lectures: 0,
  tests: 0
})

onMounted(async () => {
  await courseStore.fetchCourses()

  stats.value = {
    courses: courseStore.courses.length,
    lectures: courseStore.courses.reduce((sum, course) => sum + (course.lectures_count || 0), 0),
    tests: courseStore.courses.reduce((sum, course) => sum + (course.tests_count || 0), 0)
  }
})
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
