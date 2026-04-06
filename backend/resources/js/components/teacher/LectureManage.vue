<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Управление лекциями</h1>
      <button 
        @click="showCreateModal = true"
        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
      >
        Создать лекцию
      </button>
    </div>

    <!-- Фильтры -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <select v-model="filters.course" class="px-3 py-2 border rounded-md">
          <option value="">Все курсы</option>
          <option v-for="course in courses" :key="course.id" :value="course.id">
            {{ course.title }}
          </option>
        </select>
        <input 
          v-model="filters.search"
          type="text" 
          placeholder="Поиск лекций..."
          class="px-3 py-2 border rounded-md"
        />
        <select v-model="filters.status" class="px-3 py-2 border rounded-md">
          <option value="">Все статусы</option>
          <option value="published">Опубликованные</option>
          <option value="draft">Черновики</option>
        </select>
      </div>
    </div>

    <!-- Список лекций -->
    <div v-if="loading" class="text-center py-8">
      <div class="text-gray-500">Загрузка...</div>
    </div>

    <div v-else class="space-y-4">
      <div 
        v-for="lecture in filteredLectures" 
        :key="lecture.id"
        class="bg-white rounded-lg shadow-md p-6"
      >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ lecture.title }}</h3>
            <p class="text-gray-600 mb-2">{{ lecture.description }}</p>
            <div class="flex items-center space-x-4 text-sm text-gray-500">
              <span>📚 {{ lecture.course?.title }}</span>
              <span>📎 {{ lecture.materials_count || 0 }} материалов</span>
              <span 
                :class="getStatusClass(lecture.status)"
                class="px-2 py-1 text-xs rounded-full"
              >
                {{ getStatusText(lecture.status) }}
              </span>
            </div>
          </div>
          
          <div class="flex space-x-2 ml-4">
            <button class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
              Редактировать
            </button>
            <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
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
    case 'published':
      return 'bg-green-100 text-green-800'
    case 'draft':
      return 'bg-yellow-100 text-yellow-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'published':
      return 'Опубликована'
    case 'draft':
      return 'Черновик'
    default:
      return status
  }
}

onMounted(async () => {
  await courseStore.fetchCourses()
  loading.value = false
})
</script>
