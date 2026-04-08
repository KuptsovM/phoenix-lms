<template>
  <div class="animate-fade-in">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
      <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
        Управление <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-500 to-purple-500">тестами</span>
      </h1>
      <button @click="openCreateModal" class="btn btn-primary" :disabled="courses.length === 0" :title="courses.length === 0 ? 'Сначала создайте курс' : ''">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Создать тест
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
          <input v-model="filters.search" type="text" placeholder="Поиск тестов..." class="input-field" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Сложность</label>
          <select v-model="filters.difficulty" class="input-field">
            <option value="">Все сложности</option>
            <option value="easy">Легкий</option>
            <option value="medium">Средний</option>
            <option value="hard">Сложный</option>
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
    <div v-else-if="filteredTests.length === 0" class="text-center py-16">
      <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <p class="text-slate-500 text-lg mb-4">Нет тестов для выбранного курса</p>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="(test, index) in filteredTests"
        :key="test.id"
        class="card animate-slide-up"
        :style="{ animationDelay: `${index * 0.05}s` }"
      >
        <!-- Заголовок теста -->
        <div class="p-5">
          <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-3 mb-2">
                <h3 class="text-lg font-bold text-slate-800">{{ test.title }}</h3>
                <span :class="getDifficultyClass(test.difficulty)" class="badge">{{ getDifficultyText(test.difficulty) }}</span>
                <span :class="test.status === 'published' ? 'badge-success' : 'badge-warning'" class="badge text-xs">
                  {{ test.status === 'published' ? 'Опубликован' : 'Черновик' }}
                </span>
              </div>
              <p class="text-slate-500 text-sm mb-3 line-clamp-2">{{ test.description }}</p>
              <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                <span class="inline-flex items-center gap-1.5">
                  <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ test.questions_count }} вопросов
                </span>
                <span class="inline-flex items-center gap-1.5">
                  <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ test.duration }} мин
                </span>
              </div>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
              <button @click="toggleQuestions(test)" class="btn text-sm py-2 gap-2" :class="expandedTestId === test.id ? 'bg-violet-50 text-violet-700 border-violet-200' : 'btn-secondary'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Вопросы
                <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': expandedTestId === test.id }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <button @click="openEditModal(test)" class="btn btn-secondary text-sm py-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Изменить
              </button>
              <button @click="openDeleteModal(test)" class="btn bg-red-50 text-red-600 border-red-200 hover:bg-red-100 hover:border-red-300 text-sm py-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Удалить
              </button>
            </div>
          </div>
        </div>

        <!-- Панель вопросов (раскрывается) -->
        <div v-if="expandedTestId === test.id" class="border-t border-slate-100">
          <div class="p-5 bg-slate-50/50">
            <div class="flex items-center justify-between mb-4">
              <h4 class="font-semibold text-slate-700 flex items-center gap-2">
                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Вопросы ({{ questionsForTest.length }})
              </h4>
              <button @click="openAddQuestionModal(test)" class="btn btn-primary text-sm py-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Добавить вопрос
              </button>
            </div>

            <div v-if="questionsLoading" class="text-center py-8 text-slate-500 text-sm">
              <svg class="animate-spin h-5 w-5 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              Загрузка вопросов...
            </div>

            <div v-else-if="questionsForTest.length === 0" class="text-center py-8">
              <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-violet-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <p class="text-slate-500 text-sm">Вопросов ещё нет. Добавьте первый!</p>
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="(question, qIdx) in questionsForTest"
                :key="question.id"
                class="bg-white rounded-xl border border-slate-200 p-4"
              >
                <div class="flex items-start gap-3">
                  <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center text-xs font-bold">
                    {{ qIdx + 1 }}
                  </span>
                  <div class="flex-1 min-w-0">
                    <p class="text-slate-800 font-medium text-sm mb-1.5">{{ question.question }}</p>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                      <span class="inline-flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded-full">
                        {{ getTypeLabel(question.type) }}
                      </span>
                      <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full">
                        {{ question.points }} {{ pluralPoints(question.points) }}
                      </span>
                      <span v-if="question.type === 'multiple_choice'" class="text-slate-400">
                        {{ question.options?.length }} вариантов
                      </span>
                    </div>
                    <!-- Варианты ответов для multiple_choice -->
                    <div v-if="question.type === 'multiple_choice' && question.options?.length" class="mt-2 flex flex-wrap gap-1.5">
                      <span
                        v-for="(opt, optIdx) in question.options"
                        :key="optIdx"
                        class="text-xs px-2 py-1 rounded-lg"
                        :class="opt === question.correct_answer ? 'bg-emerald-100 text-emerald-700 font-medium ring-1 ring-emerald-300' : 'bg-slate-100 text-slate-600'"
                      >
                        {{ opt }}
                        <svg v-if="opt === question.correct_answer" class="inline w-3 h-3 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                      </span>
                    </div>
                    <!-- Правильный ответ для boolean/text -->
                    <div v-else-if="question.type !== 'multiple_choice'" class="mt-1.5 text-xs">
                      <span class="text-slate-400">Правильный ответ: </span>
                      <span class="text-emerald-600 font-medium">
                        {{ question.type === 'boolean' ? (question.correct_answer === 'true' ? 'Да (верно)' : 'Нет (неверно)') : question.correct_answer }}
                      </span>
                    </div>
                  </div>
                  <div class="flex gap-1.5 flex-shrink-0">
                    <button @click="openEditQuestionModal(question, test)" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition-colors" title="Редактировать">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button @click="openDeleteQuestionModal(question, test)" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors" title="Удалить">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== МОДАЛКА СОЗДАНИЯ/РЕДАКТИРОВАНИЯ ТЕСТА ===== -->
    <Teleport to="body">
      <div v-if="showFormModal" class="modal-overlay" @click.self="closeFormModal">
        <div class="modal-box">
          <div class="modal-header">
            <h2 class="text-xl font-bold text-slate-800">{{ editingTest ? 'Редактировать тест' : 'Создать тест' }}</h2>
            <button @click="closeFormModal" class="modal-close-btn">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <form @submit.prevent="submitTestForm" class="modal-body space-y-5">
            <div v-if="formError" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">{{ formError }}</div>
            <div>
              <label class="form-label">Название <span class="text-red-500">*</span></label>
              <input v-model="testForm.title" type="text" class="input-field" placeholder="Введите название теста" required autofocus />
            </div>
            <div>
              <label class="form-label">Описание</label>
              <textarea v-model="testForm.description" class="input-field resize-none" rows="3" placeholder="Краткое описание теста"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="form-label">Курс <span class="text-red-500">*</span></label>
                <select v-model="testForm.course_id" class="input-field" required>
                  <option value="">Выберите курс</option>
                  <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
                </select>
              </div>
              <div>
                <label class="form-label">Сложность</label>
                <select v-model="testForm.difficulty" class="input-field">
                  <option value="easy">Легкий</option>
                  <option value="medium">Средний</option>
                  <option value="hard">Сложный</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="form-label">Длительность (мин) <span class="text-red-500">*</span></label>
                <input v-model.number="testForm.duration" type="number" min="1" max="300" class="input-field" required />
              </div>
              <div>
                <label class="form-label">Статус</label>
                <select v-model="testForm.status" class="input-field">
                  <option value="draft">Черновик</option>
                  <option value="published">Опубликован</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" @click="closeFormModal" class="btn btn-secondary">Отмена</button>
              <button type="submit" class="btn btn-primary" :disabled="formLoading">
                <svg v-if="formLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ editingTest ? 'Сохранить' : 'Создать' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- ===== МОДАЛКА СОЗДАНИЯ/РЕДАКТИРОВАНИЯ ВОПРОСА ===== -->
    <Teleport to="body">
      <div v-if="showQuestionModal" class="modal-overlay" @click.self="closeQuestionModal">
        <div class="modal-box modal-wide-md">
          <div class="modal-header">
            <h2 class="text-xl font-bold text-slate-800">{{ editingQuestion ? 'Редактировать вопрос' : 'Добавить вопрос' }}</h2>
            <button @click="closeQuestionModal" class="modal-close-btn">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <form @submit.prevent="submitQuestion" class="modal-body space-y-5">
            <div v-if="questionError" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">{{ questionError }}</div>

            <div>
              <label class="form-label">Текст вопроса <span class="text-red-500">*</span></label>
              <textarea v-model="questionForm.question" class="input-field resize-none" rows="3" placeholder="Введите вопрос..." required autofocus></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="form-label">Тип вопроса <span class="text-red-500">*</span></label>
                <select v-model="questionForm.type" class="input-field" :disabled="!!editingQuestion">
                  <option value="multiple_choice">С вариантами ответа</option>
                  <option value="boolean">Да / Нет</option>
                  <option value="text">Текстовый ответ</option>
                </select>
                <p v-if="editingQuestion" class="text-xs text-slate-400 mt-1">Тип нельзя менять при редактировании</p>
              </div>
              <div>
                <label class="form-label">Баллы</label>
                <input v-model.number="questionForm.points" type="number" min="1" max="100" class="input-field" />
              </div>
            </div>

            <!-- Multiple choice options -->
            <div v-if="questionForm.type === 'multiple_choice'">
              <div class="flex items-center justify-between mb-2">
                <label class="form-label mb-0">Варианты ответов <span class="text-red-500">*</span></label>
                <button type="button" @click="addOption" class="text-sm text-violet-600 hover:text-violet-800 font-medium flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  Добавить вариант
                </button>
              </div>
              <div class="space-y-2">
                <div
                  v-for="(opt, idx) in questionForm.options"
                  :key="idx"
                  class="flex items-center gap-2 p-2 rounded-xl border-2 transition-colors"
                  :class="questionForm.correct_index === idx ? 'border-emerald-400 bg-emerald-50/50' : 'border-transparent'"
                >
                  <!-- Correct answer selector (by index) -->
                  <button
                    type="button"
                    @click="questionForm.correct_index = idx"
                    class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                    :class="questionForm.correct_index === idx
                      ? 'border-emerald-500 bg-emerald-500'
                      : 'border-slate-300 hover:border-emerald-400'"
                    title="Отметить как правильный"
                  >
                    <span v-if="questionForm.correct_index === idx" class="w-2 h-2 rounded-full bg-white"></span>
                  </button>
                  <input
                    v-model="questionForm.options[idx]"
                    type="text"
                    class="input-field flex-1 py-2 !min-h-0"
                    :placeholder="`Вариант ${idx + 1}`"
                  />
                  <button
                    type="button"
                    @click="removeOption(idx)"
                    class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-colors flex-shrink-0"
                    :disabled="questionForm.options.length <= 2"
                    :class="{ 'opacity-30 cursor-not-allowed': questionForm.options.length <= 2 }"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
              <p class="text-xs text-slate-400 mt-2">Нажмите на кружок слева от варианта, чтобы выбрать правильный ответ</p>
            </div>

            <!-- Boolean correct answer -->
            <div v-else-if="questionForm.type === 'boolean'">
              <label class="form-label">Правильный ответ <span class="text-red-500">*</span></label>
              <div class="flex gap-3">
                <label class="flex items-center gap-2 px-4 py-2.5 border rounded-xl cursor-pointer transition-all" :class="questionForm.correct_answer === 'true' ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-slate-200 hover:bg-slate-50'">
                  <input type="radio" v-model="questionForm.correct_answer" value="true" class="sr-only" />
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Да (верно)
                </label>
                <label class="flex items-center gap-2 px-4 py-2.5 border rounded-xl cursor-pointer transition-all" :class="questionForm.correct_answer === 'false' ? 'border-red-400 bg-red-50 text-red-700' : 'border-slate-200 hover:bg-slate-50'">
                  <input type="radio" v-model="questionForm.correct_answer" value="false" class="sr-only" />
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  Нет (неверно)
                </label>
              </div>
            </div>

            <!-- Text correct answer -->
            <div v-else-if="questionForm.type === 'text'">
              <label class="form-label">Правильный ответ <span class="text-red-500">*</span></label>
              <input v-model="questionForm.correct_answer" type="text" class="input-field" placeholder="Точный ответ (без учёта регистра)" />
              <p class="text-xs text-slate-400 mt-1">Ответ студента будет сравниваться без учёта регистра и лишних пробелов</p>
            </div>

            <div class="modal-footer">
              <button type="button" @click="closeQuestionModal" class="btn btn-secondary">Отмена</button>
              <button type="submit" class="btn btn-primary" :disabled="questionLoading">
                <svg v-if="questionLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ editingQuestion ? 'Сохранить' : 'Добавить вопрос' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- ===== МОДАЛКА УДАЛЕНИЯ ВОПРОСА ===== -->
    <Teleport to="body">
      <div v-if="showDeleteQuestionModal" class="modal-overlay" @click.self="showDeleteQuestionModal = false">
        <div class="modal-box max-w-md">
          <div class="modal-header">
            <h2 class="text-xl font-bold text-slate-800">Удалить вопрос</h2>
            <button @click="showDeleteQuestionModal = false" class="modal-close-btn">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="modal-body">
            <div class="flex items-start gap-4">
              <div class="w-11 h-11 flex-shrink-0 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </div>
              <div>
                <p class="text-slate-700 font-medium mb-1">Удалить этот вопрос?</p>
                <p class="text-slate-500 text-sm line-clamp-3">{{ deletingQuestion?.question }}</p>
                <p class="text-slate-400 text-xs mt-2">Это действие необратимо.</p>
              </div>
            </div>
            <div class="modal-footer mt-5">
              <button @click="showDeleteQuestionModal = false" class="btn btn-secondary">Отмена</button>
              <button @click="confirmDeleteQuestion" :disabled="deleteQuestionLoading" class="btn bg-red-600 text-white hover:bg-red-700 border-red-600">
                <svg v-if="deleteQuestionLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
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

    <!-- ===== МОДАЛКА УДАЛЕНИЯ ТЕСТА ===== -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
        <div class="modal-box max-w-md">
          <div class="modal-header">
            <h2 class="text-xl font-bold text-slate-800">Удалить тест</h2>
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
                <p class="text-slate-700 font-medium">Удалить тест и все его вопросы?</p>
                <p class="text-slate-500 text-sm mt-1">«{{ deletingTest?.title }}»</p>
                <p class="text-slate-400 text-sm mt-2">Все вопросы и результаты прохождения будут удалены. Необратимо.</p>
              </div>
            </div>
            <div class="modal-footer mt-6">
              <button @click="showDeleteModal = false" class="btn btn-secondary">Отмена</button>
              <button @click="confirmDeleteTest" class="btn bg-red-600 text-white hover:bg-red-700 border-red-600" :disabled="formLoading">
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
import { useCourseStore } from '../../stores/courses'

const route = useRoute()
const courseStore = useCourseStore()

const loading = ref(true)
const errorMessage = ref('')
const filters = ref({ course: route.query.course ? String(route.query.course) : '', search: '', difficulty: '' })

const courses = computed(() => courseStore.courses)
const tests = computed(() => courseStore.tests)

const filteredTests = computed(() => {
  return tests.value.filter(test => {
    if (filters.value.course && Number(test.course_id) !== Number(filters.value.course)) return false
    if (filters.value.difficulty && test.difficulty !== filters.value.difficulty) return false
    if (filters.value.search && !test.title.toLowerCase().includes(filters.value.search.toLowerCase())) return false
    return true
  })
})

const getDifficultyClass = (d) => ({ easy: 'badge-success', medium: 'badge-warning', hard: 'bg-red-100 text-red-700' }[d] || 'bg-gray-100 text-gray-800')
const getDifficultyText = (d) => ({ easy: 'Легкий', medium: 'Средний', hard: 'Сложный' }[d] || d)
const getTypeLabel = (t) => ({ multiple_choice: 'Варианты', boolean: 'Да/Нет', text: 'Текст' }[t] || t)
const pluralPoints = (n) => n === 1 ? 'балл' : n >= 2 && n <= 4 ? 'балла' : 'баллов'

onMounted(async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    await courseStore.fetchCourses()
    if (!filters.value.course && courseStore.courses.length > 0) {
      filters.value.course = String(courseStore.courses[0].id)
    }
    if (filters.value.course) {
      await courseStore.fetchTests(filters.value.course)
    }
  } catch (error) {
    errorMessage.value = courseStore.error || 'Не удалось загрузить курсы и тесты'
  } finally {
    loading.value = false
  }
})

watch(() => filters.value.course, async (newCourseId) => {
  errorMessage.value = ''
  expandedTestId.value = null
  if (newCourseId) {
    loading.value = true
    try {
      await courseStore.fetchTests(newCourseId)
    } catch (error) {
      errorMessage.value = courseStore.error || 'Не удалось загрузить тесты'
    } finally {
      loading.value = false
    }
  } else {
    courseStore.tests = []
  }
})

// ===== ТЕСТ ФОРМА =====
const showFormModal = ref(false)
const editingTest = ref(null)
const formLoading = ref(false)
const formError = ref('')
const showDeleteModal = ref(false)
const deletingTest = ref(null)

const emptyTestForm = () => ({
  title: '',
  description: '',
  course_id: filters.value.course || (courses.value[0]?.id ?? ''),
  difficulty: 'medium',
  duration: 30,
  status: 'draft'
})

const testForm = ref(emptyTestForm())

const openCreateModal = () => {
  editingTest.value = null
  testForm.value = emptyTestForm()
  formError.value = ''
  showFormModal.value = true
}

const openEditModal = (test) => {
  editingTest.value = test
  testForm.value = {
    title: test.title,
    description: test.description || '',
    course_id: test.course_id,
    difficulty: test.difficulty || 'medium',
    duration: test.duration || 30,
    status: test.status || 'draft'
  }
  formError.value = ''
  showFormModal.value = true
}

const closeFormModal = () => { showFormModal.value = false; editingTest.value = null }

const openDeleteModal = (test) => { deletingTest.value = test; showDeleteModal.value = true }

const submitTestForm = async () => {
  formError.value = ''
  if (!testForm.value.title.trim()) { formError.value = 'Введите название теста'; return }
  if (!testForm.value.course_id) { formError.value = 'Выберите курс'; return }
  formLoading.value = true
  try {
    if (editingTest.value) {
      await courseStore.updateTest(editingTest.value.id, {
        title: testForm.value.title.trim(),
        description: testForm.value.description,
        difficulty: testForm.value.difficulty,
        duration: testForm.value.duration,
        status: testForm.value.status
      })
    } else {
      await courseStore.createTest(testForm.value.course_id, {
        title: testForm.value.title.trim(),
        description: testForm.value.description,
        difficulty: testForm.value.difficulty,
        duration: testForm.value.duration,
        status: testForm.value.status
      })
    }
    closeFormModal()
  } catch (e) {
    formError.value = e?.response?.data?.message || (editingTest.value ? 'Ошибка при обновлении' : 'Ошибка при создании')
  } finally {
    formLoading.value = false
  }
}

const confirmDeleteTest = async () => {
  if (!deletingTest.value) return
  formLoading.value = true
  try {
    await courseStore.deleteTest(deletingTest.value.id)
    if (expandedTestId.value === deletingTest.value.id) expandedTestId.value = null
    showDeleteModal.value = false
    deletingTest.value = null
  } catch (e) {
    showDeleteModal.value = false
  } finally {
    formLoading.value = false
  }
}

// ===== ВОПРОСЫ =====
const expandedTestId = ref(null)
const questionsMap = ref({})   // testId -> question[]
const questionsLoading = ref(false)

const questionsForTest = computed(() => {
  if (!expandedTestId.value) return []
  return questionsMap.value[expandedTestId.value] || []
})

const toggleQuestions = async (test) => {
  if (expandedTestId.value === test.id) {
    expandedTestId.value = null
    return
  }
  expandedTestId.value = test.id
  if (!questionsMap.value[test.id]) {
    await loadQuestions(test)
  }
}

const loadQuestions = async (test) => {
  questionsLoading.value = true
  try {
    const fullTest = await courseStore.fetchTest(test.id)
    questionsMap.value[test.id] = fullTest.questions || courseStore.questions
  } catch (e) {
    questionsMap.value[test.id] = []
  } finally {
    questionsLoading.value = false
  }
}

// ===== ФОРМА ВОПРОСА =====
const showQuestionModal = ref(false)
const editingQuestion = ref(null)
const questionForTest = ref(null)
const questionLoading = ref(false)
const questionError = ref('')

const emptyQuestionForm = () => ({
  question: '',
  type: 'multiple_choice',
  points: 1,
  options: ['', '', '', ''],
  correct_index: null,  // index of correct option for multiple_choice
  correct_answer: ''    // for boolean and text types
})

const questionForm = ref(emptyQuestionForm())

const openAddQuestionModal = (test) => {
  questionForTest.value = test
  editingQuestion.value = null
  questionForm.value = emptyQuestionForm()
  questionError.value = ''
  showQuestionModal.value = true
}

const openEditQuestionModal = (question, test) => {
  questionForTest.value = test
  editingQuestion.value = question
  const opts = question.options ? [...question.options] : ['', '']
  const correctIdx = question.type === 'multiple_choice'
    ? opts.indexOf(question.correct_answer ?? '')
    : null
  questionForm.value = {
    question: question.question,
    type: question.type,
    points: question.points || 1,
    options: opts,
    correct_index: correctIdx >= 0 ? correctIdx : null,
    correct_answer: question.type !== 'multiple_choice' ? (question.correct_answer || '') : ''
  }
  questionError.value = ''
  showQuestionModal.value = true
}

const closeQuestionModal = () => { showQuestionModal.value = false; editingQuestion.value = null }

const addOption = () => { questionForm.value.options.push('') }
const removeOption = (idx) => {
  if (questionForm.value.options.length <= 2) return
  questionForm.value.options.splice(idx, 1)
  // Adjust correct_index after removal
  if (questionForm.value.correct_index === idx) {
    questionForm.value.correct_index = null
  } else if (questionForm.value.correct_index > idx) {
    questionForm.value.correct_index--
  }
}

const submitQuestion = async () => {
  questionError.value = ''

  if (!questionForm.value.question.trim()) {
    questionError.value = 'Введите текст вопроса'; return
  }

  let correctAnswer = ''
  const filledOptions = questionForm.value.options.map(o => o.trim()).filter(Boolean)

  if (questionForm.value.type === 'multiple_choice') {
    if (filledOptions.length < 2) {
      questionError.value = 'Добавьте минимум 2 варианта ответа'; return
    }
    if (questionForm.value.correct_index === null || questionForm.value.correct_index === undefined) {
      questionError.value = 'Выберите правильный вариант ответа'; return
    }
    const selectedOption = questionForm.value.options[questionForm.value.correct_index]?.trim()
    if (!selectedOption) {
      questionError.value = 'Выбранный правильный вариант пустой — заполните его'; return
    }
    correctAnswer = selectedOption
  } else {
    if (!questionForm.value.correct_answer.toString().trim()) {
      questionError.value = 'Укажите правильный ответ'; return
    }
    correctAnswer = questionForm.value.correct_answer
  }

  questionLoading.value = true
  const testId = questionForTest.value.id
  try {
    const payload = {
      question: questionForm.value.question.trim(),
      type: questionForm.value.type,
      points: questionForm.value.points,
      correct_answer: correctAnswer,
      ...(questionForm.value.type === 'multiple_choice' && {
        options: filledOptions
      })
    }

    if (editingQuestion.value) {
      const updated = await courseStore.updateQuestion(editingQuestion.value.id, payload)
      const list = questionsMap.value[testId] || []
      const idx = list.findIndex(q => q.id === editingQuestion.value.id)
      if (idx !== -1) list[idx] = { ...list[idx], ...updated }
    } else {
      const created = await courseStore.addQuestion(testId, payload)
      if (!questionsMap.value[testId]) questionsMap.value[testId] = []
      questionsMap.value[testId].push(created)
      // Update question count in test list
      const t = tests.value.find(t => t.id === testId)
      if (t) t.questions_count = (t.questions_count || 0) + 1
    }
    closeQuestionModal()
  } catch (e) {
    questionError.value = e?.response?.data?.message || 'Ошибка при сохранении вопроса'
  } finally {
    questionLoading.value = false
  }
}

// Delete question modal
const showDeleteQuestionModal = ref(false)
const deletingQuestion = ref(null)
const deletingQuestionTest = ref(null)
const deleteQuestionLoading = ref(false)

const openDeleteQuestionModal = (question, test) => {
  deletingQuestion.value = question
  deletingQuestionTest.value = test
  showDeleteQuestionModal.value = true
}

const confirmDeleteQuestion = async () => {
  if (!deletingQuestion.value) return
  deleteQuestionLoading.value = true
  try {
    await courseStore.deleteQuestion(deletingQuestion.value.id)
    const list = questionsMap.value[deletingQuestionTest.value.id]
    if (list) {
      const idx = list.findIndex(q => q.id === deletingQuestion.value.id)
      if (idx !== -1) list.splice(idx, 1)
    }
    const t = tests.value.find(t => t.id === deletingQuestionTest.value.id)
    if (t) t.questions_count = Math.max(0, (t.questions_count || 1) - 1)
    showDeleteQuestionModal.value = false
  } catch {
    // silent
  } finally {
    deleteQuestionLoading.value = false
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
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
  max-height: calc(100vh - 2rem);
  overflow-y: auto;
}
.modal-wide-md { max-width: 640px; }
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
