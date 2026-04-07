<template>
  <div class="animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
      <div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
          Каталог <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-500 to-cyan-500">курсов</span>
        </h1>
        <p class="text-slate-500 mt-1">Выберите курс для начала обучения</p>
      </div>
      
      <!-- View Toggle & Filter -->
      <div class="flex items-center gap-2 flex-wrap">
        <div class="view-toggle">
          <button 
            class="view-btn" 
            :class="{ 'view-btn--active': viewMode === 'grid' }"
            @click="viewMode = 'grid'"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
          </button>
          <button 
            class="view-btn"
            :class="{ 'view-btn--active': viewMode === 'list' }"
            @click="viewMode = 'list'"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
        
        <select v-model="statusFilter" class="input-field max-w-[140px] sm:max-w-[180px] text-sm">
          <option value="">Все</option>
          <option value="published">Опубликов.</option>
          <option value="draft">Черновики</option>
        </select>
        
        <select v-model="sortBy" class="input-field max-w-[130px] sm:max-w-[180px] text-sm">
          <option value="newest">Новые</option>
          <option value="oldest">Старые</option>
          <option value="title">По названию</option>
        </select>
      </div>
    </div>

    <!-- Stats Bar -->
    <div class="card p-3 sm:p-4 mb-6 flex flex-col sm:flex-row flex-wrap items-start sm:items-center justify-between gap-3 sm:gap-4 animate-slide-up">
      <!-- Stats - скрываем на маленьких экранах -->
      <div class="hidden sm:flex items-center gap-4 lg:gap-6 text-sm">
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-sky-500"></span>
          <span class="text-slate-600">Всего: <strong class="text-slate-800">{{ filteredCourses.length }}</strong></span>
        </div>
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span class="text-slate-600">Опубликовано: <strong class="text-slate-800">{{ publishedCount }}</strong></span>
        </div>
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-amber-500"></span>
          <span class="text-slate-600">Черновики: <strong class="text-slate-800">{{ draftCount }}</strong></span>
        </div>
      </div>
      
      <!-- Mobile Stats Counter -->
      <div class="sm:hidden flex items-center gap-3 text-sm">
        <span class="text-slate-600">Найдено: <strong class="text-slate-800">{{ filteredCourses.length }}</strong></span>
      </div>
      
      <div class="search-box w-full sm:w-auto">
        <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Поиск..."
          class="search-input min-w-0 flex-1 sm:w-32 md:w-40"
        />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-16">
      <div class="inline-flex items-center gap-3 text-slate-500">
        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Загрузка курсов...
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredCourses.length === 0" class="text-center py-16">
      <div class="w-24 h-24 mx-auto mb-6 rounded-3xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
      </div>
      <h3 class="text-xl font-bold text-slate-800 mb-2">Курсы не найдены</h3>
      <p class="text-slate-500 mb-6">Попробуйте изменить параметры поиска или фильтра</p>
      <button @click="resetFilters" class="btn btn-primary">
        Сбросить фильтры
      </button>
    </div>

    <!-- Grid View -->
    <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="(course, index) in filteredCourses" 
        :key="course.id"
        class="course-card card card-elevated overflow-hidden group animate-slide-up"
        :style="{ animationDelay: `${index * 0.05}s` }"
      >
        <!-- Card Header with Gradient -->
        <div class="course-card-header" :style="{ background: getCourseGradient(course.id) }">
          <span :class="getStatusClass(course.status)" class="badge">
            {{ getStatusText(course.status) }}
          </span>
          <button class="favorite-btn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </button>
        </div>
        
        <div class="p-5">
          <h3 class="text-lg font-bold text-slate-800 mb-2 line-clamp-2 group-hover:text-sky-600 transition-colors">
            {{ course.title }}
          </h3>
          
          <p class="text-slate-500 text-sm mb-4 line-clamp-2">{{ course.description }}</p>
          
          <div class="flex items-center gap-4 text-sm text-slate-500 mb-4">
            <span class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              {{ course.lectures_count || 0 }} лекций
            </span>
            <span class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ course.tests_count || 0 }} тестов
            </span>
          </div>
          
          <!-- Author -->
          <div class="flex items-center gap-2 mb-4 pb-4 border-b border-slate-100">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-400 to-cyan-500 flex items-center justify-center text-white text-sm font-bold">
              {{ course.author?.name?.charAt(0).toUpperCase() || 'A' }}
            </div>
            <span class="text-sm text-slate-600">{{ course.author?.name || 'Автор' }}</span>
          </div>
          
          <!-- Progress & Action -->
          <div class="flex items-center justify-between">
            <router-link 
              :to="`/courses/${course.id}`"
              class="btn btn-primary text-sm py-2"
            >
              Начать обучение
            </router-link>
            
            <div v-if="course.progress !== undefined" class="flex items-center gap-2">
              <div class="w-16 h-2 bg-slate-200 rounded-full overflow-hidden">
                <div 
                  class="h-full bg-gradient-to-r from-sky-500 to-emerald-500 rounded-full"
                  :style="{ width: `${course.progress}%` }"
                ></div>
              </div>
              <span class="text-xs font-medium text-slate-500">{{ Math.round(course.progress) }}%</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- List View -->
    <div v-else class="space-y-4">
      <div 
        v-for="(course, index) in filteredCourses" 
        :key="course.id"
        class="card p-5 flex flex-col md:flex-row md:items-center gap-4 animate-slide-up"
        :style="{ animationDelay: `${index * 0.03}s` }"
      >
        <div class="w-full md:w-24 h-24 rounded-xl flex items-center justify-center text-3xl" :style="{ background: getCourseGradient(course.id) }">
          📚
        </div>
        
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1">
            <span :class="getStatusClass(course.status)" class="badge text-xs">
              {{ getStatusText(course.status) }}
            </span>
          </div>
          <h3 class="text-lg font-bold text-slate-800 mb-1">{{ course.title }}</h3>
          <p class="text-slate-500 text-sm line-clamp-1">{{ course.description }}</p>
          
          <div class="flex items-center gap-4 mt-2 text-sm text-slate-500">
            <span>{{ course.lectures_count || 0 }} лекций</span>
            <span>{{ course.tests_count || 0 }} тестов</span>
            <span>Автор: {{ course.author?.name }}</span>
          </div>
        </div>
        
        <router-link 
          :to="`/courses/${course.id}`"
          class="btn btn-primary shrink-0"
        >
          Открыть
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCourseStore } from '../../stores/courses'

const courseStore = useCourseStore()

const loading = ref(true)
const viewMode = ref('grid')
const statusFilter = ref('')
const sortBy = ref('newest')
const searchQuery = ref('')

const gradients = [
  'linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%)',
  'linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%)',
  'linear-gradient(135deg, #10b981 0%, #34d399 100%)',
  'linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%)',
  'linear-gradient(135deg, #ef4444 0%, #f87171 100%)',
  'linear-gradient(135deg, #ec4899 0%, #f472b6 100%)',
]

const getCourseGradient = (id) => {
  return gradients[id % gradients.length]
}

const filteredCourses = computed(() => {
  let courses = [...courseStore.courses]
  
  // Filter by status
  if (statusFilter.value) {
    courses = courses.filter(course => course.status === statusFilter.value)
  }
  
  // Filter by search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    courses = courses.filter(course => 
      course.title.toLowerCase().includes(query) || 
      course.description?.toLowerCase().includes(query)
    )
  }
  
  // Sort
  courses.sort((a, b) => {
    switch (sortBy.value) {
      case 'newest':
        return new Date(b.created_at) - new Date(a.created_at)
      case 'oldest':
        return new Date(a.created_at) - new Date(b.created_at)
      case 'title':
        return a.title.localeCompare(b.title)
      default:
        return 0
    }
  })
  
  return courses
})

const publishedCount = computed(() => 
  courseStore.courses.filter(c => c.status === 'published').length
)

const draftCount = computed(() => 
  courseStore.courses.filter(c => c.status === 'draft').length
)

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

const resetFilters = () => {
  statusFilter.value = ''
  searchQuery.value = ''
  sortBy.value = 'newest'
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

.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* View Toggle */
.view-toggle {
  display: flex;
  background: #f1f5f9;
  border-radius: 10px;
  padding: 4px;
}

.view-btn {
  padding: 8px;
  border-radius: 8px;
  color: #64748b;
  transition: all 0.15s ease;
}

.view-btn:hover {
  color: #0f172a;
}

.view-btn--active {
  background: white;
  color: #0f172a;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Search Box */
.search-box {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  transition: all 0.2s ease;
}

.search-box:focus-within {
  border-color: #38bdf8;
  box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
}

.search-input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 0.9rem;
  color: #0f172a;
  width: 150px;
}

.search-input::placeholder {
  color: #94a3b8;
}

/* Course Card */
.course-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.course-card:hover {
  transform: translateY(-4px);
}

.course-card-header {
  height: 80px;
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1rem;
}

.favorite-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(4px);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.favorite-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.05);
}
</style>
