<template>
  <div class="animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
          Мои <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-500 to-cyan-500">курсы</span>
        </h1>
        <p class="text-slate-500 mt-1">Твой прогресс обучения</p>
      </div>
      <router-link to="/courses" class="btn btn-primary self-start gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Найти курсы
      </router-link>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
      <div class="card p-4 text-center">
        <div class="text-2xl font-bold text-sky-600">{{ studentStore.enrollments.length }}</div>
        <div class="text-xs text-slate-500 mt-1">Всего</div>
      </div>
      <div class="card p-4 text-center">
        <div class="text-2xl font-bold text-amber-600">{{ studentStore.inProgressCourses.length }}</div>
        <div class="text-xs text-slate-500 mt-1">В процессе</div>
      </div>
      <div class="card p-4 text-center">
        <div class="text-2xl font-bold text-emerald-600">{{ studentStore.completedCourses.length }}</div>
        <div class="text-xs text-slate-500 mt-1">Завершено</div>
      </div>
    </div>

    <!-- Filter Tabs + Search -->
    <div class="card p-4 mb-6 flex flex-col sm:flex-row sm:items-center gap-3">
      <div class="filter-tabs">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          class="filter-tab"
          :class="{ 'filter-tab--active': activeTab === tab.value }"
          @click="activeTab = tab.value"
        >
          {{ tab.label }}
          <span class="filter-tab-count">{{ tabCount(tab.value) }}</span>
        </button>
      </div>
      <div class="search-box sm:ml-auto">
        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        <input v-model="search" type="text" placeholder="Поиск по курсам..." class="search-input w-48" />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div v-for="i in 4" :key="i" class="card overflow-hidden animate-pulse">
        <div class="h-2 bg-slate-100"></div>
        <div class="p-5 space-y-3">
          <div class="h-5 bg-slate-100 rounded w-3/4"></div>
          <div class="h-4 bg-slate-100 rounded w-full"></div>
          <div class="h-4 bg-slate-100 rounded w-1/2"></div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="filtered.length === 0 && studentStore.enrollments.length === 0" class="empty-state">
      <div class="empty-state-icon">📚</div>
      <h3 class="empty-state-title">Вы ещё не записаны на курсы</h3>
      <p class="empty-state-text">Перейдите в каталог и найдите интересные программы</p>
      <router-link to="/courses" class="btn btn-primary mt-4">Открыть каталог</router-link>
    </div>
    <div v-else-if="filtered.length === 0" class="empty-state">
      <div class="empty-state-icon">🔍</div>
      <h3 class="empty-state-title">Ничего не найдено</h3>
      <p class="empty-state-text">Попробуйте изменить фильтр или запрос</p>
    </div>

    <!-- Course Cards -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div
        v-for="(course, i) in filtered"
        :key="course.id"
        class="my-course-card animate-slide-up"
        :style="{ animationDelay: `${i * 0.05}s` }"
      >
        <!-- Progress bar at top -->
        <div class="my-course-card-bar">
          <div class="my-course-card-bar-fill"
            :style="{ width: course.progress + '%' }"
            :class="course.completed_at ? 'bg-emerald-500' : 'bg-gradient-to-r from-sky-500 to-cyan-400'"
          ></div>
        </div>

        <div class="p-5">
          <!-- Status + Progress % -->
          <div class="flex items-center justify-between mb-3">
            <span class="badge" :class="statusBadgeClass(course)">{{ statusLabel(course) }}</span>
            <span class="text-sm font-bold"
              :class="course.completed_at ? 'text-emerald-600' : course.progress > 0 ? 'text-sky-600' : 'text-slate-400'"
            >{{ course.progress }}%</span>
          </div>

          <!-- Title -->
          <h3 class="text-lg font-bold text-slate-800 mb-1 line-clamp-2 group-hover:text-sky-600 transition-colors">
            {{ course.title }}
          </h3>
          <p class="text-slate-500 text-sm mb-4 line-clamp-2">{{ course.description }}</p>

          <!-- Lecture progress -->
          <div class="flex items-center gap-4 text-sm text-slate-500 mb-4">
            <span class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
              <strong class="text-slate-700">{{ course.viewed_lectures }}</strong> / {{ course.lectures_count }} лекций
            </span>
            <span class="flex items-center gap-1.5">
              <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              {{ course.tests_count }} тестов
            </span>
          </div>

          <!-- Dates -->
          <div class="flex items-center gap-3 text-xs text-slate-400 mb-4">
            <span>Записан: {{ formatDate(course.enrolled_at) }}</span>
            <span v-if="course.last_accessed_at">· Был {{ formatDate(course.last_accessed_at) }}</span>
            <span v-if="course.completed_at" class="text-emerald-600 font-medium">· Завершён {{ formatDate(course.completed_at) }}</span>
          </div>

          <!-- Actions -->
          <div class="flex gap-2">
            <router-link :to="`/courses/${course.id}`" class="btn btn-primary flex-1 text-sm justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              {{ course.progress > 0 ? 'Продолжить' : 'Начать' }}
            </router-link>
            <button
              class="btn btn-secondary text-sm"
              @click="confirmUnenroll(course)"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Unenroll Confirmation Modal -->
    <Teleport to="body">
      <div v-if="unenrollTarget" class="modal-backdrop" @click.self="unenrollTarget = null">
        <div class="modal-box">
          <div class="modal-header">
            <h3 class="modal-title">Отписаться от курса</h3>
            <button class="modal-close" @click="unenrollTarget = null">&times;</button>
          </div>
          <div class="modal-body">
            <p class="text-slate-600">Вы уверены, что хотите отписаться от курса <strong>«{{ unenrollTarget.title }}»</strong>?</p>
            <p class="text-sm text-amber-600 mt-2 flex items-start gap-2">
              <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
              Ваш прогресс будет сохранён, но потребуется повторная запись для доступа к материалам.
            </p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="unenrollTarget = null">Отмена</button>
            <button class="btn btn-danger" :disabled="unenrolling" @click="doUnenroll">
              <svg v-if="unenrolling" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              Отписаться
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStudentStore } from '../../stores/studentStore'

const studentStore = useStudentStore()

const loading       = ref(false)
const activeTab     = ref('all')
const search        = ref('')
const unenrollTarget = ref(null)
const unenrolling   = ref(false)

const tabs = [
  { label: 'Все',         value: 'all' },
  { label: 'В процессе',  value: 'in_progress' },
  { label: 'Завершено',   value: 'completed' },
]

const tabCount = (tab) => {
  if (tab === 'all')         return studentStore.enrollments.length
  if (tab === 'in_progress') return studentStore.inProgressCourses.length
  if (tab === 'completed')   return studentStore.completedCourses.length
  return 0
}

const filtered = computed(() => {
  let list = studentStore.enrollments

  if (activeTab.value === 'in_progress') list = list.filter(c => !c.completed_at)
  if (activeTab.value === 'completed')   list = list.filter(c => !!c.completed_at)

  if (search.value) {
    const q = search.value.toLowerCase()
    list = list.filter(c => c.title.toLowerCase().includes(q) || c.description?.toLowerCase().includes(q))
  }

  return list
})

const statusLabel = (course) => {
  if (course.completed_at) return 'Завершён'
  if (course.progress > 0)  return 'В процессе'
  return 'Не начат'
}

const statusBadgeClass = (course) => {
  if (course.completed_at) return 'badge-success'
  if (course.progress > 0)  return 'badge-warning'
  return 'bg-slate-100 text-slate-600'
}

const formatDate = (iso) => {
  if (!iso) return ''
  return new Date(iso).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short', year: 'numeric' })
}

const confirmUnenroll = (course) => { unenrollTarget.value = course }

const doUnenroll = async () => {
  if (!unenrollTarget.value) return
  unenrolling.value = true
  try {
    await studentStore.unenroll(unenrollTarget.value.id)
    unenrollTarget.value = null
  } finally {
    unenrolling.value = false
  }
}

onMounted(async () => {
  if (studentStore.enrollments.length === 0) {
    loading.value = true
    try { await studentStore.fetchEnrollments() } finally { loading.value = false }
  }
})
</script>

<style scoped>
/* Filter Tabs */
.filter-tabs { display: flex; gap: 4px; background: #f1f5f9; border-radius: 12px; padding: 4px; }
.filter-tab {
  display: flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: 9px;
  font-size: 0.875rem; font-weight: 500; color: #64748b;
  transition: all 0.15s ease; cursor: pointer;
}
.filter-tab:hover { color: #0f172a; }
.filter-tab--active { background: white; color: #0f172a; box-shadow: 0 2px 4px rgba(0,0,0,0.06); }
.filter-tab-count { font-size: 0.75rem; background: #e2e8f0; border-radius: 999px; padding: 1px 7px; font-weight: 600; }
.filter-tab--active .filter-tab-count { background: #e0f2fe; color: #0284c7; }

/* Search */
.search-box { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s; }
.search-box:focus-within { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56,189,248,0.1); }
.search-input { border: none; background: transparent; outline: none; font-size: 0.875rem; color: #0f172a; }
.search-input::placeholder { color: #94a3b8; }

/* Empty State */
.empty-state { text-align: center; padding: 4rem 2rem; }
.empty-state-icon { font-size: 3rem; margin-bottom: 1rem; }
.empty-state-title { font-size: 1.25rem; font-weight: 700; color: #0f172a; }
.empty-state-text { color: #64748b; margin-top: 0.5rem; }

/* Course Card */
.my-course-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; transition: all 0.25s ease; }
.my-course-card:hover { border-color: #38bdf8; box-shadow: 0 8px 30px rgba(56,189,248,0.12); }
.my-course-card-bar { height: 5px; background: #f1f5f9; }
.my-course-card-bar-fill { height: 100%; border-radius: 0 3px 3px 0; transition: width 0.6s ease; }

/* Modal */
.modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); backdrop-filter: blur(4px); z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.modal-box { background: white; border-radius: 20px; width: 100%; max-width: 460px; box-shadow: 0 25px 60px rgba(0,0,0,0.15); overflow: hidden; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
.modal-title { font-size: 1.125rem; font-weight: 700; color: #0f172a; }
.modal-close { width: 32px; height: 32px; border-radius: 8px; font-size: 1.25rem; color: #94a3b8; display: flex; align-items: center; justify-content: center; transition: all 0.15s; cursor: pointer; }
.modal-close:hover { background: #f1f5f9; color: #475569; }
.modal-body { padding: 1.5rem; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9; }
.btn-danger { background: #ef4444; color: white; padding: 0.5rem 1.25rem; border-radius: 10px; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: all 0.15s; }
.btn-danger:hover:not(:disabled) { background: #dc2626; }
.btn-danger:disabled { opacity: 0.7; cursor: not-allowed; }

.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
