<template>
  <div class="animate-fade-in">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
      <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
        Управление <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-500 to-cyan-500">курсами</span>
      </h1>
      <button 
        @click="showCreateModal = true"
        class="btn btn-primary"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Создать курс
      </button>
    </div>

    <!-- Модальное окно создания курса -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="card max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-scale-in">
        <div class="p-6 border-b border-slate-100">
          <h3 class="text-xl font-bold text-slate-800">Создание нового курса</h3>
        </div>
        
        <form @submit.prevent="createCourse" class="p-6 space-y-5">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Название курса</label>
            <input
              v-model="courseForm.title"
              type="text"
              class="input-field"
              placeholder="Введите название курса"
              required
            />
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Описание</label>
            <textarea
              v-model="courseForm.description"
              class="input-field min-h-[120px]"
              placeholder="Опишите курс"
              required
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Статус</label>
            <select v-model="courseForm.status" class="input-field">
              <option value="draft">Черновик</option>
              <option value="published">Опубликован</option>
            </select>
          </div>
          
          <div class="flex justify-end gap-3 pt-4">
            <button
              type="button"
              @click="showCreateModal = false"
              class="btn btn-secondary"
            >
              Отмена
            </button>
            <button
              type="submit"
              :disabled="creating"
              class="btn btn-primary"
            >
              {{ creating ? 'Создание...' : 'Создать курс' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Список курсов -->
    <div v-if="loading" class="text-center py-16">
      <div class="inline-flex items-center gap-3 text-slate-500">
        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Загрузка курсов...
      </div>
    </div>

    <div v-else-if="courses.length === 0" class="text-center py-16">
      <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
      </div>
      <p class="text-slate-500 text-lg mb-4">У вас пока нет курсов</p>
      <button @click="showCreateModal = true" class="btn btn-primary">
        Создать первый курс
      </button>
    </div>

    <div v-else class="space-y-5">
      <div 
        v-for="(course, index) in courses" 
        :key="course.id"
        class="card p-6 animate-slide-up"
        :style="{ animationDelay: `${index * 0.05}s` }"
      >
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-2">
              <h3 class="text-xl font-bold text-slate-800">{{ course.title }}</h3>
              <span :class="getStatusClass(course.status)" class="badge">
                {{ getStatusText(course.status) }}
              </span>
            </div>
            <p class="text-slate-500 mb-4 line-clamp-2">{{ course.description }}</p>
            
            <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                {{ course.lectures_count || 0 }} лекций
              </span>
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ course.tests_count || 0 }} тестов
              </span>
            </div>
          </div>
          
          <div class="flex flex-wrap gap-2 md:flex-col md:gap-2">
            <button 
              @click="editCourse(course)"
              class="btn btn-secondary text-sm py-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Редактировать
            </button>
            <button 
              @click="deleteCourse(course.id)"
              class="btn bg-red-50 text-red-600 border-red-200 hover:bg-red-100 hover:border-red-300 text-sm py-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Удалить
            </button>
          </div>
        </div>
        
        <!-- Дополнительные действия -->
        <div class="flex flex-wrap items-center gap-4 pt-4 mt-4 border-t border-slate-100">
          <router-link 
            :to="`/courses/${course.id}`"
            class="text-sm font-medium text-sky-600 hover:text-sky-700 flex items-center gap-1"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Просмотр курса
          </router-link>
          <span class="text-slate-300">|</span>
          <button 
            @click="manageLectures(course)"
            class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            Управление лекциями
          </button>
          <span class="text-slate-300">|</span>
          <button 
            @click="manageTests(course)"
            class="text-sm font-medium text-violet-600 hover:text-violet-700 flex items-center gap-1"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
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
    case 'published': return 'badge-success'
    case 'draft': return 'badge-warning'
    default: return 'bg-gray-100 text-gray-800'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'published': return 'Опубликован'
    case 'draft': return 'Черновик'
    case 'archived': return 'В архиве'
    default: return status
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
  console.log('Manage lectures for course:', course)
}

const manageTests = (course) => {
  console.log('Manage tests for course:', course)
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
