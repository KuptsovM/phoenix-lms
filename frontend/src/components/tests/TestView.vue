<template>
  <div class="animate-fade-in">
    <!-- Loading -->
    <div v-if="phase === 'loading'" class="flex items-center justify-center py-32">
      <div class="text-center">
        <svg class="animate-spin h-10 w-10 text-violet-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p class="text-slate-500">Загрузка теста...</p>
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="phase === 'error'" class="card p-10 text-center max-w-lg mx-auto">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h3 class="text-lg font-bold text-slate-800 mb-2">{{ errorMsg }}</h3>
      <button @click="$router.back()" class="btn btn-secondary mt-4">← Назад</button>
    </div>

    <!-- ===== START SCREEN ===== -->
    <div v-else-if="phase === 'start'" class="max-w-2xl mx-auto">
      <!-- Past results banner -->
      <div v-if="pastResults.length > 0" class="card p-4 mb-5 bg-gradient-to-r from-violet-50 to-purple-50 border-violet-200">
        <div class="flex items-center justify-between flex-wrap gap-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div>
              <div class="font-semibold text-slate-800 text-sm">Лучший результат: <span class="text-violet-700">{{ bestResult }}%</span></div>
              <div class="text-xs text-slate-500">{{ pastResults.length }} попыток</div>
            </div>
          </div>
          <span :class="bestResult >= 70 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'" class="text-xs font-semibold px-3 py-1 rounded-full">
            {{ bestResult >= 70 ? '✓ Сдан' : '✗ Не сдан' }}
          </span>
        </div>
      </div>

      <div class="card overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-6 py-8 text-white text-center">
          <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h1 class="text-2xl font-bold mb-2">{{ test.title }}</h1>
          <p v-if="test.description" class="text-violet-200 text-sm">{{ test.description }}</p>
        </div>

        <!-- Meta grid -->
        <div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100">
          <div class="p-4 text-center">
            <div class="text-2xl font-bold text-slate-800">{{ test.questions_count }}</div>
            <div class="text-xs text-slate-500 mt-0.5">вопросов</div>
          </div>
          <div class="p-4 text-center">
            <div class="text-2xl font-bold text-slate-800">{{ test.duration }}</div>
            <div class="text-xs text-slate-500 mt-0.5">минут</div>
          </div>
          <div class="p-4 text-center">
            <div class="text-2xl font-bold" :class="difficultyColor">{{ difficultyLabel }}</div>
            <div class="text-xs text-slate-500 mt-0.5">сложность</div>
          </div>
        </div>

        <!-- Rules -->
        <div class="p-6 space-y-3">
          <h3 class="font-semibold text-slate-700 text-sm">Условия теста:</h3>
          <ul class="space-y-2 text-sm text-slate-600">
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Время ограничено: {{ test.duration }} минут
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Для прохождения нужно набрать 70% и выше
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Ответы сохраняются автоматически
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              После отправки ответы изменить нельзя
            </li>
          </ul>
        </div>

        <div class="px-6 pb-6 flex gap-3">
          <button @click="$router.back()" class="btn btn-secondary flex-1">Назад</button>
          <button @click="startTest" :disabled="starting" class="btn btn-primary flex-1">
            <svg v-if="starting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ starting ? 'Запуск...' : 'Начать тест' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ===== TAKING TEST ===== -->
    <div v-else-if="phase === 'taking'" class="max-w-5xl mx-auto">
      <div class="flex flex-col lg:flex-row gap-5">
        <!-- Sidebar: question nav -->
        <div class="lg:w-64 flex-shrink-0">
          <div class="card p-4 lg:sticky lg:top-4">
            <!-- Timer -->
            <div class="flex items-center justify-between mb-4 p-3 rounded-xl" :class="timerUrgent ? 'bg-red-50 border border-red-200' : 'bg-slate-50'">
              <span class="text-sm font-medium text-slate-600">Время</span>
              <span class="text-xl font-bold font-mono" :class="timerUrgent ? 'text-red-600' : 'text-slate-800'">
                {{ timerDisplay }}
              </span>
            </div>

            <!-- Progress -->
            <div class="mb-4">
              <div class="flex justify-between text-xs text-slate-500 mb-1.5">
                <span>Прогресс</span>
                <span>{{ answeredCount }}/{{ questions.length }}</span>
              </div>
              <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                <div
                  class="h-full bg-gradient-to-r from-violet-500 to-purple-500 rounded-full transition-all duration-300"
                  :style="{ width: `${answeredPercent}%` }"
                ></div>
              </div>
            </div>

            <!-- Question grid -->
            <div class="grid grid-cols-5 gap-1.5">
              <button
                v-for="(q, idx) in questions"
                :key="q.id"
                @click="goToQuestion(idx)"
                class="w-full aspect-square rounded-lg text-xs font-bold transition-all"
                :class="questionBtnClass(idx)"
              >
                {{ idx + 1 }}
              </button>
            </div>

            <div class="flex gap-2 mt-3 text-xs text-slate-500">
              <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-violet-500 inline-block"></span>Текущий</span>
              <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span>Отвечено</span>
            </div>
          </div>
        </div>

        <!-- Main: question -->
        <div class="flex-1">
          <div class="card overflow-hidden">
            <!-- Question header -->
            <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-violet-100 text-violet-700 font-bold text-sm flex items-center justify-center flex-shrink-0">
                  {{ currentIdx + 1 }}
                </span>
                <span class="text-sm text-slate-500 font-medium">из {{ questions.length }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-full">
                  {{ typeLabel(currentQuestion.type) }}
                </span>
                <span class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded-full font-medium">
                  {{ currentQuestion.points }} {{ plural(currentQuestion.points, 'балл', 'балла', 'баллов') }}
                </span>
              </div>
            </div>

            <!-- Question text -->
            <div class="p-6 md:p-8">
              <p class="text-lg font-semibold text-slate-800 leading-relaxed mb-6">{{ currentQuestion.question }}</p>

              <!-- Multiple choice -->
              <div v-if="currentQuestion.type === 'multiple_choice'" class="space-y-3">
                <label
                  v-for="(opt, oi) in currentQuestion.options"
                  :key="oi"
                  class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
                  :class="answers[currentQuestion.id] === opt
                    ? 'border-violet-500 bg-violet-50'
                    : 'border-slate-200 hover:border-violet-300 hover:bg-violet-50/30'"
                >
                  <input
                    type="radio"
                    :name="`q-${currentQuestion.id}`"
                    :value="opt"
                    v-model="answers[currentQuestion.id]"
                    class="sr-only"
                  />
                  <span
                    class="w-5 h-5 rounded-full border-2 flex-shrink-0 flex items-center justify-center transition-colors"
                    :class="answers[currentQuestion.id] === opt ? 'border-violet-500 bg-violet-500' : 'border-slate-300'"
                  >
                    <span v-if="answers[currentQuestion.id] === opt" class="w-2 h-2 rounded-full bg-white"></span>
                  </span>
                  <span class="text-slate-700 font-medium">{{ opt }}</span>
                </label>
              </div>

              <!-- Boolean -->
              <div v-else-if="currentQuestion.type === 'boolean'" class="grid grid-cols-2 gap-4">
                <label
                  v-for="opt in ['true', 'false']"
                  :key="opt"
                  class="flex items-center justify-center gap-2 p-5 rounded-xl border-2 cursor-pointer transition-all font-semibold text-base"
                  :class="answers[currentQuestion.id] === opt
                    ? (opt === 'true' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-red-500 bg-red-50 text-red-700')
                    : 'border-slate-200 hover:border-slate-300 text-slate-600'"
                >
                  <input type="radio" :name="`q-${currentQuestion.id}`" :value="opt" v-model="answers[currentQuestion.id]" class="sr-only" />
                  <svg v-if="opt === 'true'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  {{ opt === 'true' ? 'Верно' : 'Неверно' }}
                </label>
              </div>

              <!-- Text input -->
              <div v-else-if="currentQuestion.type === 'text'">
                <textarea
                  v-model="answers[currentQuestion.id]"
                  rows="4"
                  class="input-field resize-none"
                  placeholder="Введите ваш ответ..."
                ></textarea>
              </div>
            </div>

            <!-- Navigation -->
            <div class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between gap-3">
              <button
                @click="prevQuestion"
                :disabled="currentIdx === 0"
                class="btn btn-secondary text-sm py-2 px-5 disabled:opacity-40 disabled:cursor-not-allowed"
              >
                ← Назад
              </button>

              <button
                v-if="currentIdx < questions.length - 1"
                @click="nextQuestion"
                class="btn btn-primary text-sm py-2 px-5"
              >
                Далее →
              </button>
              <button
                v-else
                @click="goToReview"
                class="btn bg-gradient-to-r from-violet-600 to-purple-600 text-white text-sm py-2 px-5"
              >
                Проверить ответы
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== REVIEW SCREEN ===== -->
    <div v-else-if="phase === 'review'" class="max-w-2xl mx-auto">
      <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
          <h2 class="text-xl font-bold text-slate-800">Проверка ответов</h2>
          <p class="text-slate-500 text-sm mt-1">
            Отвечено <strong>{{ answeredCount }}</strong> из {{ questions.length }} вопросов.
            <span v-if="unansweredCount > 0" class="text-amber-600">{{ unansweredCount }} без ответа.</span>
          </p>
        </div>

        <div class="divide-y divide-slate-100">
          <div
            v-for="(q, idx) in questions"
            :key="q.id"
            class="px-6 py-4 flex items-start gap-3 cursor-pointer hover:bg-slate-50 transition-colors"
            @click="goToQuestionFromReview(idx)"
          >
            <span
              class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0"
              :class="answers[q.id] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
            >{{ idx + 1 }}</span>
            <div class="flex-1 min-w-0">
              <p class="text-sm text-slate-700 truncate font-medium">{{ q.question }}</p>
              <p class="text-xs text-slate-400 mt-0.5">
                {{ answers[q.id] ? `Ответ: ${answers[q.id]}` : 'Нет ответа' }}
              </p>
            </div>
            <svg class="w-4 h-4 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </div>

        <div class="px-6 py-5 bg-slate-50/70 border-t border-slate-100 flex gap-3">
          <button @click="phase = 'taking'" class="btn btn-secondary flex-1">← Вернуться</button>
          <button @click="submitTest" :disabled="submitting" class="btn bg-gradient-to-r from-violet-600 to-purple-600 text-white flex-1">
            <svg v-if="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ submitting ? 'Отправка...' : 'Отправить ответы' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ===== RESULTS SCREEN ===== -->
    <div v-else-if="phase === 'results'" class="max-w-2xl mx-auto">
      <!-- Big score card -->
      <div class="card overflow-hidden mb-5">
        <div
          class="px-6 py-10 text-center"
          :class="result.passed ? 'bg-gradient-to-br from-emerald-500 to-teal-600' : 'bg-gradient-to-br from-red-500 to-rose-600'"
        >
          <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-4">
            <svg v-if="result.passed" class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="text-5xl font-black text-white mb-2">{{ result.percentage }}%</div>
          <div class="text-xl font-bold text-white/90">
            {{ result.passed ? 'Тест пройден! 🎉' : 'Попробуйте ещё раз' }}
          </div>
          <p class="text-white/70 text-sm mt-2">
            {{ result.correct_answers }} из {{ result.total_questions }} вопросов правильно
          </p>
        </div>

        <!-- Detailed stats -->
        <div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100">
          <div class="p-4 text-center">
            <div class="text-2xl font-bold text-slate-800">{{ result.score }}</div>
            <div class="text-xs text-slate-500">Набрано баллов</div>
          </div>
          <div class="p-4 text-center">
            <div class="text-2xl font-bold text-slate-800">{{ result.max_score }}</div>
            <div class="text-xs text-slate-500">Максимум</div>
          </div>
          <div class="p-4 text-center">
            <div class="text-2xl font-bold text-slate-800">{{ formatTime(elapsedSeconds) }}</div>
            <div class="text-xs text-slate-500">Время</div>
          </div>
        </div>

        <!-- Pass threshold bar -->
        <div class="p-6">
          <div class="flex justify-between text-xs text-slate-500 mb-2">
            <span>Результат: {{ result.percentage }}%</span>
            <span>Порог: 70%</span>
          </div>
          <div class="h-3 bg-slate-100 rounded-full overflow-hidden relative">
            <div
              class="h-full rounded-full transition-all duration-700"
              :class="result.passed ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : 'bg-gradient-to-r from-red-500 to-rose-500'"
              :style="{ width: `${result.percentage}%` }"
            ></div>
            <!-- Threshold marker -->
            <div class="absolute top-0 bottom-0 w-0.5 bg-slate-400" style="left: 70%"></div>
          </div>
        </div>

        <div class="px-6 pb-6 flex gap-3 flex-wrap">
          <button @click="retakeTest" class="btn btn-secondary flex-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Пройти снова
          </button>
          <router-link
            v-if="test.course_id"
            :to="`/courses/${test.course_id}`"
            class="btn btn-primary flex-1 text-center"
          >
            К курсу →
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const phase = ref('loading') // loading | error | start | taking | review | results
const errorMsg = ref('')
const test = ref(null)
const questions = ref([])
const attemptId = ref(null)
const answers = ref({})
const currentIdx = ref(0)
const starting = ref(false)
const submitting = ref(false)
const result = ref(null)
const pastResults = ref([])
const elapsedSeconds = ref(0)
const timerSeconds = ref(0)
let timerInterval = null
let autoSaveInterval = null
let startTimestamp = null

const currentQuestion = computed(() => questions.value[currentIdx.value] || {})
const answeredCount = computed(() => questions.value.filter(q => answers.value[q.id] !== undefined && answers.value[q.id] !== '').length)
const unansweredCount = computed(() => questions.value.length - answeredCount.value)
const answeredPercent = computed(() => questions.value.length ? Math.round((answeredCount.value / questions.value.length) * 100) : 0)
const bestResult = computed(() => pastResults.value.reduce((max, r) => Math.max(max, r.percentage), 0))
const timerUrgent = computed(() => timerSeconds.value > 0 && timerSeconds.value <= 60)
const timerDisplay = computed(() => {
  const s = timerSeconds.value
  if (s <= 0) return '0:00'
  const m = Math.floor(s / 60)
  const sec = s % 60
  return `${m}:${sec.toString().padStart(2, '0')}`
})
const difficultyLabel = computed(() => ({ easy: 'Лёгкий', medium: 'Средний', hard: 'Сложный' }[test.value?.difficulty] || '—')  )
const difficultyColor = computed(() => ({ easy: 'text-emerald-600', medium: 'text-amber-600', hard: 'text-red-600' }[test.value?.difficulty] || 'text-slate-700'))

const typeLabel = (t) => ({ multiple_choice: 'Один вариант', boolean: 'Да / Нет', text: 'Свободный ответ' }[t] || t)
const plural = (n, one, few, many) => {
  if (n % 10 === 1 && n % 100 !== 11) return one
  if (n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20)) return few
  return many
}

const questionBtnClass = (idx) => {
  const q = questions.value[idx]
  if (idx === currentIdx.value) return 'bg-violet-500 text-white'
  if (answers.value[q?.id] !== undefined && answers.value[q?.id] !== '') return 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
  return 'bg-slate-100 text-slate-500 hover:bg-slate-200'
}

const formatTime = (sec) => {
  const m = Math.floor(sec / 60)
  const s = sec % 60
  return `${m}м ${s}с`
}

const goToQuestion = (idx) => { currentIdx.value = idx }
const prevQuestion = () => { if (currentIdx.value > 0) currentIdx.value-- }
const nextQuestion = () => { if (currentIdx.value < questions.value.length - 1) currentIdx.value++ }
const goToReview = () => { phase.value = 'review' }
const goToQuestionFromReview = (idx) => { currentIdx.value = idx; phase.value = 'taking' }

const startTimer = (durationMinutes) => {
  timerSeconds.value = durationMinutes * 60
  startTimestamp = Date.now()
  timerInterval = setInterval(() => {
    timerSeconds.value--
    elapsedSeconds.value++
    if (timerSeconds.value <= 0) {
      clearInterval(timerInterval)
      submitTest()
    }
  }, 1000)
}

const autoSave = async () => {
  if (!attemptId.value || Object.keys(answers.value).length === 0) return
  try {
    await axios.post(`/api/attempts/${attemptId.value}/draft`, { answers: answers.value })
  } catch { /* silent */ }
}

const startTest = async () => {
  starting.value = true
  try {
    const res = await axios.post(`/api/tests/${test.value.id}/attempts`)
    attemptId.value = res.data.data.attempt_id
    // Restore draft answers if any
    if (res.data.data.answers && Object.keys(res.data.data.answers).length > 0) {
      answers.value = res.data.data.answers
    }
    phase.value = 'taking'
    currentIdx.value = 0
    startTimer(test.value.duration)
    autoSaveInterval = setInterval(autoSave, 15000)
  } catch (e) {
    errorMsg.value = e?.response?.data?.message || 'Не удалось начать тест'
    phase.value = 'error'
  } finally {
    starting.value = false
  }
}

const submitTest = async () => {
  if (submitting.value) return
  submitting.value = true
  clearInterval(timerInterval)
  clearInterval(autoSaveInterval)
  try {
    const timeSpent = startTimestamp ? Math.floor((Date.now() - startTimestamp) / 1000) : elapsedSeconds.value
    const res = await axios.post(`/api/attempts/${attemptId.value}/submit`, {
      answers: answers.value,
      time_spent: timeSpent,
    })
    result.value = res.data.result
    phase.value = 'results'
  } catch (e) {
    errorMsg.value = e?.response?.data?.message || 'Ошибка отправки теста'
    phase.value = 'error'
  } finally {
    submitting.value = false
  }
}

const retakeTest = () => {
  answers.value = {}
  attemptId.value = null
  currentIdx.value = 0
  elapsedSeconds.value = 0
  result.value = null
  clearInterval(timerInterval)
  clearInterval(autoSaveInterval)
  phase.value = 'start'
}

onMounted(async () => {
  try {
    const testId = route.params.id
    const [testRes, resultsRes] = await Promise.all([
      axios.get(`/api/tests/${testId}`),
      axios.get(`/api/tests/${testId}/results`).catch(() => ({ data: { data: [] } })),
    ])
    test.value = testRes.data
    questions.value = testRes.data.questions || []
    pastResults.value = resultsRes.data.data || []
    phase.value = 'start'
  } catch (e) {
    errorMsg.value = e?.response?.data?.message || 'Тест не найден или недоступен'
    phase.value = 'error'
  }
})

onUnmounted(() => {
  clearInterval(timerInterval)
  clearInterval(autoSaveInterval)
})
</script>
