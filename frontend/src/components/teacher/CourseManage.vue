<template>
  <div class="animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
      <div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
          Управление <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-500 to-cyan-500">курсами</span>
        </h1>
        <p class="text-slate-500 mt-1">{{ courses.length }} {{ pluralCourses(courses.length) }}</p>
      </div>
      <button @click="openCreateModal" class="btn btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Создать курс
      </button>
    </div>

    <!-- Stats bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="card p-4 flex items-center gap-3 animate-slide-up stagger-1">
        <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        </div>
        <div>
          <div class="text-2xl font-bold text-slate-800">{{ courses.length }}</div>
          <div class="text-xs text-slate-500 font-medium">Всего курсов</div>
        </div>
      </div>
      <div class="card p-4 flex items-center gap-3 animate-slide-up stagger-2">
        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <div class="text-2xl font-bold text-slate-800">{{ publishedCount }}</div>
          <div class="text-xs text-slate-500 font-medium">Опубликовано</div>
        </div>
      </div>
      <div class="card p-4 flex items-center gap-3 animate-slide-up stagger-3">
        <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
        <div>
          <div class="text-2xl font-bold text-slate-800">{{ totalStudents }}</div>
          <div class="text-xs text-slate-500 font-medium">Студентов</div>
        </div>
      </div>
      <div class="card p-4 flex items-center gap-3 animate-slide-up stagger-4">
        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
          </svg>
        </div>
        <div>
          <div class="text-2xl font-bold text-slate-800">{{ totalLectures }}</div>
          <div class="text-xs text-slate-500 font-medium">Лекций</div>
        </div>
      </div>
    </div>

    <!-- Course list -->
    <div v-if="loading" class="text-center py-20">
      <svg class="animate-spin h-8 w-8 text-sky-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
      </svg>
      <p class="text-slate-500">Загрузка курсов...</p>
    </div>

    <div v-else-if="courses.length === 0" class="text-center py-20 card">
      <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
      </div>
      <p class="text-slate-500 text-lg mb-4">У вас пока нет курсов</p>
      <button @click="openCreateModal" class="btn btn-primary">Создать первый курс</button>
    </div>

    <div v-else class="space-y-5">
      <div
        v-for="(course, index) in courses"
        :key="course.id"
        class="card overflow-hidden animate-slide-up"
        :style="{ animationDelay: `${index * 0.04}s` }"
      >
        <!-- Main row -->
        <div class="p-6 flex flex-col md:flex-row md:items-start gap-5">
          <!-- Left: info -->
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2.5 mb-2">
              <h3 class="text-xl font-bold text-slate-800 leading-snug">{{ course.title }}</h3>
              <span :class="statusClass(course.status)" class="badge text-xs">{{ statusText(course.status) }}</span>
            </div>
            <p class="text-slate-500 text-sm mb-4 line-clamp-2">{{ course.description }}</p>

            <div class="flex flex-wrap gap-5 text-sm text-slate-500">
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <strong>{{ course.lectures_count || 0 }}</strong> лекций
              </span>
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <strong>{{ course.tests_count || 0 }}</strong> тестов
              </span>
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <strong>{{ course.enrollments_count || 0 }}</strong> студентов
              </span>
            </div>
          </div>

          <!-- Right: actions -->
          <div class="flex flex-col gap-2 flex-shrink-0 md:items-end">
            <div class="flex gap-2">
              <button @click="openEditModal(course)" class="btn btn-secondary text-sm py-2 px-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Редактировать
              </button>
              <button @click="confirmDelete(course)" class="btn text-sm py-2 px-4 bg-red-50 text-red-600 border-red-200 hover:bg-red-100 hover:border-red-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Удалить
              </button>
            </div>
          </div>
        </div>

        <!-- Footer: quick actions -->
        <div class="px-6 py-3 bg-slate-50/70 border-t border-slate-100 flex flex-wrap items-center gap-x-5 gap-y-2">
          <router-link :to="`/courses/${course.id}`" class="quick-link text-sky-600 hover:text-sky-700">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Просмотр
          </router-link>
          <span class="text-slate-300 text-xs">|</span>
          <button @click="goToLectures(course)" class="quick-link text-emerald-600 hover:text-emerald-700">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            Лекции ({{ course.lectures_count || 0 }})
          </button>
          <span class="text-slate-300 text-xs">|</span>
          <button @click="goToTests(course)" class="quick-link text-violet-600 hover:text-violet-700">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Тесты ({{ course.tests_count || 0 }})
          </button>
          <span v-if="course.status === 'draft'" class="text-slate-300 text-xs">|</span>
          <button v-if="course.status === 'draft'" @click="publishCourse(course)" class="quick-link text-emerald-600 hover:text-emerald-700 font-semibold">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Опубликовать
          </button>
          <span v-if="course.status === 'published'" class="text-slate-300 text-xs">|</span>
          <span class="text-slate-300 text-xs">|</span>
          <router-link :to="`/teacher/courses/${course.id}/students`" class="quick-link text-amber-600 hover:text-amber-700">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Студенты ({{ course.enrollments_count || 0 }})
          </router-link>
          <span v-if="course.status === 'published'" class="text-slate-300 text-xs">|</span>
          <button v-if="course.status === 'published'" @click="unpublishCourse(course)" class="quick-link text-amber-600 hover:text-amber-700">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
            Снять с публикации
          </button>
        </div>
      </div>
    </div>

    <!-- ===== CREATE / EDIT MODAL ===== -->
    <Teleport to="body">
      <div v-if="showFormModal" class="modal-overlay" @click.self="closeFormModal">
        <div class="modal-box max-w-xl w-full">
          <div class="modal-header">
            <h2 class="text-xl font-bold text-slate-800">
              {{ editingCourse ? 'Редактировать курс' : 'Создать новый курс' }}
            </h2>
            <button @click="closeFormModal" class="modal-close-btn">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitForm">
            <div v-if="formError" class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
              {{ formError }}
            </div>

            <div class="modal-body space-y-5">
              <div>
                <label class="form-label">Название <span class="text-red-500">*</span></label>
                <input
                  v-model="form.title"
                  type="text"
                  class="input-field"
                  placeholder="Введите название курса"
                  required
                  autofocus
                />
              </div>

              <div>
                <label class="form-label">Описание <span class="text-red-500">*</span></label>
                <textarea
                  v-model="form.description"
                  class="input-field resize-none"
                  rows="4"
                  placeholder="Подробно опишите курс: чему научится студент, для кого предназначен"
                  required
                ></textarea>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="form-label">Статус</label>
                  <select v-model="form.status" class="input-field">
                    <option value="draft">Черновик</option>
                    <option value="published">Опубликован</option>
                    <option value="archived">В архиве</option>
                  </select>
                </div>
                <div class="flex items-end pb-0.5">
                  <label class="flex items-center gap-3 cursor-pointer select-none">
                    <div
                      class="relative w-11 h-6 rounded-full transition-colors duration-200"
                      :class="form.is_featured ? 'bg-sky-500' : 'bg-slate-200'"
                      @click="form.is_featured = !form.is_featured"
                    >
                      <div
                        class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
                        :class="form.is_featured ? 'translate-x-5' : ''"
                      ></div>
                    </div>
                    <span class="text-sm font-medium text-slate-700">Рекомендуемый</span>
                  </label>
                </div>
              </div>

              <div v-if="form.status === 'published'" class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 text-sm text-emerald-700 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Курс будет виден всем студентам сразу после сохранения.
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" @click="closeFormModal" class="btn btn-secondary">Отмена</button>
              <button type="submit" :disabled="submitting" class="btn btn-primary min-w-[140px]">
                <svg v-if="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ submitting ? 'Сохранение...' : (editingCourse ? 'Сохранить изменения' : 'Создать курс') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- ===== DELETE CONFIRM MODAL ===== -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
        <div class="modal-box max-w-md w-full animate-scale-in">
          <div class="p-6 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
              <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Удалить курс?</h3>
            <p class="text-slate-500 text-sm mb-1">
              Курс <strong class="text-slate-700">«{{ courseToDelete?.title }}»</strong> будет удалён безвозвратно.
            </p>
            <p v-if="(courseToDelete?.lectures_count || 0) > 0 || (courseToDelete?.tests_count || 0) > 0"
               class="text-amber-600 text-sm mt-2 bg-amber-50 border border-amber-200 rounded-lg p-2">
              Сначала удалите все лекции ({{ courseToDelete?.lectures_count || 0 }}) и тесты ({{ courseToDelete?.tests_count || 0 }}).
            </p>
          </div>
          <div v-if="deleteError" class="mx-6 mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
            {{ deleteError }}
          </div>
          <div class="px-6 pb-6 flex gap-3 justify-end">
            <button @click="showDeleteModal = false" class="btn btn-secondary">Отмена</button>
            <button
              @click="executeDelete"
              :disabled="deleting || (courseToDelete?.lectures_count || 0) > 0 || (courseToDelete?.tests_count || 0) > 0"
              class="btn bg-red-600 text-white hover:bg-red-700 border-red-600 min-w-[100px] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="deleting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              {{ deleting ? 'Удаление...' : 'Удалить' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCourseStore } from '../../stores/courses'
import axios from 'axios'

const router = useRouter()
const courseStore = useCourseStore()

const loading = ref(true)
const submitting = ref(false)
const deleting = ref(false)

const showFormModal = ref(false)
const showDeleteModal = ref(false)
const editingCourse = ref(null)
const courseToDelete = ref(null)

const formError = ref('')
const deleteError = ref('')

const courses = ref([])

const form = ref({
  title: '',
  description: '',
  status: 'draft',
  is_featured: false,
})

// Derived stats
const publishedCount = computed(() => courses.value.filter(c => c.status === 'published').length)
const totalStudents = computed(() => courses.value.reduce((s, c) => s + (c.enrollments_count || 0), 0))
const totalLectures = computed(() => courses.value.reduce((s, c) => s + (c.lectures_count || 0), 0))

const statusClass = (status) => ({
  'published': 'badge-success',
  'draft': 'badge-warning',
  'archived': 'bg-slate-100 text-slate-600 border-slate-200',
}[status] || 'bg-slate-100 text-slate-600')

const statusText = (status) => ({
  'published': 'Опубликован',
  'draft': 'Черновик',
  'archived': 'В архиве',
}[status] || status)

const pluralCourses = (n) => {
  if (n % 10 === 1 && n % 100 !== 11) return 'курс'
  if ([2, 3, 4].includes(n % 10) && ![12, 13, 14].includes(n % 100)) return 'курса'
  return 'курсов'
}

// Load teacher's courses with enrollment counts
const loadCourses = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/teacher/courses')
    courses.value = data.data
  } catch {
    // fallback to courseStore
    await courseStore.fetchCourses()
    courses.value = courseStore.courses
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  editingCourse.value = null
  form.value = { title: '', description: '', status: 'draft', is_featured: false }
  formError.value = ''
  showFormModal.value = true
}

const openEditModal = (course) => {
  editingCourse.value = course
  form.value = {
    title: course.title,
    description: course.description,
    status: course.status,
    is_featured: course.is_featured || false,
  }
  formError.value = ''
  showFormModal.value = true
}

const closeFormModal = () => {
  showFormModal.value = false
  editingCourse.value = null
}

const submitForm = async () => {
  submitting.value = true
  formError.value = ''
  try {
    if (editingCourse.value) {
      await courseStore.updateCourse(editingCourse.value.id, form.value)
      const idx = courses.value.findIndex(c => c.id === editingCourse.value.id)
      if (idx !== -1) {
        courses.value[idx] = { ...courses.value[idx], ...form.value }
      }
    } else {
      const created = await courseStore.createCourse(form.value)
      if (created?.id) {
        courses.value.unshift({ ...created, lectures_count: 0, tests_count: 0, enrollments_count: 0 })
      } else {
        await loadCourses()
      }
    }
    closeFormModal()
  } catch (err) {
    formError.value = err?.response?.data?.message || 'Ошибка при сохранении курса'
  } finally {
    submitting.value = false
  }
}

const confirmDelete = (course) => {
  courseToDelete.value = course
  deleteError.value = ''
  showDeleteModal.value = true
}

const executeDelete = async () => {
  if (!courseToDelete.value) return
  deleting.value = true
  deleteError.value = ''
  try {
    await courseStore.deleteCourse(courseToDelete.value.id)
    courses.value = courses.value.filter(c => c.id !== courseToDelete.value.id)
    showDeleteModal.value = false
  } catch (err) {
    deleteError.value = err?.response?.data?.message || 'Ошибка при удалении курса'
  } finally {
    deleting.value = false
  }
}

const publishCourse = async (course) => {
  try {
    await courseStore.updateCourse(course.id, { status: 'published' })
    const idx = courses.value.findIndex(c => c.id === course.id)
    if (idx !== -1) courses.value[idx].status = 'published'
  } catch {
    // ignore
  }
}

const unpublishCourse = async (course) => {
  try {
    await courseStore.updateCourse(course.id, { status: 'draft' })
    const idx = courses.value.findIndex(c => c.id === course.id)
    if (idx !== -1) courses.value[idx].status = 'draft'
  } catch {
    // ignore
  }
}

const goToLectures = (course) => {
  router.push({ path: '/teacher/lectures', query: { course: course.id } })
}

const goToTests = (course) => {
  router.push({ path: '/teacher/tests', query: { course: course.id } })
}

onMounted(loadCourses)
</script>

<style scoped>
.quick-link {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.8125rem;
  font-weight: 500;
  transition: color 0.15s;
}
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
  flex: 1;
}
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid #f1f5f9;
  flex-shrink: 0;
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
  background: transparent;
  border: none;
  cursor: pointer;
}
.modal-close-btn:hover { background: #f1f5f9; color: #475569; }
.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.375rem;
}

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
</style>
