<template>
  <div>
    <div class="mb-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Вопросы теста</h3>
        <div class="text-sm text-gray-500">
          Прогресс: {{ currentQuestionIndex + 1 }} / {{ questions.length }}
        </div>
      </div>
      
      <!-- Прогресс бар -->
      <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
        <div 
          class="bg-blue-500 h-2 rounded-full transition-all duration-300"
          :style="{ width: `${((currentQuestionIndex + 1) / questions.length) * 100}%` }"
        ></div>
      </div>
    </div>

    <div v-if="currentQuestion" class="bg-white rounded-lg shadow-md p-6">
      <div class="mb-6">
        <h4 class="text-xl font-medium mb-4">
          {{ currentQuestion.question }}
        </h4>
        
        <!-- Варианты ответов -->
        <div v-if="currentQuestion.type === 'multiple_choice'" class="space-y-3">
          <label 
            v-for="(option, index) in currentQuestion.options" 
            :key="index"
            class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer"
          >
            <input
              type="radio"
              :name="`question-${currentQuestion.id}`"
              :value="option"
              v-model="answers[currentQuestion.id]"
              class="mr-3"
            />
            <span>{{ option }}</span>
          </label>
        </div>
        
        <!-- Текстовый ответ -->
        <div v-else-if="currentQuestion.type === 'text'">
          <textarea
            v-model="answers[currentQuestion.id]"
            class="w-full p-3 border rounded-lg"
            rows="4"
            placeholder="Введите ваш ответ..."
          ></textarea>
        </div>
        
        <!-- Да/Нет -->
        <div v-else-if="currentQuestion.type === 'boolean'" class="space-y-3">
          <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
            <input
              type="radio"
              :name="`question-${currentQuestion.id}`"
              value="true"
              v-model="answers[currentQuestion.id]"
              class="mr-3"
            />
            <span>Да</span>
          </label>
          <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
            <input
              type="radio"
              :name="`question-${currentQuestion.id}`"
              value="false"
              v-model="answers[currentQuestion.id]"
              class="mr-3"
            />
            <span>Нет</span>
          </label>
        </div>
      </div>

      <!-- Кнопки навигации -->
      <div class="flex justify-between">
        <button
          @click="previousQuestion"
          :disabled="currentQuestionIndex === 0"
          class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 disabled:bg-gray-300"
        >
          Предыдущий
        </button>
        
        <button
          v-if="currentQuestionIndex < questions.length - 1"
          @click="nextQuestion"
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          Следующий
        </button>
        
        <button
          v-else
          @click="submitTest"
          class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
        >
          Завершить тест
        </button>
      </div>
    </div>

    <!-- Обзор ответов перед отправкой -->
    <div v-if="showReview" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-2xl max-h-[80vh] overflow-y-auto">
        <h3 class="text-xl font-semibold mb-4">Проверьте ваши ответы</h3>
        
        <div class="space-y-4 mb-6">
          <div 
            v-for="(question, index) in questions" 
            :key="question.id"
            class="border rounded-lg p-4"
          >
            <div class="font-medium mb-2">{{ index + 1 }}. {{ question.question }}</div>
            <div class="text-sm text-gray-600">
              Ваш ответ: {{ answers[question.id] || 'Не отвечено' }}
            </div>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3">
          <button
            @click="showReview = false"
            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
          >
            Вернуться к тесту
          </button>
          <button
            @click="confirmSubmit"
            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
          >
            Подтвердить отправку
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  test: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['submit'])

const currentQuestionIndex = ref(0)
const answers = ref({})
const showReview = ref(false)

const questions = computed(() => props.test.questions || [])
const currentQuestion = computed(() => questions.value[currentQuestionIndex.value])

const nextQuestion = () => {
  if (currentQuestionIndex.value < questions.value.length - 1) {
    currentQuestionIndex.value++
  }
}

const previousQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--
  }
}

const submitTest = () => {
  showReview.value = true
}

const confirmSubmit = () => {
  emit('submit', answers.value)
}

// Инициализация пустых ответов
questions.value.forEach(question => {
  if (!answers.value[question.id]) {
    answers.value[question.id] = ''
  }
})
</script>
