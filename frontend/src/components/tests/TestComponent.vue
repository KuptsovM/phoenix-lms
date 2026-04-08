<template>
  <div>
    <!-- Прогресс и навигация по вопросам -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-3">
        <span class="text-sm text-slate-500 font-medium">Вопрос {{ currentQuestionIndex + 1 }} из {{ questions.length }}</span>
        <span class="text-sm text-slate-500">
          Отвечено: {{ answeredCount }} / {{ questions.length }}
        </span>
      </div>
      <div class="w-full bg-slate-100 rounded-full h-2 mb-4">
        <div
          class="h-2 rounded-full transition-all duration-500"
          :class="progress === 100 ? 'bg-emerald-500' : 'bg-violet-500'"
          :style="{ width: `${progress}%` }"
        ></div>
      </div>

      <!-- Быстрая навигация по вопросам (точки) -->
      <div class="flex flex-wrap gap-1.5">
        <button
          v-for="(q, idx) in questions"
          :key="q.id"
          @click="currentQuestionIndex = idx"
          class="w-8 h-8 rounded-lg text-xs font-semibold transition-all"
          :class="getQuestionDotClass(idx)"
          :title="`Вопрос ${idx + 1}`"
        >
          {{ idx + 1 }}
        </button>
      </div>
    </div>

    <!-- Карточка вопроса -->
    <div v-if="currentQuestion" class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
      <!-- Заголовок вопроса -->
      <div class="p-6 border-b border-slate-100">
        <div class="flex items-start gap-3">
          <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center text-sm font-bold">
            {{ currentQuestionIndex + 1 }}
          </span>
          <div class="flex-1">
            <p class="text-slate-800 font-semibold text-base leading-relaxed">{{ currentQuestion.question }}</p>
            <div class="flex items-center gap-2 mt-2">
              <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">{{ getTypeLabel(currentQuestion.type) }}</span>
              <span class="text-xs bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full">{{ currentQuestion.points }} {{ pluralPoints(currentQuestion.points) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Варианты ответов -->
      <div class="p-6 space-y-3">
        <!-- Multiple choice -->
        <template v-if="currentQuestion.type === 'multiple_choice'">
          <label
            v-for="(option, optIdx) in currentQuestion.options"
            :key="optIdx"
            class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
            :class="answers[currentQuestion.id] === option
              ? 'border-violet-400 bg-violet-50'
              : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'"
          >
            <div
              class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
              :class="answers[currentQuestion.id] === option ? 'border-violet-500 bg-violet-500' : 'border-slate-300'"
            >
              <div v-if="answers[currentQuestion.id] === option" class="w-2 h-2 rounded-full bg-white"></div>
            </div>
            <input
              type="radio"
              :name="`q-${currentQuestion.id}`"
              :value="option"
              v-model="answers[currentQuestion.id]"
              class="sr-only"
            />
            <span class="text-slate-700">{{ option }}</span>
          </label>
        </template>

        <!-- Boolean -->
        <template v-else-if="currentQuestion.type === 'boolean'">
          <label
            class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
            :class="answers[currentQuestion.id] === 'true' ? 'border-emerald-400 bg-emerald-50' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'"
          >
            <div class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all" :class="answers[currentQuestion.id] === 'true' ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300'">
              <div v-if="answers[currentQuestion.id] === 'true'" class="w-2 h-2 rounded-full bg-white"></div>
            </div>
            <input type="radio" :name="`q-${currentQuestion.id}`" value="true" v-model="answers[currentQuestion.id]" class="sr-only" />
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-slate-700 font-medium">Да — утверждение верно</span>
          </label>
          <label
            class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
            :class="answers[currentQuestion.id] === 'false' ? 'border-red-400 bg-red-50' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'"
          >
            <div class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all" :class="answers[currentQuestion.id] === 'false' ? 'border-red-500 bg-red-500' : 'border-slate-300'">
              <div v-if="answers[currentQuestion.id] === 'false'" class="w-2 h-2 rounded-full bg-white"></div>
            </div>
            <input type="radio" :name="`q-${currentQuestion.id}`" value="false" v-model="answers[currentQuestion.id]" class="sr-only" />
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span class="text-slate-700 font-medium">Нет — утверждение неверно</span>
          </label>
        </template>

        <!-- Text -->
        <template v-else-if="currentQuestion.type === 'text'">
          <textarea
            v-model="answers[currentQuestion.id]"
            class="w-full p-4 border-2 border-slate-200 rounded-xl focus:border-violet-400 focus:outline-none resize-none transition-colors text-slate-700"
            rows="4"
            placeholder="Введите ваш ответ..."
          ></textarea>
        </template>
      </div>

      <!-- Навигация внутри карточки -->
      <div class="px-6 pb-6 flex items-center justify-between gap-3">
        <button
          @click="prevQuestion"
          :disabled="currentQuestionIndex === 0"
          class="btn btn-secondary py-2 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Назад
        </button>

        <div class="flex gap-2">
          <button
            v-if="currentQuestionIndex < questions.length - 1"
            @click="nextQuestion"
            class="btn btn-primary py-2"
          >
            Вперёд
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>

          <button
            v-else
            @click="showReview = true"
            class="btn bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700 py-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Завершить тест
          </button>
        </div>
      </div>
    </div>

    <!-- Модалка обзора ответов перед отправкой -->
    <Teleport to="body">
      <div v-if="showReview" class="modal-overlay" @click.self="showReview = false">
        <div class="modal-box">
          <div class="modal-header">
            <h3 class="text-xl font-bold text-slate-800">Проверьте ответы</h3>
            <button @click="showReview = false" class="modal-close-btn">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="modal-body">
            <div v-if="unansweredCount > 0" class="mb-4 bg-amber-50 border border-amber-200 rounded-xl p-3 text-amber-700 text-sm flex items-center gap-2">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              {{ unansweredCount }} вопросов без ответа
            </div>

            <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
              <div
                v-for="(question, idx) in questions"
                :key="question.id"
                class="flex items-start gap-3 p-3 rounded-xl border"
                :class="answers[question.id] ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-slate-50'"
              >
                <span class="flex-shrink-0 w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold" :class="answers[question.id] ? 'bg-emerald-200 text-emerald-700' : 'bg-slate-200 text-slate-500'">
                  {{ idx + 1 }}
                </span>
                <div class="flex-1 min-w-0">
                  <p class="text-slate-700 text-sm font-medium line-clamp-2">{{ question.question }}</p>
                  <p class="text-xs mt-0.5" :class="answers[question.id] ? 'text-emerald-600' : 'text-slate-400'">
                    {{ answers[question.id] ? `Ответ: ${formatAnswer(question, answers[question.id])}` : 'Без ответа' }}
                  </p>
                </div>
                <button @click="goToQuestion(idx)" class="flex-shrink-0 text-xs text-violet-600 hover:text-violet-800 font-medium">
                  Изменить
                </button>
              </div>
            </div>

            <div class="modal-footer mt-5">
              <button @click="showReview = false" class="btn btn-secondary">Вернуться</button>
              <button @click="confirmSubmit" class="btn bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700">
                Отправить тест
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  test: { type: Object, required: true },
  initialAnswers: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['submit', 'answers-change'])

const currentQuestionIndex = ref(0)
const answers = ref({})
const showReview = ref(false)

const questions = computed(() => props.test.questions || [])
const currentQuestion = computed(() => questions.value[currentQuestionIndex.value])

const answeredCount = computed(() => {
  return questions.value.filter(q => {
    const a = answers.value[q.id]
    return a !== undefined && a !== null && a !== ''
  }).length
})

const unansweredCount = computed(() => questions.value.length - answeredCount.value)

const progress = computed(() => {
  if (!questions.value.length) return 0
  return Math.round((answeredCount.value / questions.value.length) * 100)
})

const getTypeLabel = (t) => ({ multiple_choice: 'Варианты', boolean: 'Да / Нет', text: 'Текстовый' }[t] || t)
const pluralPoints = (n) => n === 1 ? 'балл' : n >= 2 && n <= 4 ? 'балла' : 'баллов'

const formatAnswer = (question, answer) => {
  if (question.type === 'boolean') return answer === 'true' ? 'Да' : 'Нет'
  return answer
}

const getQuestionDotClass = (idx) => {
  const q = questions.value[idx]
  const isAnswered = answers.value[q?.id] !== undefined && answers.value[q?.id] !== null && answers.value[q?.id] !== ''
  const isCurrent = idx === currentQuestionIndex.value
  if (isCurrent) return 'bg-violet-600 text-white ring-2 ring-violet-300'
  if (isAnswered) return 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
  return 'bg-slate-100 text-slate-500 hover:bg-slate-200'
}

const nextQuestion = () => {
  if (currentQuestionIndex.value < questions.value.length - 1) currentQuestionIndex.value++
}
const prevQuestion = () => {
  if (currentQuestionIndex.value > 0) currentQuestionIndex.value--
}
const goToQuestion = (idx) => {
  currentQuestionIndex.value = idx
  showReview.value = false
}

const confirmSubmit = () => {
  showReview.value = false
  emit('submit', answers.value)
}

watch(
  () => [questions.value, props.initialAnswers],
  () => {
    const initial = props.initialAnswers || {}
    questions.value.forEach(question => {
      if (Object.prototype.hasOwnProperty.call(initial, question.id)) {
        answers.value[question.id] = initial[question.id]
      } else if (!Object.prototype.hasOwnProperty.call(answers.value, question.id)) {
        answers.value[question.id] = ''
      }
    })
  },
  { immediate: true, deep: true }
)

watch(
  () => answers.value,
  (value) => { emit('answers-change', { ...value }) },
  { deep: true }
)
</script>

<style scoped>
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
  max-width: 580px;
  animation: slideUp 0.2s ease;
  max-height: calc(100vh - 2rem);
  overflow-y: auto;
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  position: sticky;
  top: 0;
  background: white;
  z-index: 1;
}
.modal-body { padding: 1.5rem; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; }
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
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
</style>
