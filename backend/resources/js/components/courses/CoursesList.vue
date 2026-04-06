<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Курсы</h1>
      <div class="flex space-x-2">
        <select v-model="statusFilter" class="px-3 py-2 border rounded-md">
          <option value="">Все курсы</option>
          <option value="published">Опубликованные</option>
          <option value="draft">Черновики</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="text-center py-8">
      <div class="text-gray-500">Загрузка...</div>
    </div>

    <div v-else-if="filteredCourses.length === 0" class="text-center py-8">
      <div class="text-gray-500">Курсы не найдены</div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="course in filteredCourses" 
        :key="course.id"
        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
      >
        <div class="p-6">
          <div class="flex justify-between items-start mb-2">
            <h3 class="text-xl font-semibold text-gray-800">{{ course.title }}</h3>
            <span 
              :class="getStatusClass(course.status)"
              class="px-2 py-1 text-xs rounded-full"
            >
              {{ getStatusText(course.status) }}
            </span>
          </div>
          
          <p class="text-gray-600 mb-4 line-clamp-2">{{ course.description }}</p>
          
          <div class="flex items-center text-sm text-gray-500 mb-4">
            <span class="mr-4">📚 {{ course.lectures_count || 0 }} лекций</span>
            <span>📝 {{ course.tests_count || 0 }} тестов</span>
          </div>
          
          <div v-if="course.author" class="text-sm text-gray-500 mb-4">
            Автор: {{ course.author.name }}
          </div>
          
          <div class="flex justify-between items-center">
            <router-link 
              :to="`/courses/${course.id}`"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
            >
              Открыть курс
            </router-link>
            
            <div v-if="course.progress !== undefined" class="text-sm text-gray-600">
              Прогресс: {{ Math.round(course.progress) }}%
            </div>
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
const statusFilter = ref('')

const filteredCourses = computed(() => {
  let courses = courseStore.courses
  
  if (statusFilter.value) {
    courses = courses.filter(course => course.status === statusFilter.value)
  }
  
  return courses
})

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
