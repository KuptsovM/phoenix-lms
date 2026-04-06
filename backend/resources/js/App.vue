<template>
  <div id="app">
    <nav class="bg-gray-800 text-white p-4">
      <div class="container mx-auto flex justify-between items-center">
        <div class="text-xl font-bold">
          <router-link to="/">Phoenix LMS</router-link>
        </div>
        <div class="flex space-x-4">
          <router-link to="/dashboard" class="hover:text-gray-300">Главная</router-link>
          <router-link to="/courses" class="hover:text-gray-300">Курсы</router-link>
          <template v-if="user">
            <span class="text-gray-300">{{ user.name }}</span>
            <button @click="logout" class="hover:text-gray-300">Выйти</button>
          </template>
          <template v-else>
            <router-link to="/login" class="hover:text-gray-300">Войти</router-link>
          </template>
        </div>
      </div>
    </nav>
    
    <main class="container mx-auto p-4">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const user = computed(() => authStore.user)

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
