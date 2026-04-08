<template>
  <section class="min-h-[calc(100vh-3rem)] flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo & Title -->
      <div class="text-center mb-8 animate-fade-in">
        <div class="inline-flex items-center gap-2 mb-4">
          <span class="text-3xl">🔥</span>
          <span class="text-2xl font-extrabold bg-gradient-to-r from-slate-800 to-sky-600 bg-clip-text text-transparent">
            Phoenix LMS
          </span>
        </div>
        <h1 class="text-3xl font-bold text-slate-800">Добро пожаловать</h1>
        <p class="text-slate-500 mt-1">Войдите в свой учебный кабинет</p>
      </div>

      <!-- Demo Accounts -->
      <div class="mb-6 animate-slide-up">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Быстрый вход:</p>
        <div class="grid grid-cols-2 gap-3">
          <button
            type="button"
            class="flex items-center gap-3 p-3 border-2 border-sky-100 rounded-xl bg-sky-50 hover:border-sky-300 hover:bg-white transition-all text-left"
            @click="fillDemo('student')"
          >
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-sky-400 to-cyan-500 flex items-center justify-center text-white">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <div>
              <span class="block text-xs font-semibold text-slate-400">Студент</span>
              <span class="text-sm font-medium text-slate-700">student@</span>
            </div>
          </button>

          <button
            type="button"
            class="flex items-center gap-3 p-3 border-2 border-emerald-100 rounded-xl bg-emerald-50 hover:border-emerald-300 hover:bg-white transition-all text-left"
            @click="fillDemo('teacher')"
          >
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-white">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <div>
              <span class="block text-xs font-semibold text-slate-400">Преподаватель</span>
              <span class="text-sm font-medium text-slate-700">teacher@</span>
            </div>
          </button>
        </div>
      </div>

      <!-- Login Form -->
      <form class="card p-6 animate-slide-up stagger-2" @submit.prevent="handleLogin">
        <div class="space-y-4">
          <div>
            <label for="email" class="block text-sm font-semibold text-slate-600 mb-1.5">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="student@example.com"
              required
              class="input-field"
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-semibold text-slate-600 mb-1.5">Пароль</label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Введите пароль"
                required
                class="input-field pr-10"
              />
              <button 
                type="button" 
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                @click="showPassword = !showPassword"
              >
                <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-500 cursor-pointer">
              <input type="checkbox" v-model="rememberMe" class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500" />
              Запомнить
            </label>
            <a href="#" class="text-sm text-sky-500 hover:text-sky-600 font-medium">Забыли пароль?</a>
          </div>
        </div>

        <p v-if="error" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm flex items-center gap-2">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ error }}
        </p>

        <button
          type="submit"
          :disabled="loading"
          class="w-full mt-6 btn btn-primary"
        >
          <span v-if="loading" class="animate-spin w-5 h-5 border-2 border-white/30 border-t-white rounded-full"></span>
          <span v-else>Войти в систему</span>
        </button>
      </form>

      <!-- Footer Hint -->
      <div class="mt-6 text-center animate-fade-in">
        <p class="text-sm text-slate-500">Тестовые данные: <code class="px-1.5 py-0.5 bg-slate-100 rounded text-slate-700">student@example.com</code> или <code class="px-1.5 py-0.5 bg-slate-100 rounded text-slate-700">teacher@example.com</code></p>
        <p class="text-sm text-slate-400 mt-1">Пароль: <code class="px-1.5 py-0.5 bg-slate-100 rounded text-slate-600">password</code></p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  email: '',
  password: ''
})

const loading = ref(false)
const error = ref('')
const showPassword = ref(false)
const rememberMe = ref(false)

const demoAccounts = {
  student: { email: 'student@example.com', password: 'password' },
  teacher: { email: 'teacher@example.com', password: 'password' }
}

const fillDemo = (role) => {
  form.value = { ...demoAccounts[role] }
  error.value = ''
}

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    const success = await authStore.login(form.value.email, form.value.password)
    if (success) {
      router.push('/dashboard')
      return
    }
    error.value = 'Неверный email или пароль.'
  } catch (err) {
    error.value = 'Произошла ошибка при входе.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Minimal scoped styles - rely on Tailwind */
</style>
