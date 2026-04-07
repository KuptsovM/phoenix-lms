<template>
  <div class="animate-fade-in">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
      <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
        Управление <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-500 to-purple-500">тестами</span>
      </h1>
      <button @click="showCreateModal = true" class="btn btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Создать тест
      </button>
    </div>

    <!-- Фильтры -->
    <div class="card p-4 mb-6 animate-slide-up">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Курс</label>
          <select v-model="filters.course" class="input-field">
            <option value="">Все курсы</option>
            <option v-for="course in courses" :key="course.id" :value="course.id">
              {{ course.title }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Поиск</label>
          <input 
            v-model="filters.search"
            type="text" 
            placeholder="Поиск тестов..."
            class="input-field"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Сложность</label>
          <select v-model="filters.difficulty" class="input-field">
            <option value="">Все сложности</option>
            <option value="easy">Легкий</option>
            <option value="medium">Средний</option>
            <option value="hard">Сложный</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Список тестов -->
    <div v-if="loading" class="text-center py-16">
      <div class="inline-flex items-center gap-3 text-slate-500">
        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Загрузка...
      </div>
    </div>

    <div v-else class="space-y-4">
      <div 
        v-for="(test, index) in filteredTests" 
        :key="test.id"
        class="card p-5 animate-slide-up"
        :style="{ animationDelay: `${index * 0.05}s` }"
      >
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-2">
              <h3 class="text-lg font-bold text-slate-800">{{ test.title }}</h3>
              <span :class="getDifficultyClass(test.difficulty)" class="badge">
                {{ getDifficultyText(test.difficulty) }}
              </span>
            </div>
            <p class="text-slate-500 text-sm mb-3 line-clamp-2">{{ test.description }}</p>
            <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                {{ test.course?.title }}
              </span>
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ test.questions_count }} вопросов
              </span>
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ test.duration }} мин
              </span>
            </div>
          </div>
          
          <div class="flex flex-wrap gap-2">
            <button class="btn btn-secondary text-sm py-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              Просмотр
            </button>
            <button class="btn btn-secondary text-sm py-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Редактировать
            </button>
            <button class="btn bg-red-50 text-red-600 border-red-200 hover:bg-red-100 hover:border-red-300 text-sm py-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Удалить
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCourseStore } from '../../stores/courses'

const courseStore = useCourseStore()

const loading = ref(true)
const showCreateModal = ref(false)

const filters = ref({
  course: '',
  search: '',
  difficulty: ''
})

const courses = computed(() => courseStore.courses)
const tests = ref([
  {
    id: 1,
    title: 'Тест по основам Python',
    description: 'Проверка базовых знаний Python',
    course: { id: 1, title: 'Основы программирования' },
    questions_count: 10,
    duration: 30,
    difficulty: 'easy'
  },
  {
    id: 2,
    title: 'Тест по функциям и классам',
    description: 'Продвинутые концепции Python',
    course: { id: 1, title: 'Основы программирования' },
    questions_count: 15,
    duration: 45,
    difficulty: 'medium'
  }
])

const filteredTests = computed(() => {
  return tests.value.filter(test => {
    if (filters.value.course && test.course.id !== filters.value.course) return false
    if (filters.value.difficulty && test.difficulty !== filters.value.difficulty) return false
    if (filters.value.search && !test.title.toLowerCase().includes(filters.value.search.toLowerCase())) return false
    return true
  })
})

const getDifficultyClass = (difficulty) => {
  switch (difficulty) {
    case 'easy': return 'badge-success'
    case 'medium': return 'badge-warning'
    case 'hard': return 'bg-red-100 text-red-700'
    default: return 'bg-gray-100 text-gray-800'
  }
}

const getDifficultyText = (difficulty) => {
  switch (difficulty) {
    case 'easy': return 'Легкий'
    case 'medium': return 'Средний'
    case 'hard': return 'Сложный'
    default: return difficulty
  }
}

onMounted(async () => {
  await courseStore.fetchCourses()
  loading.value = false
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
