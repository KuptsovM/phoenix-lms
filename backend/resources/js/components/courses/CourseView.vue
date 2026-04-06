<template>
  <div>
    <div v-if="loading" class="text-center py-8">
      <div class="text-gray-500">Загрузка...</div>
    </div>

    <div v-else-if="!course" class="text-center py-8">
      <div class="text-gray-500">Курс не найден</div>
    </div>

    <div v-else>
      <!-- Заголовок курса -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ course.title }}</h1>
            <p class="text-gray-600">{{ course.description }}</p>
          </div>
          <span 
            :class="getStatusClass(course.status)"
            class="px-3 py-1 text-sm rounded-full"
          >
            {{ getStatusText(course.status) }}
          </span>
        </div>
        
        <div class="flex items-center text-sm text-gray-500 space-x-4">
          <span>Автор: {{ course.author?.name }}</span>
          <span>Создан: {{ formatDate(course.created_at) }}</span>
        </div>
      </div>

      <!-- Навигация по курсу -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Содержание курса -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Содержание курса</h2>
            
            <!-- Лекции -->
            <div v-if="lectures.length > 0" class="mb-6">
              <h3 class="font-semibold text-gray-700 mb-3">📚 Лекции</h3>
              <div class="space-y-2">
                <div 
                  v-for="lecture in lectures" 
                  :key="lecture.id"
                  class="p-3 border rounded-lg hover:bg-gray-50 cursor-pointer"
                  @click="openLecture(lecture)"
                >
                  <div class="font-medium">{{ lecture.title }}</div>
                  <div class="text-sm text-gray-500">{{ lecture.description }}</div>
                </div>
              </div>
            </div>
            
            <!-- Тесты -->
            <div v-if="tests.length > 0">
              <h3 class="font-semibold text-gray-700 mb-3">📝 Тесты</h3>
              <div class="space-y-2">
                <div 
                  v-for="test in tests" 
                  :key="test.id"
                  class="p-3 border rounded-lg hover:bg-gray-50 cursor-pointer"
                  @click="openTest(test)"
                >
                  <div class="font-medium">{{ test.title }}</div>
                  <div class="text-sm text-gray-500">{{ test.questions_count }} вопросов</div>
                </div>
              </div>
            </div>
            
            <div v-if="lectures.length === 0 && tests.length === 0" class="text-gray-500">
              Нет доступных материалов
            </div>
          </div>
        </div>

        <!-- Основной контент -->
        <div class="lg:col-span-2">
          <div v-if="selectedLecture" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-4">{{ selectedLecture.title }}</h2>
            <div class="prose max-w-none" v-html="selectedLecture.content"></div>
            
            <!-- Материалы лекции -->
            <div v-if="lectureMaterials.length > 0" class="mt-6">
              <h3 class="text-lg font-semibold mb-3">Материалы лекции</h3>
              <div class="space-y-2">
                <a 
                  v-for="material in lectureMaterials" 
                  :key="material.id"
                  :href="material.file_url"
                  target="_blank"
                  class="flex items-center p-3 border rounded-lg hover:bg-gray-50"
                >
                  <span class="mr-2">📎</span>
                  <span>{{ material.title }}</span>
                  <span class="ml-auto text-sm text-gray-500">{{ material.file_size }}</span>
                </a>
              </div>
            </div>
          </div>
          
          <div v-else-if="selectedTest" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-4">{{ selectedTest.title }}</h2>
            <p class="text-gray-600 mb-6">{{ selectedTest.description }}</p>
            
            <div v-if="!testStarted" class="text-center">
              <button 
                @click="startTest"
                class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
              >
                Начать тест
              </button>
            </div>
            
            <div v-else>
              <TestComponent 
                :test="selectedTest"
                @submit="handleTestSubmit"
              />
            </div>
          </div>
          
          <div v-else class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
            Выберите лекцию или тест для просмотра
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCourseStore } from '../../stores/courses'
import TestComponent from '../tests/TestComponent.vue'

const route = useRoute()
const courseStore = useCourseStore()

const loading = ref(true)
const selectedLecture = ref(null)
const selectedTest = ref(null)
const testStarted = ref(false)

const course = computed(() => courseStore.currentCourse)
const lectures = computed(() => courseStore.lectures)
const tests = computed(() => courseStore.tests)
const lectureMaterials = computed(() => courseStore.materials)

const getStatusClass = (status) => {
  switch (status) {
    case 'published':
      return 'bg-green-100 text-green-800'
    case 'draft':
      return 'bg-yellow-100 text-yellow-800'
    case 'archived':
      return 'bg-gray-100 text-gray-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'published':
      return 'Опубликован'
    case 'draft':
      return 'Черновик'
    case 'archived':
      return 'В архиве'
    default:
      return status
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('ru-RU')
}

const openLecture = async (lecture) => {
  selectedLecture.value = lecture
  selectedTest.value = null
  testStarted.value = false
  await courseStore.fetchMaterials(lecture.id)
}

const openTest = (test) => {
  selectedTest.value = test
  selectedLecture.value = null
  testStarted.value = false
}

const startTest = () => {
  testStarted.value = true
}

const handleTestSubmit = async (answers) => {
  try {
    const result = await courseStore.submitTestAnswers(selectedTest.value.id, answers)
    // Показать результаты
    alert(`Тест завершен! Ваш результат: ${result.score}/${result.total_points}`)
    testStarted.value = false
  } catch (error) {
    alert('Ошибка при отправке теста')
  }
}

onMounted(async () => {
  const courseId = route.params.id
  await courseStore.fetchCourse(courseId)
  await courseStore.fetchLectures(courseId)
  await courseStore.fetchTests(courseId)
  loading.value = false
})
</script>
