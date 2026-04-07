<template>
  <div class="animate-fade-in">
    <!-- Состояния загрузки -->
    <div v-if="loading" class="text-center py-16">
      <div class="inline-flex items-center gap-3 text-slate-500">
        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Загрузка курса...
      </div>
    </div>

    <div v-else-if="!course" class="text-center py-16">
      <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <p class="text-slate-500 text-lg">Курс не найден</p>
    </div>

    <div v-else>
      <!-- Заголовок курса -->
      <div class="card card-elevated p-6 md:p-8 mb-6 animate-slide-up">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-4">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-3">
              <span :class="getStatusClass(course.status)" class="badge">
                {{ getStatusText(course.status) }}
              </span>
              <span class="text-sm text-slate-400">ID: {{ course.id }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 mb-3">{{ course.title }}</h1>
            <p class="text-slate-500 text-lg leading-relaxed">{{ course.description }}</p>
          </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-6 pt-4 border-t border-slate-100 text-sm text-slate-500">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-400 to-cyan-500 flex items-center justify-center text-white text-sm font-bold">
              {{ course.author?.name?.charAt(0).toUpperCase() || 'A' }}
            </div>
            <span>{{ course.author?.name || 'Автор' }}</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Создан: {{ formatDate(course.created_at) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span>{{ lectures.length }} лекций</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ tests.length }} тестов</span>
          </div>
        </div>
      </div>

      <!-- Mobile Toggle Button -->
      <button 
        class="lg:hidden w-full mb-4 flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 shadow-sm"
        @click="showSidebar = !showSidebar"
      >
        <span class="font-semibold text-slate-800 flex items-center gap-2">
          <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
          </svg>
          Содержание курса
        </span>
        <svg 
          class="w-5 h-5 text-slate-400 transition-transform" 
          :class="{ 'rotate-180': showSidebar }"
          fill="none" stroke="currentColor" viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <!-- Навигация по курсу -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Содержание курса -->
        <div class="lg:col-span-1" :class="{ 'hidden lg:block': !showSidebar }">
          <div class="card p-4 md:p-6 lg:sticky lg:top-24">
            <h2 class="text-lg font-bold mb-5 flex items-center gap-2">
              <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
              Содержание курса
            </h2>
            
            <div v-if="lectures.length > 0" class="mb-6">
              <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-xs">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </span>
                Лекции
              </h3>
              <div class="space-y-2">
                <button 
                  v-for="lecture in lectures" 
                  :key="lecture.id"
                  class="w-full text-left p-3 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-all duration-200"
                  :class="{ 'border-sky-400 bg-sky-50': selectedLecture?.id === lecture.id }"
                  @click="openLecture(lecture)"
                >
                  <div class="font-semibold text-slate-800 text-sm">{{ lecture.title }}</div>
                  <div class="text-xs text-slate-500 mt-1 line-clamp-1">{{ lecture.description }}</div>
                </button>
              </div>
            </div>
            
            <div v-if="tests.length > 0">
              <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center text-xs">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </span>
                Тесты
              </h3>
              <div class="space-y-2">
                <button 
                  v-for="test in tests" 
                  :key="test.id"
                  class="w-full text-left p-3 rounded-xl border border-slate-200 hover:border-violet-300 hover:bg-violet-50/50 transition-all duration-200"
                  :class="{ 'border-violet-400 bg-violet-50': selectedTest?.id === test.id }"
                  @click="openTest(test)"
                >
                  <div class="font-semibold text-slate-800 text-sm">{{ test.title }}</div>
                  <div class="text-xs text-slate-500 mt-1">{{ test.questions_count }} вопросов</div>
                </button>
              </div>
            </div>
            
            <div v-if="lectures.length === 0 && tests.length === 0" class="text-slate-400 text-sm text-center py-4">
              Нет доступных материалов
            </div>
          </div>
        </div>

        <!-- Основной контент -->
        <div class="lg:col-span-2">
          <div v-if="selectedLecture" class="card p-6 md:p-8 animate-slide-up">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-4 pb-4 border-b border-slate-100">
              <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              Лекция
            </div>
            <h2 class="text-2xl font-bold text-slate-800 mb-6">{{ selectedLecture.title }}</h2>
            <div class="prose max-w-none text-slate-600" v-html="selectedLecture.content"></div>
            
            <div v-if="lectureMaterials.length > 0" class="mt-8 pt-6 border-t border-slate-100">
              <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
                Материалы лекции
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <a 
                  v-for="material in lectureMaterials" 
                  :key="material.id"
                  :href="material.file_url"
                  target="_blank"
                  class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/50 transition-all duration-200 group"
                >
                  <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="font-semibold text-slate-800 text-sm truncate">{{ material.title }}</div>
                    <div class="text-xs text-slate-500">{{ material.file_size }}</div>
                  </div>
                  <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                </a>
              </div>
            </div>
          </div>
          
          <div v-else-if="selectedTest" class="card p-6 md:p-8 animate-slide-up">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-4 pb-4 border-b border-slate-100">
              <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Тест
            </div>
            <h2 class="text-2xl font-bold text-slate-800 mb-2">{{ selectedTest.title }}</h2>
            <p class="text-slate-500 mb-6">{{ selectedTest.description }}</p>
            
            <div v-if="!testStarted" class="text-center py-8">
              <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-violet-100 to-purple-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <p class="text-slate-500 mb-6">Готовы проверить свои знания?</p>
              <button @click="startTest" class="btn btn-primary">
                Начать тест
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                </svg>
              </button>
            </div>
            
            <div v-else>
              <TestComponent :test="selectedTest" @submit="handleTestSubmit" />
            </div>
          </div>
          
          <div v-else class="card p-6 md:p-8 text-center py-16">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
              <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
              </svg>
            </div>
            <p class="text-slate-500 text-lg">Выберите лекцию или тест для просмотра</p>
            <p class="text-slate-400 text-sm mt-1">Нажмите на элемент из списка слева</p>
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
const showSidebar = ref(false)

const course = computed(() => courseStore.currentCourse)
const lectures = computed(() => courseStore.lectures)
const tests = computed(() => courseStore.tests)
const lectureMaterials = computed(() => courseStore.materials)

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

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' })
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

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.prose { line-height: 1.8; }
.prose :deep(h1), .prose :deep(h2), .prose :deep(h3) {
  color: #0f172a;
  font-weight: 700;
  margin-top: 1.5em;
  margin-bottom: 0.5em;
}
.prose :deep(p) { margin-bottom: 1em; }
.prose :deep(ul), .prose :deep(ol) { padding-left: 1.5em; margin-bottom: 1em; }
.prose :deep(li) { margin-bottom: 0.5em; }
.prose :deep(code) { background: #f1f5f9; padding: 0.2em 0.4em; border-radius: 4px; font-size: 0.9em; }
.prose :deep(pre) { background: #1e293b; color: #e2e8f0; padding: 1em; border-radius: 8px; overflow-x: auto; margin: 1em 0; }
</style>
