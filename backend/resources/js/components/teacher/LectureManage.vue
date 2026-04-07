<template>
  <div class="animate-fade-in">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
      <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
        Управление <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-green-500">лекциями</span>
      </h1>
      <button @click="showCreateModal = true" class="btn btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Создать лекцию
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
            placeholder="Поиск лекций..."
            class="input-field"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Статус</label>
          <select v-model="filters.status" class="input-field">
            <option value="">Все статусы</option>
            <option value="published">Опубликованные</option>
            <option value="draft">Черновики</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Список лекций -->
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
        v-for="(lecture, index) in filteredLectures" 
        :key="lecture.id"
        class="card p-5 animate-slide-up"
        :style="{ animationDelay: `${index * 0.05}s` }"
      >
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-2">
              <h3 class="text-lg font-bold text-slate-800">{{ lecture.title }}</h3>
              <span :class="getStatusClass(lecture.status)" class="badge">
                {{ getStatusText(lecture.status) }}
              </span>
            </div>
            <p class="text-slate-500 text-sm mb-3 line-clamp-2">{{ lecture.description }}</p>
            <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                {{ lecture.course?.title }}
              </span>
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
                {{ lecture.materials_count || 0 }} материалов
              </span>
            </div>
          </div>
          
          <div class="flex gap-2">
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
  status: ''
})

const courses = computed(() => courseStore.courses)
const lectures = ref([
  {
    id: 1,
    title: 'Введение в программирование',
    description: 'Основные концепции и принципы программирования',
    course: { id: 1, title: 'Основы программирования' },
    status: 'published',
    materials_count: 3
  },
  {
    id: 2,
    title: 'Переменные и типы данных',
    description: 'Работа с переменными и типами данных в Python',
    course: { id: 1, title: 'Основы программирования' },
    status: 'draft',
    materials_count: 1
  }
])

const filteredLectures = computed(() => {
  return lectures.value.filter(lecture => {
    if (filters.value.course && lecture.course.id !== filters.value.course) return false
    if (filters.value.status && lecture.status !== filters.value.status) return false
    if (filters.value.search && !lecture.title.toLowerCase().includes(filters.value.search.toLowerCase())) return false
    return true
  })
})

const getStatusClass = (status) => {
  switch (status) {
    case 'published': return 'badge-success'
    case 'draft': return 'badge-warning'
    default: return 'bg-gray-100 text-gray-800'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'published': return 'Опубликована'
    case 'draft': return 'Черновик'
    default: return status
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
