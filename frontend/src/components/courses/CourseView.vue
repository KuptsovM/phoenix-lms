<template>
  <div class="animate-fade-in">
    <!-- Loading -->
    <div v-if="loading" class="text-center py-16">
      <div class="inline-flex items-center gap-3 text-slate-500">
        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
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
      <!-- Course Header -->
      <div class="card card-elevated p-6 md:p-8 mb-6 animate-slide-up">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-4">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-3 flex-wrap">
              <span :class="getStatusClass(course.status)" class="badge">{{ getStatusText(course.status) }}</span>
              <span v-if="isEnrolled" class="badge badge-success flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4" /></svg>
                Записан
              </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 mb-3">{{ course.title }}</h1>
            <p class="text-slate-500 text-lg leading-relaxed">{{ course.description }}</p>
          </div>

          <!-- Enroll / Unenroll button (student only) -->
          <div v-if="!authStore.isTeacher" class="shrink-0">
            <button
              v-if="!isEnrolled"
              class="btn btn-primary gap-2"
              :disabled="enrolling"
              @click="enrollNow"
            >
              <svg v-if="enrolling" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
              {{ enrolling ? 'Запись...' : 'Записаться на курс' }}
            </button>
            <button v-else class="btn btn-secondary text-sm gap-1 text-slate-500" @click="unenrollConfirm = true">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
              Отписаться
            </button>
          </div>
        </div>

        <!-- Course progress bar (enrolled) -->
        <div v-if="isEnrolled" class="mt-2 mb-4">
          <div class="flex justify-between text-sm text-slate-500 mb-1.5">
            <span class="font-medium">Прогресс курса</span>
            <span class="font-bold" :class="courseProgress >= 100 ? 'text-emerald-600' : 'text-sky-600'">
              {{ courseProgress }}%
            </span>
          </div>
          <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
            <div
              class="h-full rounded-full transition-all duration-700"
              :class="courseProgress >= 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-sky-500 to-cyan-400'"
              :style="{ width: courseProgress + '%' }"
            ></div>
          </div>
          <div class="mt-1.5 text-xs text-slate-400">
            {{ viewedCount }} из {{ lectures.length }} лекций прочитано
          </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-6 pt-4 border-t border-slate-100 text-sm text-slate-500">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-400 to-cyan-500 flex items-center justify-center text-white text-sm font-bold">
              {{ course.author?.name?.charAt(0).toUpperCase() || 'A' }}
            </div>
            <span>{{ course.author?.name || 'Автор' }}</span>
          </div>
          <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            {{ lectures.length }} лекций
          </span>
          <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ tests.length }} тестов
          </span>
        </div>
      </div>

      <!-- ENROLLMENT GATE (not enrolled, student only) -->
      <div v-if="!isEnrolled && !authStore.isTeacher" class="enrollment-gate">
        <div class="enrollment-gate-inner">
          <div class="enrollment-gate-icon">🔒</div>
          <h2 class="enrollment-gate-title">Запишитесь, чтобы начать обучение</h2>
          <p class="enrollment-gate-text">
            Этот курс содержит {{ lectures.length }} лекций и {{ tests.length }} тестов. 
            Запишитесь, чтобы получить полный доступ к материалам и отслеживать свой прогресс.
          </p>
          <div class="enrollment-gate-features">
            <div class="enrollment-gate-feature">
              <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              Доступ ко всем лекциям
            </div>
            <div class="enrollment-gate-feature">
              <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              Прохождение тестов
            </div>
            <div class="enrollment-gate-feature">
              <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              Отслеживание прогресса
            </div>
            <div class="enrollment-gate-feature">
              <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              Сертификат по завершении
            </div>
          </div>
          <button
            class="btn btn-primary text-lg px-8 py-3 gap-2 mt-2"
            :disabled="enrolling"
            @click="enrollNow"
          >
            <svg v-if="enrolling" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ enrolling ? 'Запись...' : 'Записаться бесплатно' }}
          </button>
        </div>
        <!-- Blurred preview of lectures list -->
        <div class="enrollment-gate-preview">
          <h3 class="text-base font-bold text-slate-700 mb-3">Содержание курса</h3>
          <div class="space-y-2">
            <div v-for="(lecture, i) in lectures.slice(0, 5)" :key="i" class="flex items-center gap-3 p-2.5 rounded-lg bg-slate-50 border border-slate-100 blur-[2px] select-none">
              <div class="w-7 h-7 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600 text-xs font-bold shrink-0">{{ i + 1 }}</div>
              <span class="text-sm text-slate-700">{{ lecture.title }}</span>
            </div>
            <div v-if="lectures.length > 5" class="text-center text-sm text-slate-400 py-1">... и ещё {{ lectures.length - 5 }} лекций</div>
          </div>
        </div>
      </div>

      <!-- CONTENT (enrolled or teacher) -->
      <div v-else>
        <!-- Mobile Sidebar Toggle -->
        <button
          class="lg:hidden w-full mb-4 flex items-center justify-between p-4 bg-white rounded-xl border border-slate-200 shadow-sm"
          @click="showSidebar = !showSidebar"
        >
          <span class="font-semibold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
            Содержание курса
          </span>
          <svg class="w-5 h-5 text-slate-400 transition-transform" :class="{ 'rotate-180': showSidebar }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Sidebar -->
          <div class="lg:col-span-1" :class="{ 'hidden lg:block': !showSidebar }">
            <div class="card p-4 md:p-5 lg:sticky lg:top-24">
              <h2 class="text-base font-bold mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                Содержание курса
              </h2>

              <!-- Sidebar progress mini-bar -->
              <div v-if="isEnrolled && lectures.length > 0" class="mb-4 p-3 bg-sky-50 rounded-xl">
                <div class="flex justify-between text-xs text-sky-700 mb-1.5 font-medium">
                  <span>Прогресс</span>
                  <span>{{ viewedCount }}/{{ lectures.length }}</span>
                </div>
                <div class="h-1.5 bg-sky-100 rounded-full overflow-hidden">
                  <div class="h-full bg-sky-500 rounded-full transition-all" :style="{ width: courseProgress + '%' }"></div>
                </div>
              </div>

              <!-- Lectures -->
              <div v-if="lectures.length > 0" class="mb-4">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                  Лекции
                </div>
                <div class="space-y-1.5">
                  <button
                    v-for="(lecture, idx) in lectures"
                    :key="lecture.id"
                    class="sidebar-item"
                    :class="{
                      'sidebar-item--active': selectedLecture?.id === lecture.id,
                      'sidebar-item--done': isLectureViewed(lecture.id),
                    }"
                    @click="openLecture(lecture)"
                  >
                    <div class="sidebar-item-num">
                      <svg v-if="isLectureViewed(lecture.id)" class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                      <span v-else class="text-xs">{{ idx + 1 }}</span>
                    </div>
                    <div class="flex-1 min-w-0 text-left">
                      <div class="text-sm font-medium truncate" :class="selectedLecture?.id === lecture.id ? 'text-sky-700' : 'text-slate-700'">{{ lecture.title }}</div>
                      <div class="text-xs text-slate-400 truncate mt-0.5">{{ lecture.description }}</div>
                    </div>
                  </button>
                </div>
              </div>

              <!-- Tests -->
              <div v-if="tests.length > 0">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  Тесты
                </div>
                <div class="space-y-1.5">
                  <button
                    v-for="test in tests"
                    :key="test.id"
                    class="sidebar-item"
                    :class="{ 'sidebar-item--active-violet': selectedTest?.id === test.id }"
                    @click="openTest(test)"
                  >
                    <div class="sidebar-item-num sidebar-item-num--violet">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <div class="flex-1 min-w-0 text-left">
                      <div class="text-sm font-medium truncate" :class="selectedTest?.id === test.id ? 'text-violet-700' : 'text-slate-700'">{{ test.title }}</div>
                      <div class="text-xs text-slate-400 mt-0.5">{{ test.questions_count }} вопросов</div>
                    </div>
                  </button>
                </div>
              </div>

              <div v-if="lectures.length === 0 && tests.length === 0" class="text-slate-400 text-sm text-center py-4">
                Нет доступных материалов
              </div>
            </div>
          </div>

          <!-- Main Content Area -->
          <div class="lg:col-span-2">

            <!-- Lecture View -->
            <div v-if="selectedLecture" class="card p-6 md:p-8 animate-slide-up">
              <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2 text-sm text-slate-500">
                  <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                  Лекция
                </div>
                <!-- Read status -->
                <div v-if="isEnrolled">
                  <span v-if="isLectureViewed(selectedLecture.id)" class="inline-flex items-center gap-1.5 text-sm text-emerald-600 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Прочитано
                  </span>
                  <button
                    v-else
                    class="btn btn-secondary text-sm gap-1.5"
                    :disabled="markingRead"
                    @click="markLectureRead(selectedLecture)"
                  >
                    <svg v-if="markingRead" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Отметить прочитанной
                  </button>
                </div>
              </div>

              <h2 class="text-2xl font-bold text-slate-800 mb-6">{{ selectedLecture.title }}</h2>
              <div class="prose max-w-none text-slate-600" v-html="renderMarkdown(selectedLecture.content)"></div>

              <!-- Navigation between lectures -->
              <div class="flex items-center justify-between mt-8 pt-6 border-t border-slate-100">
                <button
                  v-if="prevLecture"
                  class="btn btn-secondary gap-2 text-sm"
                  @click="openLecture(prevLecture)"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                  Предыдущая
                </button>
                <div v-else></div>

                <button
                  v-if="isEnrolled && !isLectureViewed(selectedLecture.id)"
                  class="btn btn-primary gap-2 text-sm"
                  :disabled="markingRead"
                  @click="markAndNext"
                >
                  <svg v-if="markingRead" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                  {{ nextLecture ? 'Прочитал, далее' : 'Завершить лекцию' }}
                </button>
                <button
                  v-else-if="nextLecture"
                  class="btn btn-primary gap-2 text-sm"
                  @click="openLecture(nextLecture)"
                >
                  Следующая
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
              </div>

              <!-- Materials -->
              <div v-if="lectureMaterials.length > 0" class="mt-6 pt-6 border-t border-slate-100">
                <h3 class="text-base font-bold mb-3 flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                  Материалы
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <a v-for="m in lectureMaterials" :key="m.id" :href="m.file_url" target="_blank"
                    class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/50 transition-all group">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="font-medium text-slate-800 text-sm truncate">{{ m.title }}</div>
                      <div class="text-xs text-slate-500">{{ m.file_size }}</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                  </a>
                </div>
              </div>
            </div>

            <!-- Test View -->
            <div v-else-if="selectedTest" class="card p-6 md:p-8 animate-slide-up">
              <div class="flex items-center gap-2 text-sm text-slate-500 mb-4 pb-4 border-b border-slate-100">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Тест
              </div>
              <h2 class="text-2xl font-bold text-slate-800 mb-2">{{ selectedTest.title }}</h2>
              <p class="text-slate-500 mb-6">{{ selectedTest.description }}</p>

              <!-- Autosave indicator -->
              <div v-if="testStarted" class="mb-4 flex items-center gap-2 text-sm">
                <svg v-if="isSavingDraft" class="w-4 h-4 text-sky-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <svg v-else-if="autosaveError" class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M10.29 3.86l-8.1 14.04A2 2 0 003.9 21h16.2a2 2 0 001.73-3.1l-8.1-14.04a2 2 0 00-3.46 0z" /></svg>
                <svg v-else class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span :class="autosaveError ? 'text-red-600' : isSavingDraft ? 'text-sky-600' : 'text-emerald-600'">
                  {{ autosaveError ? 'Ошибка автосохранения' : isSavingDraft ? 'Сохранение...' : 'Черновик сохранён' }}
                </span>
                <span v-if="!autosaveError && !isSavingDraft && lastDraftSavedAt" class="text-slate-500">· {{ draftSavedAgoText }}</span>
              </div>

              <!-- Inline error -->
              <div v-if="inlineError" class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M10.29 3.86l-8.1 14.04A2 2 0 003.9 21h16.2a2 2 0 001.73-3.1l-8.1-14.04a2 2 0 00-3.46 0z" /></svg>
                {{ inlineError }}
              </div>

              <!-- Test Result -->
              <div v-if="testResult" class="mb-6">
                <div :class="testResult.passed ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'" class="rounded-2xl border p-6 text-center">
                  <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" :class="testResult.passed ? 'bg-emerald-100' : 'bg-red-100'">
                    <svg v-if="testResult.passed" class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <svg v-else class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  </div>
                  <h3 class="text-xl font-bold mb-1" :class="testResult.passed ? 'text-emerald-700' : 'text-red-700'">
                    {{ testResult.passed ? 'Тест пройден!' : 'Тест не пройден' }}
                  </h3>
                  <p class="text-2xl font-extrabold mb-1" :class="testResult.passed ? 'text-emerald-600' : 'text-red-600'">
                    {{ testResult.score }} / {{ testResult.max_score }} баллов
                  </p>
                  <p class="text-slate-500 text-sm mb-4">{{ testResult.correct_answers }} из {{ testResult.total_questions }} правильных · {{ testResult.percentage }}%</p>
                  <button @click="openTest(selectedTest)" class="btn btn-secondary">Пройти ещё раз</button>
                </div>
              </div>

              <div v-else-if="!testStarted" class="text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-violet-100 to-purple-100 flex items-center justify-center">
                  <svg class="w-8 h-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <p class="text-slate-500 mb-6">Готовы проверить свои знания?</p>
                <button @click="startTest" class="btn btn-primary">
                  Начать тест
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg>
                </button>
              </div>

              <div v-else>
                <TestComponent :test="selectedTest" :initial-answers="attemptAnswers" @submit="handleTestSubmit" @answers-change="handleAnswersChange" />
              </div>
            </div>

            <!-- Placeholder -->
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

    <!-- Unenroll Confirm Modal -->
    <Teleport to="body">
      <div v-if="unenrollConfirm" class="modal-backdrop" @click.self="unenrollConfirm = false">
        <div class="modal-box">
          <div class="modal-header">
            <h3 class="modal-title">Отписаться от курса</h3>
            <button class="modal-close" @click="unenrollConfirm = false">&times;</button>
          </div>
          <div class="modal-body">
            <p class="text-slate-600">Вы уверены, что хотите отписаться от <strong>«{{ course?.title }}»</strong>?</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="unenrollConfirm = false">Отмена</button>
            <button class="btn btn-danger" :disabled="unenrolling" @click="doUnenroll">
              <svg v-if="unenrolling" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              Отписаться
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Course Complete Celebration -->
    <Teleport to="body">
      <div v-if="showCelebration" class="modal-backdrop" @click="showCelebration = false">
        <div class="celebration-box">
          <div class="text-5xl mb-4">🎉</div>
          <h2 class="text-2xl font-extrabold text-slate-800 mb-2">Курс завершён!</h2>
          <p class="text-slate-500 mb-1">Вы прочитали все лекции курса</p>
          <p class="font-bold text-sky-600 text-lg mb-6">«{{ course?.title }}»</p>
          <button class="btn btn-primary" @click="showCelebration = false">Отлично!</button>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import { marked } from 'marked'
import { useCourseStore } from '../../stores/courses'
import { useStudentStore } from '../../stores/studentStore'
import { useAuthStore } from '../../stores/auth'
import TestComponent from '../tests/TestComponent.vue'

marked.setOptions({ breaks: true, gfm: true })

const route        = useRoute()
const courseStore  = useCourseStore()
const studentStore = useStudentStore()
const authStore    = useAuthStore()

const loading       = ref(true)
const showSidebar   = ref(false)
const enrolling     = ref(false)
const unenrollConfirm = ref(false)
const unenrolling   = ref(false)
const markingRead   = ref(false)
const showCelebration = ref(false)

// Test state
const inlineError      = ref('')
const testResult       = ref(null)
const selectedLecture  = ref(null)
const selectedTest     = ref(null)
const testStarted      = ref(false)
const startedAt        = ref(null)
const attemptAnswers   = ref({})
const liveAnswers      = ref({})
const autosaveTimer    = ref(null)
const draftTicker      = ref(null)
const lastDraftSavedAt = ref(null)
const autosaveError    = ref(false)
const isSavingDraft    = ref(false)
const nowTick          = ref(Date.now())

const course  = computed(() => courseStore.currentCourse)
const lectures = computed(() => courseStore.lectures)
const tests    = computed(() => courseStore.tests)
const lectureMaterials = computed(() => courseStore.materials)

const renderMarkdown = (content) => content ? marked.parse(content) : ''

// Enrollment state - sourced from API response embedded in course
const isEnrolled = computed(() => {
  if (authStore.isTeacher) return true
  return !!course.value?.enrollment?.enrolled
})

const isLectureViewed = (lectureId) => {
  const ids = course.value?.enrollment?.viewed_lecture_ids ?? []
  return ids.includes(Number(lectureId)) || studentStore.isLectureViewed(lectureId)
}

const viewedCount = computed(() => {
  return lectures.value.filter(l => isLectureViewed(l.id)).length
})

const courseProgress = computed(() => {
  const total = lectures.value.length
  if (!total) return 0
  return Math.round((viewedCount.value / total) * 100)
})

// Prev/Next lecture navigation
const currentLectureIndex = computed(() =>
  lectures.value.findIndex(l => l.id === selectedLecture.value?.id)
)
const prevLecture = computed(() =>
  currentLectureIndex.value > 0 ? lectures.value[currentLectureIndex.value - 1] : null
)
const nextLecture = computed(() =>
  currentLectureIndex.value < lectures.value.length - 1 ? lectures.value[currentLectureIndex.value + 1] : null
)

const draftSavedAgoText = computed(() => {
  nowTick.value
  if (!lastDraftSavedAt.value) return ''
  const diff = Math.max(0, Math.floor((Date.now() - lastDraftSavedAt.value) / 1000))
  if (diff < 5) return 'только что'
  if (diff < 60) return `${diff} сек назад`
  return `${Math.floor(diff / 60)} мин назад`
})

/* ── Enrollment ── */
const enrollNow = async () => {
  enrolling.value = true
  try {
    await studentStore.enroll(course.value.id)
    // Reload course to get updated enrollment data
    await courseStore.fetchCourse(route.params.id)
  } catch (e) {
    console.error(e)
  } finally {
    enrolling.value = false
  }
}

const doUnenroll = async () => {
  unenrolling.value = true
  try {
    await studentStore.unenroll(course.value.id)
    await courseStore.fetchCourse(route.params.id)
    unenrollConfirm.value = false
    selectedLecture.value = null
    selectedTest.value = null
  } finally {
    unenrolling.value = false
  }
}

/* ── Lecture ── */
const openLecture = async (lecture) => {
  stopAutosave()
  selectedLecture.value = lecture
  selectedTest.value = null
  testStarted.value = false
  lastDraftSavedAt.value = null
  autosaveError.value = false
  showSidebar.value = false
  await courseStore.fetchMaterials(lecture.id)
}

const markLectureRead = async (lecture) => {
  if (!isEnrolled.value || isLectureViewed(lecture.id)) return
  markingRead.value = true
  try {
    const result = await studentStore.completeLecture(lecture.id)
    // Push into the local course enrollment data reactively
    if (course.value?.enrollment?.viewed_lecture_ids) {
      course.value.enrollment.viewed_lecture_ids.push(Number(lecture.id))
      course.value.enrollment.progress = result.progress
    }
    if (result.course_completed) {
      showCelebration.value = true
    }
  } catch (e) {
    console.error(e)
  } finally {
    markingRead.value = false
  }
}

const markAndNext = async () => {
  await markLectureRead(selectedLecture.value)
  if (nextLecture.value && !showCelebration.value) {
    openLecture(nextLecture.value)
  }
}

/* ── Test ── */
const openTest = (test) => {
  stopAutosave()
  selectedTest.value = test
  selectedLecture.value = null
  testStarted.value = false
  testResult.value = null
  inlineError.value = ''
  startedAt.value = null
  attemptAnswers.value = {}
  liveAnswers.value = {}
  lastDraftSavedAt.value = null
  autosaveError.value = false
  showSidebar.value = false
}

const startTest = async () => {
  inlineError.value = ''
  try {
    const attempt = await courseStore.startTestAttempt(selectedTest.value.id)
    const fullAttempt = await courseStore.fetchTestAttempt(attempt.attempt_id)
    const fullTest = await courseStore.fetchTest(selectedTest.value.id)
    selectedTest.value = { ...selectedTest.value, ...fullTest, questions: fullTest.questions || courseStore.questions }
    attemptAnswers.value = fullAttempt?.answers || attempt.answers || {}
    liveAnswers.value = { ...attemptAnswers.value }
    startedAt.value = attempt.started_at ? Date.parse(attempt.started_at) : Date.now()
    testStarted.value = true
    testResult.value = null
    lastDraftSavedAt.value = Date.now()
    autosaveError.value = false
    startAutosave()
  } catch {
    inlineError.value = courseStore.error || 'Не удалось начать попытку теста'
  }
}

const handleTestSubmit = async (answers) => {
  inlineError.value = ''
  try {
    const attemptId = courseStore.currentAttempt?.attempt_id || courseStore.currentAttempt?.id
    if (!attemptId) throw new Error('Попытка не инициализирована')
    const timeSpent = startedAt.value ? Math.max(0, Math.floor((Date.now() - startedAt.value) / 1000)) : 0
    stopAutosave()
    const response = await courseStore.submitTestAttempt(attemptId, answers, timeSpent)
    testResult.value = response.result
    testStarted.value = false
    startedAt.value = null
    attemptAnswers.value = {}
    liveAnswers.value = {}
    lastDraftSavedAt.value = null
    autosaveError.value = false
  } catch {
    inlineError.value = courseStore.error || 'Ошибка при отправке теста'
  }
}

const handleAnswersChange = (answers) => { liveAnswers.value = answers || {} }

const startAutosave = () => {
  stopAutosave()
  autosaveTimer.value = setInterval(saveDraft, 10000)
  draftTicker.value = setInterval(() => { nowTick.value = Date.now() }, 1000)
}

const stopAutosave = () => {
  if (autosaveTimer.value) { clearInterval(autosaveTimer.value); autosaveTimer.value = null }
  if (draftTicker.value)   { clearInterval(draftTicker.value);   draftTicker.value = null }
}

const saveDraft = async () => {
  const attemptId = courseStore.currentAttempt?.attempt_id || courseStore.currentAttempt?.id
  if (!attemptId || !testStarted.value) return
  try {
    isSavingDraft.value = true
    const timeSpent = startedAt.value ? Math.max(0, Math.floor((Date.now() - startedAt.value) / 1000)) : 0
    await courseStore.saveTestAttemptDraft(attemptId, liveAnswers.value, timeSpent)
    lastDraftSavedAt.value = Date.now()
    autosaveError.value = false
  } catch {
    autosaveError.value = true
  } finally {
    isSavingDraft.value = false
  }
}

/* ── Helpers ── */
const getStatusClass = (s) => ({ published: 'badge-success', draft: 'badge-warning' }[s] || 'bg-gray-100 text-gray-800')
const getStatusText  = (s) => ({ published: 'Опубликован', draft: 'Черновик', archived: 'Архив' }[s] || s)

onMounted(async () => {
  const courseId = route.params.id
  await courseStore.fetchCourse(courseId)
  await courseStore.fetchLectures(courseId)
  await courseStore.fetchTests(courseId)
  // Populate student store with viewed lecture IDs from course response
  const viewed = courseStore.currentCourse?.enrollment?.viewed_lecture_ids ?? []
  studentStore.setViewedLectures(viewed)
  loading.value = false
})

onBeforeUnmount(async () => {
  await saveDraft()
  stopAutosave()
})
</script>

<style scoped>
/* Sidebar items */
.sidebar-item {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 0.625rem;
  padding: 0.5rem 0.625rem;
  border-radius: 10px;
  border: 1px solid transparent;
  transition: all 0.15s ease;
  cursor: pointer;
}
.sidebar-item:hover { background: #f8fafc; border-color: #e2e8f0; }
.sidebar-item--active { background: #eff6ff; border-color: #bae6fd; }
.sidebar-item--done .sidebar-item-num { background: #dcfce7; }
.sidebar-item--active-violet { background: #f5f3ff; border-color: #ddd6fe; }

.sidebar-item-num {
  width: 26px;
  height: 26px;
  border-radius: 7px;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: #64748b;
  font-weight: 600;
  transition: background 0.15s;
}
.sidebar-item-num--violet { background: #f5f3ff; color: #7c3aed; }

/* Enrollment Gate */
.enrollment-gate {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}
@media (min-width: 768px) {
  .enrollment-gate { grid-template-columns: 1fr 1fr; align-items: start; }
}
.enrollment-gate-inner {
  background: white;
  border-radius: 24px;
  padding: 2.5rem;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 24px rgba(0,0,0,0.06);
  text-align: center;
}
.enrollment-gate-icon { font-size: 3rem; margin-bottom: 1rem; }
.enrollment-gate-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 0.75rem; }
.enrollment-gate-text { color: #64748b; line-height: 1.6; margin-bottom: 1.5rem; }
.enrollment-gate-features {
  display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem;
  margin-bottom: 2rem; text-align: left;
}
.enrollment-gate-feature {
  display: flex; align-items: center; gap: 0.5rem;
  font-size: 0.875rem; color: #374151; font-weight: 500;
}
.enrollment-gate-preview {
  background: white;
  border-radius: 20px;
  padding: 1.5rem;
  border: 1px solid #e2e8f0;
  position: relative;
}
.enrollment-gate-preview::after {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 20px;
  background: linear-gradient(to bottom, transparent 40%, rgba(255,255,255,0.95) 100%);
  pointer-events: none;
}

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

/* Celebration */
.celebration-box { background: white; border-radius: 24px; padding: 3rem 2rem; text-align: center; max-width: 400px; box-shadow: 0 25px 60px rgba(0,0,0,0.2); }

/* Prose */
.prose { line-height: 1.8; }
.prose :deep(h1), .prose :deep(h2), .prose :deep(h3) { color: #0f172a; font-weight: 700; margin-top: 1.5em; margin-bottom: 0.5em; }
.prose :deep(p) { margin-bottom: 1em; }
.prose :deep(ul), .prose :deep(ol) { padding-left: 1.5em; margin-bottom: 1em; }
.prose :deep(li) { margin-bottom: 0.5em; }
.prose :deep(code) { background: #f1f5f9; padding: 0.2em 0.4em; border-radius: 4px; font-size: 0.9em; }
.prose :deep(pre) { background: #1e293b; color: #e2e8f0; padding: 1em; border-radius: 8px; overflow-x: auto; margin: 1em 0; }
.prose :deep(pre code) { background: transparent; padding: 0; color: inherit; }
.prose :deep(blockquote) { border-left: 4px solid #e2e8f0; padding-left: 1rem; color: #64748b; margin: 1em 0; }

.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
</style>
