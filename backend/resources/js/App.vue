<template>
  <div id="app" class="app-shell">
    <nav class="topbar">
      <div class="container mx-auto flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="brand-block">
          <router-link to="/" class="brand-link">Phoenix LMS</router-link>
          <p class="brand-subtitle">Платформа для студентов и преподавателей</p>
        </div>

        <div class="nav-links">
          <router-link to="/dashboard" class="nav-link">Главная</router-link>
          <router-link to="/courses" class="nav-link">Курсы</router-link>
          <router-link v-if="authStore.isTeacher" to="/teacher" class="nav-link">Преподаватель</router-link>

          <template v-if="user">
            <span class="user-pill">{{ user.name }}</span>
            <button @click="logout" class="nav-link nav-link--button">Выйти</button>
          </template>

          <template v-else>
            <router-link to="/login" class="nav-link">Войти</router-link>
          </template>
        </div>
      </div>
    </nav>

    <main class="container mx-auto p-4 md:p-6">
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

<style scoped>
.app-shell {
  min-height: 100vh;
  background:
    radial-gradient(circle at top, rgba(14, 165, 233, 0.15), transparent 28%),
    linear-gradient(180deg, #f8fafc 0%, #eef6ff 100%);
}

.topbar {
  position: sticky;
  top: 0;
  z-index: 10;
  border-bottom: 1px solid rgba(148, 163, 184, 0.18);
  background: rgba(15, 23, 42, 0.92);
  backdrop-filter: blur(14px);
  color: white;
  padding: 1rem 1.25rem;
}

.brand-block {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.brand-link {
  font-size: 1.25rem;
  font-weight: 800;
  letter-spacing: 0.04em;
}

.brand-subtitle {
  margin: 0;
  color: #cbd5e1;
  font-size: 0.92rem;
}

.nav-links {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.75rem;
}

.nav-link {
  color: #e2e8f0;
  transition: color 0.18s ease;
}

.nav-link:hover {
  color: #67e8f9;
}

.nav-link--button {
  background: transparent;
  border: none;
  padding: 0;
}

.user-pill {
  padding: 0.42rem 0.8rem;
  border-radius: 999px;
  background: rgba(148, 163, 184, 0.18);
  color: #f8fafc;
}
</style>
