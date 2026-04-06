<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Управление тестами</h1>
      <button 
        @click="showCreateModal = true"
        class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600"
      >
        Создать тест
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
          placeholder="Поиск тестов..."
          class="px-3 py-2 border rounded-md"
        />
        <select v-model="filters.difficulty" class="px-3 py-2 border rounded-md">
          <option value="">Все сложности</option>
          <option value="easy">Легкий</option>
          <option value="medium">Средний</option>
          <option value="hard">Сложный</option>
        </select>
      </div>
    </div>

    <!-- Список тестов -->
    <div v-if="loading" class="text-center py-8">
      <div class="text-gray-500">Загрузка...</div>
    </div>

    <div v-else class="space-y-4">
      <div 
        v-for="test in filteredTests" 
        :key="test.id"
        class="bg-white rounded-lg shadow-md p-6"
      >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ test.title }}</h3>
            <p class="text-gray-600 mb-2">{{ test.description }}</p>
            <div class="flex items-center space-x-4 text-sm text-gray-500">
              <span>📚 {{ test.course?.title }}</span>
              <span>❓ {{ test.questions_count }} вопросов</span>
              <span>⏱️ {{ test.duration }} мин</span>
              <span 
                :class="getDifficultyClass(test.difficulty)"
                class="px-2 py-1 text-xs rounded-full"
              >
                {{ getDifficultyText(test.difficulty) }}
              </span>
            </div>
          </div>
          
          <div class="flex space-x-2 ml-4">
            <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
              Просмотр
            </button>
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
    case 'easy':
      return 'bg-green-100 text-green-800'
    case 'medium':
      return 'bg-yellow-100 text-yellow-800'
    case 'hard':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getDifficultyText = (difficulty) => {
  switch (difficulty) {
    case 'easy':
      return 'Легкий'
    case 'medium':
      return 'Средний'
    case 'hard':
      return 'Сложный'
    default:
      return difficulty
  }
}

onMounted(async () => {
  await courseStore.fetchCourses()
  loading.value = false
})
</script>
