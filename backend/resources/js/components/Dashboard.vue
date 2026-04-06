<template>
  <div>
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Добро пожаловать, {{ user?.name }}!</h1>
      <p class="text-gray-600 mt-2">
        <span v-if="authStore.isTeacher">Панель преподавателя</span>
        <span v-else>Панель студента</span>
      </p>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-2xl font-bold text-blue-600">{{ stats.courses }}</div>
        <div class="text-gray-600">Курсы</div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-2xl font-bold text-green-600">{{ stats.lectures }}</div>
        <div class="text-gray-600">Лекции</div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-2xl font-bold text-purple-600">{{ stats.tests }}</div>
        <div class="text-gray-600">Тесты</div>
      </div>
    </div>

    <!-- Быстрые действия -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-bold mb-4">Быстрые действия</h2>
      
      <div v-if="authStore.isTeacher" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <router-link 
          to="/teacher/courses" 
          class="block p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
        >
          <h3 class="font-semibold text-blue-800">Управление курсами</h3>
          <p class="text-blue-600 text-sm">Создавайте и редактируйте курсы</p>
        </router-link>
        
        <router-link 
          to="/teacher/lectures" 
          class="block p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
        >
          <h3 class="font-semibold text-green-800">Управление лекциями</h3>
          <p class="text-green-600 text-sm">Добавляйте лекции и материалы</p>
        </router-link>
        
        <router-link 
          to="/teacher/tests" 
          class="block p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors"
        >
          <h3 class="font-semibold text-purple-800">Управление тестами</h3>
          <p class="text-purple-600 text-sm">Создавайте тесты и задания</p>
        </router-link>
        
        <router-link 
          to="/admin" 
          class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <h3 class="font-semibold text-gray-800">Админ панель</h3>
          <p class="text-gray-600 text-sm">Управление пользователями и ролями</p>
        </router-link>
      </div>
      
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <router-link 
          to="/courses" 
          class="block p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
        >
          <h3 class="font-semibold text-blue-800">Мои курсы</h3>
          <p class="text-blue-600 text-sm">Просмотр доступных курсов</p>
        </router-link>
        
        <router-link 
          to="/courses" 
          class="block p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
        >
          <h3 class="font-semibold text-green-800">Прогресс обучения</h3>
          <p class="text-green-600 text-sm">Отслеживайте свой прогресс</p>
        </router-link>
      </div>
    </div>

    <!-- Последние курсы -->
    <div class="bg-white p-6 rounded-lg shadow-md mt-6">
      <h2 class="text-xl font-bold mb-4">Последние курсы</h2>
      <div v-if="recentCourses.length > 0" class="space-y-3">
        <div 
          v-for="course in recentCourses" 
          :key="course.id"
          class="flex justify-between items-center p-3 border rounded-lg hover:bg-gray-50"
        >
          <div>
            <h3 class="font-semibold">{{ course.title }}</h3>
            <p class="text-sm text-gray-600">{{ course.description }}</p>
          </div>
          <router-link 
            :to="`/courses/${course.id}`" 
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Открыть
          </router-link>
        </div>
      </div>
      <div v-else class="text-gray-500">
        Нет доступных курсов
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
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
