<template>
  <div class="max-w-md mx-auto mt-8 bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Вход в систему</h2>
    
    <form @submit.prevent="handleLogin">
      <div class="mb-4">
        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
          Email
        </label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        />
      </div>
      
      <div class="mb-6">
        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
          Пароль
        </label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        />
      </div>
      
      <div v-if="error" class="mb-4 text-red-500 text-sm">
        {{ error }}
      </div>
      
      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 disabled:bg-gray-400"
      >
        {{ loading ? 'Вход...' : 'Войти' }}
      </button>
    </form>
    
    <div class="mt-4 text-center text-sm text-gray-600">
      <p>Тестовые данные:</p>
      <p>Студент: student@example.com / password</p>
      <p>Учитель: teacher@example.com / password</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
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

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const success = await authStore.login(form.value.email, form.value.password)
    if (success) {
      router.push('/dashboard')
    } else {
      error.value = 'Неверные учетные данные'
    }
  } catch (err) {
    error.value = 'Произошла ошибка при входе'
  } finally {
    loading.value = false
  }
}
</script>
