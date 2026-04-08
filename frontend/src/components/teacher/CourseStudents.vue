<template>
  <div class="animate-fade-in">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-slate-400 mb-6">
      <router-link to="/teacher" class="hover:text-sky-600 transition-colors">Панель</router-link>
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
      <router-link to="/teacher/courses" class="hover:text-sky-600 transition-colors">Курсы</router-link>
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
      <span class="text-slate-600 font-medium truncate max-w-[200px]">{{ courseName || 'Студенты' }}</span>
    </div>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">
          Студенты <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-500">курса</span>
        </h1>
        <p v-if="courseName" class="text-slate-500 mt-1">{{ courseName }}</p>
      </div>
      <div class="flex gap-2">
        <router-link :to="`/teacher/lectures?course=${courseId}`" class="btn btn-secondary text-sm py-2">
          Лекции
        </router-link>
        <router-link :to="`/teacher/tests?course=${courseId}`" class="btn btn-secondary text-sm py-2">
          Тесты
        </router-link>
        <router-link :to="`/courses/${courseId}`" class="btn btn-primary text-sm py-2">
          Просмотр курса →
        </router-link>
      </div>
    </div>

    <!-- Summary stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="card p-4 animate-slide-up stagger-1">
        <div class="text-2xl font-bold text-slate-800">{{ summary.total }}</div>
        <div class="text-xs text-slate-500 mt-0.5 font-medium">Записалось</div>
      </div>
      <div class="card p-4 animate-slide-up stagger-2">
        <div class="text-2xl font-bold text-emerald-600">{{ summary.completed }}</div>
        <div class="text-xs text-slate-500 mt-0.5 font-medium">Завершили</div>
      </div>
      <div class="card p-4 animate-slide-up stagger-3">
        <div class="text-2xl font-bold text-sky-600">{{ summary.avg_progress }}%</div>
        <div class="text-xs text-slate-500 mt-0.5 font-medium">Ср. прогресс</div>
      </div>
      <div class="card p-4 animate-slide-up stagger-4">
        <div class="text-2xl font-bold text-violet-600">{{ inProgressCount }}</div>
        <div class="text-xs text-slate-500 mt-0.5 font-medium">В процессе</div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-20">
      <svg class="animate-spin h-8 w-8 text-amber-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
      </svg>
      <p class="text-slate-500">Загрузка студентов...</p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="card p-8 text-center text-red-600">{{ error }}</div>

    <!-- Empty -->
    <div v-else-if="filteredStudents.length === 0 && !searchQuery" class="card p-16 text-center">
      <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-amber-100 flex items-center justify-center">
        <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
      </div>
      <p class="text-slate-500 text-lg">Пока никто не записался на этот курс</p>
      <p class="text-slate-400 text-sm mt-1">Опубликуйте курс, чтобы студенты могли записаться</p>
    </div>

    <!-- Table -->
    <div v-else class="card overflow-hidden animate-slide-up">
      <!-- Toolbar -->
      <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <div class="relative w-full sm:w-64">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Поиск по имени или email..."
            class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-400/15"
          />
        </div>

        <div class="flex items-center gap-2 text-sm text-slate-500">
          <span>Сортировка:</span>
          <select v-model="sortBy" class="text-sm border border-slate-200 rounded-lg px-2 py-1.5 focus:outline-none focus:border-sky-400 bg-white">
            <option value="enrolled_at">Дата записи</option>
            <option value="progress">Прогресс</option>
            <option value="name">Имя</option>
            <option value="best_test_score">Результат теста</option>
          </select>
          <button @click="sortDir = sortDir === 'desc' ? 'asc' : 'desc'" class="p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
            <svg class="w-4 h-4 text-slate-500 transition-transform" :class="sortDir === 'asc' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Table header (desktop) -->
      <div class="hidden md:grid grid-cols-[2fr_1fr_1fr_1fr_1fr] px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-50 bg-slate-50/50">
        <span>Студент</span>
        <span class="text-center">Прогресс</span>
        <span class="text-center">Лекции</span>
        <span class="text-center">Тест</span>
        <span class="text-center">Статус</span>
      </div>

      <!-- Rows -->
      <div class="divide-y divide-slate-50">
        <div
          v-for="(s, idx) in filteredStudents"
          :key="s.user_id"
          class="grid md:grid-cols-[2fr_1fr_1fr_1fr_1fr] gap-y-2 px-5 py-4 items-center hover:bg-slate-50/50 transition-colors"
        >
          <!-- Student info -->
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0"
              :style="{ background: avatarBg(s.name), color: '#fff' }">
              {{ initials(s.name) }}
            </div>
            <div class="min-w-0">
              <div class="font-semibold text-slate-800 text-sm truncate">{{ s.name }}</div>
              <div class="text-xs text-slate-400 truncate">{{ s.email }}</div>
              <div class="text-xs text-slate-400 md:hidden mt-0.5">Записался {{ formatDate(s.enrolled_at) }}</div>
            </div>
          </div>

          <!-- Progress bar -->
          <div class="md:px-4">
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs text-slate-500 md:hidden">Прогресс</span>
              <span class="text-sm font-bold" :class="progressColor(s.progress)">{{ s.progress }}%</span>
            </div>
            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="progressBarColor(s.progress)"
                :style="{ width: `${s.progress}%` }"
              ></div>
            </div>
          </div>

          <!-- Lectures -->
          <div class="text-center">
            <span class="text-xs text-slate-500 md:hidden block">Лекции:</span>
            <span class="text-sm font-medium text-slate-700">{{ s.lectures_viewed }}<span class="text-slate-400">/{{ s.total_lectures }}</span></span>
          </div>

          <!-- Test score -->
          <div class="text-center">
            <span class="text-xs text-slate-500 md:hidden block">Тест:</span>
            <span v-if="s.best_test_score !== null" class="text-sm font-bold" :class="s.best_test_score >= 70 ? 'text-emerald-600' : 'text-red-600'">
              {{ s.best_test_score }}%
            </span>
            <span v-else class="text-xs text-slate-400">—</span>
          </div>

          <!-- Status -->
          <div class="text-center">
            <span v-if="s.completed_at" class="badge badge-success text-xs">Завершил</span>
            <span v-else-if="s.progress > 0" class="badge bg-sky-100 text-sky-700 text-xs">В процессе</span>
            <span v-else class="badge badge-warning text-xs">Не начал</span>
          </div>
        </div>
      </div>

      <!-- Empty search state -->
      <div v-if="filteredStudents.length === 0 && searchQuery" class="py-10 text-center text-slate-400 text-sm">
        Студенты не найдены по запросу «{{ searchQuery }}»
      </div>

      <!-- Footer -->
      <div class="px-5 py-3 bg-slate-50/50 border-t border-slate-100 text-xs text-slate-400">
        Показано {{ filteredStudents.length }} из {{ students.length }} студентов
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const courseId = computed(() => route.params.courseId)

const loading = ref(true)
const error = ref(null)
const courseName = ref('')
const students = ref([])
const summary = ref({ total: 0, completed: 0, avg_progress: 0 })
const searchQuery = ref('')
const sortBy = ref('enrolled_at')
const sortDir = ref('desc')

const inProgressCount = computed(() => students.value.filter(s => s.progress > 0 && !s.completed_at).length)

const filteredStudents = computed(() => {
  let list = [...students.value]

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(s => s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q))
  }

  list.sort((a, b) => {
    let aVal = a[sortBy.value]
    let bVal = b[sortBy.value]

    if (aVal === null || aVal === undefined) aVal = sortDir.value === 'desc' ? -Infinity : Infinity
    if (bVal === null || bVal === undefined) bVal = sortDir.value === 'desc' ? -Infinity : Infinity

    if (typeof aVal === 'string') {
      return sortDir.value === 'desc' ? bVal.localeCompare(aVal) : aVal.localeCompare(bVal)
    }
    return sortDir.value === 'desc' ? bVal - aVal : aVal - bVal
  })

  return list
})

const initials = (name) => name ? name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase() : '?'

const COLORS = ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#ef4444', '#6366f1', '#0ea5e9']
const avatarBg = (name) => COLORS[(name?.charCodeAt(0) || 0) % COLORS.length]

const progressColor = (p) => p >= 100 ? 'text-emerald-600' : p >= 50 ? 'text-sky-600' : p > 0 ? 'text-amber-600' : 'text-slate-400'
const progressBarColor = (p) => p >= 100 ? 'bg-emerald-500' : p >= 50 ? 'bg-sky-500' : p > 0 ? 'bg-amber-500' : 'bg-slate-200'

const formatDate = (iso) => {
  if (!iso) return '—'
  return new Date(iso).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`/api/teacher/courses/${courseId.value}/students`)
    courseName.value = data.course.title
    students.value = data.students
    summary.value = data.summary
  } catch (e) {
    error.value = e?.response?.data?.message || 'Не удалось загрузить данные'
  } finally {
    loading.value = false
  }
})
</script>
