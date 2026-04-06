<template>
  <div>
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Добро пожаловать, {{ user?.name }}!</h1>
      <p class="text-gray-600 mt-2">
        <span v-if="authStore.isTeacher">Панель преподавателя</span>
        <span v-else>Панель студента</span>
      </p>
    </div>

    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-2xl font-bold text-blue-600">{{ stats.courses }}</div>
        <div class="text-gray-600">Курсы</div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-2xl font-bold text-green-600">{{ stats.lectures }}</div>
        <div class="text-gray-600">Лекции</div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-2xl font-bold text-cyan-700">{{ stats.tests }}</div>
        <div class="text-gray-600">Тесты</div>
      </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-bold mb-4">Быстрые действия</h2>

      <div v-if="authStore.isTeacher" class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <router-link
          to="/teacher/courses"
          class="block p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
        >
          <h3 class="font-semibold text-blue-800">Управление курсами</h3>
          <p class="text-blue-600 text-sm">Создавайте и редактируйте учебные программы</p>
        </router-link>

        <router-link
          to="/teacher/lectures"
          class="block p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
        >
          <h3 class="font-semibold text-green-800">Управление лекциями</h3>
          <p class="text-green-600 text-sm">Добавляйте лекции и учебные материалы</p>
        </router-link>

        <router-link
          to="/teacher/tests"
          class="block p-4 bg-cyan-50 rounded-lg hover:bg-cyan-100 transition-colors"
        >
          <h3 class="font-semibold text-cyan-800">Управление тестами</h3>
          <p class="text-cyan-700 text-sm">Создавайте тесты и вопросы для проверки знаний</p>
        </router-link>

        <router-link
          to="/admin"
          class="block p-4 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors"
        >
          <h3 class="font-semibold text-slate-800">Админ-панель</h3>
          <p class="text-slate-600 text-sm">Управление пользователями, ролями и доступом</p>
        </router-link>
      </div>

      <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <router-link
          to="/courses"
          class="block p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
        >
          <h3 class="font-semibold text-blue-800">Мои курсы</h3>
          <p class="text-blue-600 text-sm">Просмотр доступных материалов и занятий</p>
        </router-link>

        <router-link
          to="/courses"
          class="block p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
        >
          <h3 class="font-semibold text-green-800">Прогресс обучения</h3>
          <p class="text-green-600 text-sm">Следите за лекциями, тестами и успехами</p>
        </router-link>
      </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mt-6">
      <h2 class="text-xl font-bold mb-4">Последние курсы</h2>

      <div v-if="recentCourses.length > 0" class="space-y-3">
        <div
          v-for="course in recentCourses"
          :key="course.id"
          class="flex flex-col gap-3 rounded-lg border p-4 md:flex-row md:items-center md:justify-between hover:bg-gray-50"
        >
          <div>
            <h3 class="font-semibold">{{ course.title }}</h3>
            <p class="text-sm text-gray-600">{{ course.description }}</p>
          </div>

          <router-link
            :to="`/courses/${course.id}`"
            class="inline-flex items-center justify-center rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
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
import { computed, onMounted, ref } from 'vue'
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
