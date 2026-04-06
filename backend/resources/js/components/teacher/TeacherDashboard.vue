<template>
  <div>
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Панель преподавателя</h1>
      <p class="text-gray-600 mt-2">Управление курсами, лекциями и тестами</p>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-2xl font-bold text-blue-600">{{ stats.courses }}</div>
        <div class="text-gray-600">Ваши курсы</div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-2xl font-bold text-green-600">{{ stats.lectures }}</div>
        <div class="text-gray-600">Лекции</div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-2xl font-bold text-purple-600">{{ stats.tests }}</div>
        <div class="text-gray-600">Тесты</div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-2xl font-bold text-orange-600">{{ stats.students }}</div>
        <div class="text-gray-600">Студенты</div>
      </div>
    </div>

    <!-- Быстрые действия -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <router-link 
        to="/teacher/courses" 
        class="block p-6 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
      >
        <div class="text-blue-800 text-2xl mb-3">📚</div>
        <h3 class="font-semibold text-blue-800 mb-2">Управление курсами</h3>
        <p class="text-blue-600 text-sm">Создавайте новые курсы и редактируйте существующие</p>
      </router-link>
      
      <router-link 
        to="/teacher/lectures" 
        class="block p-6 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
      >
        <div class="text-green-800 text-2xl mb-3">📝</div>
        <h3 class="font-semibold text-green-800 mb-2">Управление лекциями</h3>
        <p class="text-green-600 text-sm">Добавляйте лекции и учебные материалы</p>
      </router-link>
      
      <router-link 
        to="/teacher/tests" 
        class="block p-6 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors"
      >
        <div class="text-purple-800 text-2xl mb-3">🎯</div>
        <h3 class="font-semibold text-purple-800 mb-2">Управление тестами</h3>
        <p class="text-purple-600 text-sm">Создавайте тесты для проверки знаний</p>
      </router-link>
    </div>

    <!-- Последние действия -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-bold mb-4">Последние действия</h2>
      
      <div v-if="recentActivities.length > 0" class="space-y-4">
        <div 
          v-for="activity in recentActivities" 
          :key="activity.id"
          class="flex items-center justify-between p-3 border rounded-lg"
        >
          <div class="flex items-center">
            <div class="text-2xl mr-3">{{ getActivityIcon(activity.type) }}</div>
            <div>
              <div class="font-medium">{{ activity.title }}</div>
              <div class="text-sm text-gray-500">{{ activity.description }}</div>
            </div>
          </div>
          <div class="text-sm text-gray-500">
            {{ formatDate(activity.created_at) }}
          </div>
        </div>
      </div>
      
      <div v-else class="text-gray-500 text-center py-8">
        Нет недавних действий
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
  switch (type) {
    case 'course':
      return '📚'
    case 'lecture':
      return '📝'
    case 'test':
      return '🎯'
    default:
      return '📄'
  }
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
    students: 42 // Заглушка, нужно будет получить из API
  }
})
</script>
