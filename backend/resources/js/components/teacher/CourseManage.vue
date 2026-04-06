<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Управление курсами</h1>
      <button 
        @click="showCreateModal = true"
        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
      >
        Создать курс
      </button>
    </div>

    <!-- Модальное окно создания курса -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-semibold mb-4">Создание нового курса</h3>
        
        <form @submit.prevent="createCourse">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Название курса</label>
            <input
              v-model="courseForm.title"
              type="text"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>
          
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Описание</label>
            <textarea
              v-model="courseForm.description"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="4"
              required
            ></textarea>
          </div>
          
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Статус</label>
            <select
              v-model="courseForm.status"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="draft">Черновик</option>
              <option value="published">Опубликован</option>
            </select>
          </div>
          
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="showCreateModal = false"
              class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
            >
              Отмена
            </button>
            <button
              type="submit"
              :disabled="creating"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:bg-gray-400"
            >
              {{ creating ? 'Создание...' : 'Создать курс' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Список курсов -->
    <div v-if="loading" class="text-center py-8">
      <div class="text-gray-500">Загрузка...</div>
    </div>

    <div v-else-if="courses.length === 0" class="text-center py-8">
      <div class="text-gray-500 mb-4">У вас пока нет курсов</div>
      <button 
        @click="showCreateModal = true"
        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
      >
        Создать первый курс
      </button>
    </div>

    <div v-else class="space-y-6">
      <div 
        v-for="course in courses" 
        :key="course.id"
        class="bg-white rounded-lg shadow-md p-6"
      >
        <div class="flex justify-between items-start mb-4">
          <div class="flex-1">
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ course.title }}</h3>
            <p class="text-gray-600 mb-2">{{ course.description }}</p>
            
            <div class="flex items-center space-x-4 text-sm text-gray-500">
              <span>📚 {{ course.lectures_count || 0 }} лекций</span>
              <span>📝 {{ course.tests_count || 0 }} тестов</span>
              <span 
                :class="getStatusClass(course.status)"
                class="px-2 py-1 text-xs rounded-full"
              >
                {{ getStatusText(course.status) }}
              </span>
            </div>
          </div>
          
          <div class="flex space-x-2 ml-4">
            <button 
              @click="editCourse(course)"
              class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600"
            >
              Редактировать
            </button>
            <button 
              @click="deleteCourse(course.id)"
              class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
            >
              Удалить
            </button>
          </div>
        </div>
        
        <!-- Дополнительные действия -->
        <div class="flex space-x-4 pt-4 border-t">
          <router-link 
            :to="`/courses/${course.id}`"
            class="text-blue-600 hover:text-blue-800"
          >
            Просмотр курса
          </router-link>
          <span class="text-gray-400">|</span>
          <button 
            @click="manageLectures(course)"
            class="text-green-600 hover:text-green-800"
          >
            Управление лекциями
          </button>
          <span class="text-gray-400">|</span>
          <button 
            @click="manageTests(course)"
            class="text-purple-600 hover:text-purple-800"
          >
            Управление тестами
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCourseStore } from '../../stores/courses'

const courseStore = useCourseStore()

const showCreateModal = ref(false)
const creating = ref(false)
const loading = ref(true)

const courseForm = ref({
  title: '',
  description: '',
  status: 'draft'
})

const courses = computed(() => courseStore.courses)

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

const createCourse = async () => {
  creating.value = true
  try {
    await courseStore.createCourse(courseForm.value)
    showCreateModal.value = false
    courseForm.value = { title: '', description: '', status: 'draft' }
  } catch (error) {
    alert('Ошибка при создании курса')
  } finally {
    creating.value = false
  }
}

const editCourse = (course) => {
  // Заглушка для редактирования
  console.log('Edit course:', course)
}

const deleteCourse = async (courseId) => {
  if (confirm('Вы уверены, что хотите удалить этот курс?')) {
    try {
      await courseStore.deleteCourse(courseId)
    } catch (error) {
      alert('Ошибка при удалении курса')
    }
  }
}

const manageLectures = (course) => {
  // Перенаправление на управление лекциями
  console.log('Manage lectures for course:', course)
}

const manageTests = (course) => {
  // Перенаправление на управление тестами
  console.log('Manage tests for course:', course)
}

onMounted(async () => {
  await courseStore.fetchCourses()
  loading.value = false
})
</script>
