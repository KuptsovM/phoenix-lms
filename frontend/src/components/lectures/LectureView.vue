<template>
  <div class="min-h-screen animate-fade-in">
    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-32">
      <div class="text-center">
        <svg class="animate-spin h-10 w-10 text-sky-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p class="text-slate-500">Загрузка лекции...</p>
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="card p-8 text-center">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h3 class="text-lg font-bold text-slate-800 mb-2">Ошибка загрузки</h3>
      <p class="text-slate-500 mb-4">{{ error }}</p>
      <button @click="$router.back()" class="btn btn-secondary">← Назад</button>
    </div>

    <!-- Content -->
    <div v-else-if="lecture" class="max-w-5xl mx-auto">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-sm text-slate-400 mb-6 flex-wrap">
        <router-link to="/courses" class="hover:text-sky-600 transition-colors">Курсы</router-link>
        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <router-link v-if="lecture.course_id" :to="`/courses/${lecture.course_id}`" class="hover:text-sky-600 transition-colors truncate max-w-[180px]">
          {{ lecture.course_title || 'Курс' }}
        </router-link>
        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-slate-600 font-medium truncate max-w-[200px]">{{ lecture.title }}</span>
      </div>

      <!-- Main card -->
      <div class="card overflow-hidden mb-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-5 md:px-8">
          <div class="flex items-start justify-between gap-4">
            <div>
              <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1 text-xs font-medium text-sky-300 bg-sky-900/40 px-2 py-0.5 rounded-full">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                  Лекция
                </span>
                <span v-if="viewed" class="inline-flex items-center gap-1 text-xs font-medium text-emerald-300 bg-emerald-900/40 px-2 py-0.5 rounded-full">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Прочитано
                </span>
              </div>
              <h1 class="text-xl md:text-2xl font-bold text-white leading-snug">{{ lecture.title }}</h1>
              <p v-if="lecture.description" class="text-slate-300 text-sm mt-1.5 leading-relaxed">{{ lecture.description }}</p>
            </div>
          </div>
        </div>

        <!-- Article content -->
        <div class="p-6 md:p-10">
          <div v-if="lecture.content" class="lecture-prose" v-html="renderedContent"></div>
          <div v-else class="text-center py-16 text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="font-medium">Содержание лекции ещё не добавлено</p>
          </div>
        </div>

        <!-- Materials -->
        <div v-if="materials.length > 0" class="px-6 md:px-10 pb-6">
          <h3 class="text-sm font-semibold text-slate-600 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
            </svg>
            Дополнительные материалы
          </h3>
          <div class="flex flex-wrap gap-2">
            <a
              v-for="m in materials"
              :key="m.id"
              :href="m.file_url"
              target="_blank"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-50 border border-slate-200 text-sm text-slate-700 hover:border-sky-300 hover:text-sky-700 hover:bg-sky-50 transition-colors"
            >
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              {{ m.title }}
              <span v-if="m.file_size" class="text-xs text-slate-400">({{ m.file_size }})</span>
            </a>
          </div>
        </div>

        <!-- Footer: complete + nav -->
        <div class="px-6 md:px-10 py-5 bg-slate-50/70 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
          <!-- Mark as complete -->
          <button
            v-if="!viewed"
            @click="markComplete"
            :disabled="completing"
            class="btn btn-primary w-full sm:w-auto"
          >
            <svg v-if="completing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ completing ? 'Сохраняем...' : 'Отметить как прочитанное' }}
          </button>
          <div v-else class="flex items-center gap-2 text-emerald-600 font-medium text-sm w-full sm:w-auto justify-center sm:justify-start">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Лекция прочитана
          </div>

          <!-- Prev / Next -->
          <div class="flex items-center gap-2 ml-auto">
            <router-link
              v-if="prevLecture"
              :to="`/lectures/${prevLecture.id}`"
              class="btn btn-secondary text-sm py-2 px-4"
            >
              ← Предыдущая
            </router-link>
            <router-link
              v-if="nextLecture"
              :to="`/lectures/${nextLecture.id}`"
              class="btn btn-primary text-sm py-2 px-4"
              @click="nextLecture && !viewed && markComplete()"
            >
              Следующая →
            </router-link>
            <router-link
              v-else-if="lecture.course_id"
              :to="`/courses/${lecture.course_id}`"
              class="btn btn-secondary text-sm py-2 px-4"
            >
              К курсу
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { marked } from 'marked'
import { useStudentStore } from '../../stores/studentStore'
import axios from 'axios'

marked.setOptions({ breaks: true, gfm: true })

const route = useRoute()
const studentStore = useStudentStore()

const loading = ref(true)
const error = ref(null)
const completing = ref(false)
const lecture = ref(null)
const materials = ref([])
const courseLectures = ref([])
const viewed = ref(false)

const renderedContent = computed(() => {
  if (!lecture.value?.content) return ''
  try {
    return marked.parse(lecture.value.content)
  } catch {
    return lecture.value.content
  }
})

const currentIdx = computed(() => courseLectures.value.findIndex(l => l.id === lecture.value?.id))
const prevLecture = computed(() => currentIdx.value > 0 ? courseLectures.value[currentIdx.value - 1] : null)
const nextLecture = computed(() => currentIdx.value < courseLectures.value.length - 1 ? courseLectures.value[currentIdx.value + 1] : null)

const markComplete = async () => {
  if (viewed.value || completing.value) return
  completing.value = true
  try {
    await studentStore.completeLecture(lecture.value.id)
    viewed.value = true
  } catch {
    // silently fail
  } finally {
    completing.value = false
  }
}

onMounted(async () => {
  loading.value = true
  error.value = null
  try {
    const lectureId = route.params.id
    const [lectureRes, materialsRes] = await Promise.all([
      axios.get(`/api/lectures/${lectureId}`),
      axios.get(`/api/lectures/${lectureId}/materials`).catch(() => ({ data: { data: [] } })),
    ])

    lecture.value = {
      ...lectureRes.data,
      course_title: lectureRes.data.course?.title || '',
    }
    materials.value = materialsRes.data.data || []

    // Check if already viewed
    viewed.value = studentStore.isLectureViewed(Number(lectureId))

    // Load sibling lectures for prev/next navigation
    if (lecture.value.course_id) {
      try {
        const siblingsRes = await axios.get(`/api/courses/${lecture.value.course_id}/lectures`)
        courseLectures.value = (siblingsRes.data.data || []).sort((a, b) => (a.order ?? 0) - (b.order ?? 0))
      } catch {
        // navigation disabled
      }
    }
  } catch (e) {
    error.value = e?.response?.data?.message || 'Не удалось загрузить лекцию'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.lecture-prose {
  line-height: 1.85;
  color: #334155;
  font-size: 1rem;
}
.lecture-prose :deep(h1) {
  font-size: 1.75rem;
  font-weight: 800;
  color: #0f172a;
  margin-top: 2rem;
  margin-bottom: 0.75rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #f1f5f9;
}
.lecture-prose :deep(h2) {
  font-size: 1.375rem;
  font-weight: 700;
  color: #1e293b;
  margin-top: 1.75rem;
  margin-bottom: 0.6rem;
}
.lecture-prose :deep(h3) {
  font-size: 1.125rem;
  font-weight: 600;
  color: #334155;
  margin-top: 1.25rem;
  margin-bottom: 0.5rem;
}
.lecture-prose :deep(p) {
  margin-bottom: 1.1rem;
}
.lecture-prose :deep(ul), .lecture-prose :deep(ol) {
  padding-left: 1.75rem;
  margin-bottom: 1.1rem;
}
.lecture-prose :deep(li) {
  margin-bottom: 0.4rem;
}
.lecture-prose :deep(code) {
  background: #f1f5f9;
  padding: 0.18em 0.45em;
  border-radius: 4px;
  font-family: 'Fira Code', 'Cascadia Code', monospace;
  font-size: 0.875em;
  color: #6366f1;
}
.lecture-prose :deep(pre) {
  background: #1e293b;
  color: #e2e8f0;
  padding: 1.25rem;
  border-radius: 10px;
  overflow-x: auto;
  margin: 1.25rem 0;
  font-size: 0.9em;
  line-height: 1.7;
}
.lecture-prose :deep(pre code) {
  background: transparent;
  color: inherit;
  padding: 0;
}
.lecture-prose :deep(blockquote) {
  border-left: 4px solid #6366f1;
  padding: 0.75rem 1.25rem;
  color: #64748b;
  background: #f8faff;
  border-radius: 0 8px 8px 0;
  margin: 1.25rem 0;
  font-style: italic;
}
.lecture-prose :deep(hr) {
  border: none;
  border-top: 2px solid #f1f5f9;
  margin: 2rem 0;
}
.lecture-prose :deep(a) {
  color: #0ea5e9;
  text-decoration: underline;
  text-underline-offset: 2px;
}
.lecture-prose :deep(strong) { font-weight: 700; color: #0f172a; }
.lecture-prose :deep(em) { font-style: italic; color: #475569; }
.lecture-prose :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin: 1.25rem 0;
  font-size: 0.9rem;
}
.lecture-prose :deep(th), .lecture-prose :deep(td) {
  border: 1px solid #e2e8f0;
  padding: 0.6rem 0.9rem;
  text-align: left;
}
.lecture-prose :deep(th) {
  background: #f8fafc;
  font-weight: 600;
  color: #334155;
}
.lecture-prose :deep(img) {
  max-width: 100%;
  border-radius: 8px;
  margin: 1rem 0;
}
</style>
