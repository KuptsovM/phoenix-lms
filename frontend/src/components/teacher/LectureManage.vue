<template>
  <div class="animate-fade-in">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
      <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
        Управление <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-green-500">лекциями</span>
      </h1>
      <button @click="openCreateModal" class="btn btn-primary" :disabled="courses.length === 0" :title="courses.length === 0 ? 'Сначала создайте курс' : ''">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Создать лекцию
      </button>
    </div>

    <!-- Фильтры -->
    <div class="card p-4 mb-6 animate-slide-up">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Курс</label>
          <select v-model="filters.course" class="input-field">
            <option value="">Все курсы</option>
            <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Поиск</label>
          <input v-model="filters.search" type="text" placeholder="Поиск лекций..." class="input-field" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Статус</label>
          <select v-model="filters.status" class="input-field">
            <option value="">Все статусы</option>
            <option value="published">Опубликованные</option>
            <option value="draft">Черновики</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Список -->
    <div v-if="loading" class="text-center py-16">
      <div class="inline-flex items-center gap-3 text-slate-500">
        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Загрузка...
      </div>
    </div>

    <div v-else-if="errorMessage" class="card p-6 text-center text-red-600">{{ errorMessage }}</div>

    <div v-else-if="filteredLectures.length === 0" class="text-center py-16">
      <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
      </div>
      <p class="text-slate-500 text-lg">Нет лекций для выбранного курса</p>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="(lecture, index) in filteredLectures"
        :key="lecture.id"
        class="card p-5 animate-slide-up"
        :style="{ animationDelay: `${index * 0.05}s` }"
      >
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-2">
              <h3 class="text-lg font-bold text-slate-800">{{ lecture.title }}</h3>
              <span :class="getStatusClass(lecture.status)" class="badge">{{ getStatusText(lecture.status) }}</span>
            </div>
            <p class="text-slate-500 text-sm mb-3 line-clamp-2">{{ lecture.description }}</p>
            <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
                {{ lecture.materials_count || 0 }} материалов
              </span>
              <span v-if="lecture.content" class="inline-flex items-center gap-1.5 text-emerald-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Контент добавлен
              </span>
            </div>
          </div>
          <div class="flex gap-2 flex-shrink-0">
            <button @click="openEditModal(lecture)" class="btn btn-secondary text-sm py-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Редактировать
            </button>
            <button @click="openDeleteModal(lecture)" class="btn bg-red-50 text-red-600 border-red-200 hover:bg-red-100 hover:border-red-300 text-sm py-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Удалить
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== МОДАЛКА СОЗДАНИЯ / РЕДАКТИРОВАНИЯ ===== -->
    <Teleport to="body">
      <div v-if="showFormModal" class="modal-overlay" @click.self="closeFormModal">
        <div class="modal-box modal-wide">
          <!-- Шапка -->
          <div class="modal-header">
            <div class="flex items-center gap-4">
              <h2 class="text-xl font-bold text-slate-800">
                {{ editingLecture ? 'Редактировать лекцию' : 'Создать лекцию' }}
              </h2>
              <!-- Вкладки -->
              <div class="flex gap-1 bg-slate-100 rounded-lg p-1">
                <button
                  v-for="tab in tabs"
                  :key="tab.id"
                  @click="activeTab = tab.id"
                  class="px-3 py-1.5 rounded-md text-sm font-medium transition-all"
                  :class="activeTab === tab.id ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                >
                  {{ tab.label }}
                </button>
              </div>
            </div>
            <button @click="closeFormModal" class="modal-close-btn">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitForm">
            <!-- Ошибка -->
            <div v-if="formError" class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
              {{ formError }}
            </div>

            <!-- Вкладка: Основное -->
            <div v-show="activeTab === 'info'" class="modal-body space-y-5">
              <div>
                <label class="form-label">Название <span class="text-red-500">*</span></label>
                <input v-model="form.title" type="text" class="input-field" placeholder="Введите название лекции" required autofocus />
              </div>
              <div>
                <label class="form-label">Описание</label>
                <textarea v-model="form.description" class="input-field resize-none" rows="3" placeholder="Краткое описание лекции"></textarea>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="form-label">Курс <span class="text-red-500">*</span></label>
                  <select v-model="form.course_id" class="input-field" required>
                    <option value="">Выберите курс</option>
                    <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Статус</label>
                  <select v-model="form.status" class="input-field">
                    <option value="draft">Черновик</option>
                    <option value="published">Опубликована</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Вкладка: Контент (Markdown) -->
            <div v-show="activeTab === 'content'" class="modal-body">
              <div class="mb-3 flex items-center justify-between">
                <p class="text-sm text-slate-500">Используйте <strong>Markdown</strong>: # Заголовок, **жирный**, *курсив*, - список, ` код`</p>
                <div class="flex gap-1 bg-slate-100 rounded-lg p-0.5">
                  <button type="button" @click="editorMode = 'edit'" class="px-2.5 py-1 rounded text-xs font-medium transition-all" :class="editorMode === 'edit' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500'">
                    Редактор
                  </button>
                  <button type="button" @click="editorMode = 'split'" class="px-2.5 py-1 rounded text-xs font-medium transition-all" :class="editorMode === 'split' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500'">
                    Разделённый
                  </button>
                  <button type="button" @click="editorMode = 'preview'" class="px-2.5 py-1 rounded text-xs font-medium transition-all" :class="editorMode === 'preview' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500'">
                    Превью
                  </button>
                </div>
              </div>

              <!-- Markdown тулбар -->
              <div class="flex flex-wrap gap-1 mb-2 p-2 bg-slate-50 rounded-lg border border-slate-200">
                <button type="button" @click="insertMd('# ', '')" class="md-btn font-bold">H1</button>
                <button type="button" @click="insertMd('## ', '')" class="md-btn font-bold text-sm">H2</button>
                <button type="button" @click="insertMd('### ', '')" class="md-btn font-bold text-xs">H3</button>
                <div class="w-px h-6 bg-slate-200 mx-1"></div>
                <button type="button" @click="insertMd('**', '**')" class="md-btn font-bold">B</button>
                <button type="button" @click="insertMd('*', '*')" class="md-btn italic">I</button>
                <button type="button" @click="insertMd('~~', '~~')" class="md-btn line-through text-sm">S</button>
                <div class="w-px h-6 bg-slate-200 mx-1"></div>
                <button type="button" @click="insertMd('- ', '')" class="md-btn text-xs">• Список</button>
                <button type="button" @click="insertMd('1. ', '')" class="md-btn text-xs">1. Список</button>
                <button type="button" @click="insertMd('> ', '')" class="md-btn text-xs">❝ Цитата</button>
                <div class="w-px h-6 bg-slate-200 mx-1"></div>
                <button type="button" @click="insertMd('`', '`')" class="md-btn font-mono text-xs">Code</button>
                <button type="button" @click="insertMd('\n```\n', '\n```')" class="md-btn font-mono text-xs">Block</button>
                <button type="button" @click="insertMd('[текст](', ')')" class="md-btn text-xs">🔗 Ссылка</button>
                <div class="w-px h-6 bg-slate-200 mx-1"></div>
                <button type="button" @click="insertMd('---\n', '')" class="md-btn text-xs">— Разделитель</button>
              </div>

              <div class="editor-container" :class="{ 'split': editorMode === 'split' }">
                <textarea
                  v-if="editorMode !== 'preview'"
                  ref="mdTextarea"
                  v-model="form.content"
                  class="md-editor"
                  :class="{ 'w-full': editorMode === 'edit' }"
                  placeholder="# Заголовок лекции&#10;&#10;Начните писать содержание лекции в формате Markdown..."
                  spellcheck="true"
                ></textarea>
                <div
                  v-if="editorMode !== 'edit'"
                  class="md-preview prose"
                  :class="{ 'w-full': editorMode === 'preview' }"
                  v-html="renderedPreview"
                ></div>
              </div>
              <p class="text-xs text-slate-400 mt-1">{{ form.content?.length || 0 }} символов</p>
            </div>

            <!-- Футер -->
            <div class="modal-footer border-t border-slate-100 px-6 py-4">
              <button type="button" @click="closeFormModal" class="btn btn-secondary">Отмена</button>
              <button type="submit" class="btn btn-primary" :disabled="formLoading">
                <svg v-if="formLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ editingLecture ? 'Сохранить' : 'Создать' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- ===== МОДАЛКА УДАЛЕНИЯ ===== -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
        <div class="modal-box max-w-md">
          <div class="modal-header">
            <h2 class="text-xl font-bold text-slate-800">Удалить лекцию</h2>
            <button @click="showDeleteModal = false" class="modal-close-btn">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="modal-body">
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 flex-shrink-0 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div>
                <p class="text-slate-700 font-medium">Вы уверены, что хотите удалить лекцию?</p>
                <p class="text-slate-500 text-sm mt-1">«{{ deletingLecture?.title }}»</p>
                <p class="text-slate-400 text-sm mt-2">Все материалы лекции будут удалены. Это действие необратимо.</p>
              </div>
            </div>
            <div class="modal-footer mt-6">
              <button @click="showDeleteModal = false" class="btn btn-secondary">Отмена</button>
              <button @click="confirmDelete" class="btn bg-red-600 text-white hover:bg-red-700 border-red-600" :disabled="formLoading">
                <svg v-if="formLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Удалить
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { marked } from 'marked'
import { useCourseStore } from '../../stores/courses'

marked.setOptions({ breaks: true, gfm: true })

const route = useRoute()
const courseStore = useCourseStore()

const loading = ref(true)
const errorMessage = ref('')
const filters = ref({ course: route.query.course ? String(route.query.course) : '', search: '', status: '' })

const courses = computed(() => courseStore.courses)
const lectures = computed(() => courseStore.lectures)

const filteredLectures = computed(() => {
  return lectures.value.filter(lecture => {
    if (filters.value.course && Number(lecture.course_id) !== Number(filters.value.course)) return false
    if (filters.value.status && lecture.status !== filters.value.status) return false
    if (filters.value.search && !lecture.title.toLowerCase().includes(filters.value.search.toLowerCase())) return false
    return true
  })
})

const getStatusClass = (status) => {
  switch (status) {
    case 'published': return 'badge-success'
    case 'draft': return 'badge-warning'
    default: return 'bg-gray-100 text-gray-800'
  }
}
const getStatusText = (status) => {
  switch (status) {
    case 'published': return 'Опубликована'
    case 'draft': return 'Черновик'
    default: return status
  }
}

onMounted(async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    await courseStore.fetchCourses()
    // Prefer query param course, then first course
    if (!filters.value.course && courseStore.courses.length > 0) {
      filters.value.course = String(courseStore.courses[0].id)
    }
    if (filters.value.course) {
      await courseStore.fetchLectures(filters.value.course)
    }
  } catch (error) {
    errorMessage.value = courseStore.error || 'Не удалось загрузить курсы и лекции'
  } finally {
    loading.value = false
  }
})

watch(() => filters.value.course, async (newCourseId) => {
  errorMessage.value = ''
  if (newCourseId) {
    loading.value = true
    try {
      await courseStore.fetchLectures(newCourseId)
    } catch (error) {
      errorMessage.value = courseStore.error || 'Не удалось загрузить лекции'
    } finally {
      loading.value = false
    }
  } else {
    courseStore.lectures = []
  }
})

// --- Modal state ---
const showFormModal = ref(false)
const showDeleteModal = ref(false)
const editingLecture = ref(null)
const deletingLecture = ref(null)
const formLoading = ref(false)
const formError = ref('')
const activeTab = ref('info')
const editorMode = ref('split')
const mdTextarea = ref(null)

const tabs = [
  { id: 'info', label: 'Основное' },
  { id: 'content', label: 'Контент' },
]

const emptyForm = () => ({
  title: '',
  description: '',
  course_id: filters.value.course || (courses.value[0]?.id ?? ''),
  status: 'draft',
  content: ''
})

const form = ref(emptyForm())

const renderedPreview = computed(() => {
  if (!form.value.content) return '<p class="text-slate-400 italic">Начните вводить текст...</p>'
  return marked.parse(form.value.content)
})

const openCreateModal = () => {
  editingLecture.value = null
  form.value = emptyForm()
  formError.value = ''
  activeTab.value = 'info'
  showFormModal.value = true
}

const openEditModal = (lecture) => {
  editingLecture.value = lecture
  form.value = {
    title: lecture.title,
    description: lecture.description || '',
    course_id: lecture.course_id,
    status: lecture.status || 'draft',
    content: lecture.content || ''
  }
  formError.value = ''
  activeTab.value = 'info'
  showFormModal.value = true
}

const closeFormModal = () => {
  showFormModal.value = false
  editingLecture.value = null
}

const openDeleteModal = (lecture) => {
  deletingLecture.value = lecture
  showDeleteModal.value = true
}

const submitForm = async () => {
  formError.value = ''
  if (!form.value.title.trim()) { formError.value = 'Введите название лекции'; activeTab.value = 'info'; return }
  if (!form.value.course_id) { formError.value = 'Выберите курс'; activeTab.value = 'info'; return }

  formLoading.value = true
  try {
    const payload = {
      title: form.value.title.trim(),
      description: form.value.description,
      status: form.value.status,
      content: form.value.content || null
    }

    if (editingLecture.value) {
      await courseStore.updateLecture(editingLecture.value.id, payload)
    } else {
      await courseStore.createLecture(form.value.course_id, payload)
    }
    closeFormModal()
  } catch (e) {
    formError.value = e?.response?.data?.message || (editingLecture.value ? 'Ошибка при обновлении' : 'Ошибка при создании')
  } finally {
    formLoading.value = false
  }
}

const confirmDelete = async () => {
  if (!deletingLecture.value) return
  formLoading.value = true
  try {
    await courseStore.deleteLecture(deletingLecture.value.id)
    showDeleteModal.value = false
    deletingLecture.value = null
  } catch (e) {
    showDeleteModal.value = false
  } finally {
    formLoading.value = false
  }
}

// Markdown toolbar helper
const insertMd = (before, after) => {
  const el = mdTextarea.value
  if (!el) {
    form.value.content = (form.value.content || '') + before + after
    return
  }
  const start = el.selectionStart
  const end = el.selectionEnd
  const selected = form.value.content.substring(start, end)
  const newText = form.value.content.substring(0, start) + before + selected + after + form.value.content.substring(end)
  form.value.content = newText
  // restore cursor
  setTimeout(() => {
    el.focus()
    const newPos = start + before.length + selected.length + after.length
    el.setSelectionRange(newPos, newPos)
  }, 0)
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
  padding: 1rem;
  animation: fadeIn 0.15s ease;
}
.modal-box {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  width: 100%;
  max-width: 540px;
  animation: slideUp 0.2s ease;
  display: flex;
  flex-direction: column;
  max-height: calc(100vh - 2rem);
  overflow: hidden;
}
.modal-wide {
  max-width: 900px;
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  flex-shrink: 0;
}
.modal-body {
  padding: 1.5rem;
  overflow-y: auto;
}
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}
.modal-close-btn {
  width: 2rem;
  height: 2rem;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  transition: all 0.15s;
}
.modal-close-btn:hover { background: #f1f5f9; color: #475569; }
.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.375rem;
}

/* Markdown editor */
.editor-container {
  display: flex;
  gap: 0.75rem;
  height: 420px;
}
.editor-container.split .md-editor,
.editor-container.split .md-preview {
  width: 50%;
}
.md-editor {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  font-family: 'Fira Code', 'Cascadia Code', monospace;
  font-size: 0.875rem;
  line-height: 1.6;
  resize: none;
  outline: none;
  color: #1e293b;
  background: #f8fafc;
  transition: border-color 0.15s;
}
.md-editor:focus { border-color: #6366f1; background: white; }
.md-preview {
  flex: 1;
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  overflow-y: auto;
  background: white;
  font-size: 0.9rem;
  line-height: 1.75;
}

/* Markdown toolbar */
.md-btn {
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.8125rem;
  color: #475569;
  background: white;
  border: 1px solid #e2e8f0;
  cursor: pointer;
  transition: all 0.15s;
  white-space: nowrap;
}
.md-btn:hover { background: #f1f5f9; border-color: #cbd5e1; color: #1e293b; }

/* Prose preview styles */
.prose :deep(h1) { font-size: 1.5em; font-weight: 800; margin-top: 1em; margin-bottom: 0.4em; color: #0f172a; }
.prose :deep(h2) { font-size: 1.25em; font-weight: 700; margin-top: 1em; margin-bottom: 0.4em; color: #1e293b; }
.prose :deep(h3) { font-size: 1.1em; font-weight: 600; margin-top: 0.8em; margin-bottom: 0.3em; color: #334155; }
.prose :deep(p) { margin-bottom: 0.75em; color: #475569; }
.prose :deep(ul), .prose :deep(ol) { padding-left: 1.5em; margin-bottom: 0.75em; }
.prose :deep(li) { margin-bottom: 0.3em; color: #475569; }
.prose :deep(code) { background: #f1f5f9; padding: 0.15em 0.4em; border-radius: 4px; font-family: monospace; font-size: 0.85em; color: #6366f1; }
.prose :deep(pre) { background: #1e293b; color: #e2e8f0; padding: 1em; border-radius: 8px; overflow-x: auto; margin: 0.75em 0; }
.prose :deep(pre code) { background: transparent; color: inherit; padding: 0; }
.prose :deep(blockquote) { border-left: 3px solid #6366f1; padding-left: 1em; color: #64748b; margin: 0.75em 0; font-style: italic; }
.prose :deep(hr) { border: none; border-top: 1px solid #e2e8f0; margin: 1em 0; }
.prose :deep(a) { color: #6366f1; text-decoration: underline; }
.prose :deep(strong) { font-weight: 600; color: #1e293b; }
.prose :deep(table) { width: 100%; border-collapse: collapse; margin: 0.75em 0; }
.prose :deep(th), .prose :deep(td) { border: 1px solid #e2e8f0; padding: 0.5em 0.75em; text-align: left; }
.prose :deep(th) { background: #f8fafc; font-weight: 600; }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
</style>
