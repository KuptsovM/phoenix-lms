<template>
  <div class="animate-fade-in">
    <div class="mb-8">
      <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
        Панель <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-500 to-purple-500">преподавателя</span>
      </h1>
      <p class="text-slate-500 mt-2 text-lg">Управление курсами, лекциями и тестами</p>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 gap-5 mb-8 md:grid-cols-4">
      <div class="card stat-card p-6 text-slate-800 animate-slide-up stagger-1">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-400 to-cyan-500 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
        </div>
        <div class="text-3xl font-bold text-sky-600">{{ stats.courses }}</div>
        <div class="text-slate-500 font-medium">Ваши курсы</div>
      </div>

      <div class="card stat-card p-6 text-slate-800 animate-slide-up stagger-2">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center">
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
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <div class="text-3xl font-bold text-violet-600">{{ stats.tests }}</div>
        <div class="text-slate-500 font-medium">Тесты</div>
      </div>

      <div class="card stat-card p-6 text-slate-800 animate-slide-up stagger-4">
        <div class="flex items-center justify-between mb-3">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
        </div>
        <div class="text-3xl font-bold text-amber-600">{{ stats.students }}</div>
        <div class="text-slate-500 font-medium">Студенты</div>
      </div>
    </div>

    <!-- Быстрые действия -->
    <div class="grid grid-cols-1 gap-5 mb-8 md:grid-cols-3">
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
          <h3 class="font-bold text-sky-800 text-lg mb-2">Управление курсами</h3>
          <p class="text-sky-600 text-sm">Создавайте новые курсы и редактируйте существующие</p>
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
          <h3 class="font-bold text-emerald-800 text-lg mb-2">Управление лекциями</h3>
          <p class="text-emerald-600 text-sm">Добавляйте лекции и учебные материалы</p>
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
          <h3 class="font-bold text-violet-800 text-lg mb-2">Управление тестами</h3>
          <p class="text-violet-600 text-sm">Создавайте тесты для проверки знаний</p>
        </div>
      </router-link>
    </div>

    <!-- Последние действия -->
    <div class="card p-6 md:p-8 animate-slide-up stagger-4">
      <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Последние действия
      </h2>
      
      <div v-if="recentActivities.length > 0" class="space-y-3">
        <div 
          v-for="(activity, index) in recentActivities" 
          :key="activity.id"
          class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-xl border border-slate-200/80 hover:border-sky-300 hover:bg-sky-50/30 transition-all duration-200"
          :style="{ animationDelay: `${index * 0.05}s` }"
        >
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center shrink-0" :class="getActivityBg(activity.type)">
              <span v-html="getActivityIcon(activity.type)"></span>
            </div>
            <div class="min-w-0">
              <div class="font-bold text-slate-800 text-sm sm:text-base truncate">{{ activity.title }}</div>
              <div class="text-sm text-slate-500 truncate">{{ activity.description }}</div>
            </div>
          </div>
          <div class="text-xs sm:text-sm text-slate-400 whitespace-nowrap sm:ml-4">
            {{ formatDate(activity.created_at) }}
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-12">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
          <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <p class="text-slate-500">Нет недавних действий</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCourseStore } from '../../stores/courses'

const courseStore = useCourseStore()

const stats = ref({
  courses: 0,
  lectures: 0,
  tests: 0,
  students: 0
})

const recentActivities = ref([
  {
    id: 1,
    type: 'course',
    title: 'Создан новый курс',
    description: 'Основы программирования на Python',
    created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString()
  },
  {
    id: 2,
    type: 'lecture',
    title: 'Добавлена лекция',
    description: 'Введение в алгоритмы',
    created_at: new Date(Date.now() - 5 * 60 * 60 * 1000).toISOString()
  },
  {
    id: 3,
    type: 'test',
    title: 'Создан тест',
    description: 'Тест по основам Python',
    created_at: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString()
  }
])

const getActivityIcon = (type) => {
  const icons = {
    course: '<svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>',
    lecture: '<svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>',
    test: '<svg class="w-5 h-5 sm:w-6 sm:h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
  }
  return icons[type] || icons.course
}

const getActivityBg = (type) => {
  const bgs = {
    course: 'bg-sky-100',
    lecture: 'bg-emerald-100',
    test: 'bg-violet-100'
  }
  return bgs[type] || bgs.course
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))
  
  if (diffInHours < 1) {
    return 'Только что'
  } else if (diffInHours < 24) {
    return `${diffInHours} ч. назад`
  } else {
    return date.toLocaleDateString('ru-RU')
  }
}

onMounted(async () => {
  await courseStore.fetchCourses()
  
  stats.value = {
    courses: courseStore.courses.length,
    lectures: courseStore.courses.reduce((sum, course) => sum + (course.lectures_count || 0), 0),
    tests: courseStore.courses.reduce((sum, course) => sum + (course.tests_count || 0), 0),
    students: 42
  }
})
</script>
