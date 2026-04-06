<template>
  <section class="auth-shell">
    <div class="auth-card">
      <div class="auth-copy">
        <p class="auth-eyebrow">Phoenix LMS</p>
        <h1>Вход в учебный кабинет</h1>
        <p>
          Используйте рабочие тестовые данные для входа как студент или
          преподаватель.
        </p>
      </div>

      <div class="demo-grid">
        <button
          type="button"
          class="demo-card"
          @click="fillDemo('student')"
        >
          <span class="demo-role">Студент</span>
          <strong>student@example.com</strong>
          <span>Пароль: password</span>
        </button>

        <button
          type="button"
          class="demo-card demo-card--teacher"
          @click="fillDemo('teacher')"
        >
          <span class="demo-role">Преподаватель</span>
          <strong>teacher@example.com</strong>
          <span>Пароль: password</span>
        </button>
      </div>

      <form class="auth-form" @submit.prevent="handleLogin">
        <div class="field">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="student@example.com"
            required
          />
        </div>

        <div class="field">
          <label for="password">Пароль</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="password"
            required
          />
        </div>

        <p v-if="error" class="error-text">
          {{ error }}
        </p>

        <button
          type="submit"
          :disabled="loading"
          class="submit-button"
        >
          {{ loading ? 'Входим...' : 'Войти в систему' }}
        </button>
      </form>

      <div class="auth-hint">
        <p>Если тестовые аккаунты были изменены, обновите их командой:</p>
        <code>cd backend && php artisan db:seed</code>
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

const demoAccounts = {
  student: {
    email: 'student@example.com',
    password: 'password'
  },
  teacher: {
    email: 'teacher@example.com',
    password: 'password'
  }
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
.auth-shell {
  min-height: calc(100vh - 12rem);
  display: flex;
  align-items: center;
  justify-content: center;
}

.auth-card {
  width: min(100%, 760px);
  padding: 2rem;
  border-radius: 28px;
  background:
    radial-gradient(circle at top right, rgba(14, 165, 233, 0.16), transparent 28%),
    radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.16), transparent 30%),
    rgba(255, 255, 255, 0.96);
  border: 1px solid rgba(148, 163, 184, 0.22);
  box-shadow: 0 28px 80px rgba(15, 23, 42, 0.12);
  backdrop-filter: blur(14px);
}

.auth-copy h1 {
  margin: 0;
  font-size: clamp(2rem, 4vw, 2.8rem);
  line-height: 1.05;
  color: #0f172a;
}

.auth-copy p {
  margin: 0.75rem 0 0;
  color: #475569;
}

.auth-eyebrow {
  margin: 0 0 0.75rem;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: #0284c7;
}

.demo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
  margin: 1.75rem 0;
}

.demo-card {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  padding: 1rem 1.1rem;
  text-align: left;
  border: 1px solid #bfdbfe;
  border-radius: 20px;
  background: linear-gradient(135deg, #eff6ff, #ffffff);
  color: #1e3a8a;
  transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
}

.demo-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 16px 30px rgba(37, 99, 235, 0.14);
  border-color: #60a5fa;
}

.demo-card--teacher {
  border-color: #a7f3d0;
  background: linear-gradient(135deg, #ecfdf5, #ffffff);
  color: #065f46;
}

.demo-role {
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  opacity: 0.75;
}

.auth-form {
  display: grid;
  gap: 1rem;
}

.field {
  display: grid;
  gap: 0.45rem;
}

.field label {
  font-size: 0.95rem;
  font-weight: 600;
  color: #334155;
}

.field input {
  padding: 0.95rem 1rem;
  border: 1px solid #cbd5e1;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.92);
  color: #0f172a;
  outline: none;
  transition: border-color 0.18s ease, box-shadow 0.18s ease;
}

.field input:focus {
  border-color: #0ea5e9;
  box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
}

.submit-button {
  padding: 0.95rem 1.2rem;
  border: none;
  border-radius: 16px;
  font-weight: 700;
  color: #fff;
  background: linear-gradient(135deg, #0284c7, #0f766e);
  box-shadow: 0 18px 30px rgba(14, 116, 144, 0.25);
  transition: transform 0.18s ease, opacity 0.18s ease;
}

.submit-button:hover:enabled {
  transform: translateY(-1px);
}

.submit-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.error-text {
  margin: 0;
  color: #b91c1c;
  font-size: 0.92rem;
}

.auth-hint {
  margin-top: 1rem;
  font-size: 0.92rem;
  color: #64748b;
}

.auth-hint code {
  display: inline-block;
  margin-top: 0.45rem;
  padding: 0.5rem 0.7rem;
  border-radius: 12px;
  background: #e2e8f0;
  color: #0f172a;
}
</style>
