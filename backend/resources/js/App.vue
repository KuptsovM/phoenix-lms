<template>
  <div id="app" class="app-shell">
    <!-- Header -->
    <header class="topbar">
      <div class="container mx-auto">
        <div class="flex items-center justify-between gap-4 py-3">
          <!-- Logo -->
          <div class="brand-block">
            <router-link to="/" class="brand-link">
              <span class="brand-icon">🔥</span>
              Phoenix LMS
            </router-link>
            <p class="brand-subtitle">Платформа для обучения</p>
          </div>

          <!-- Navigation -->
          <nav class="hidden md:flex items-center gap-1">
            <router-link 
              v-for="item in navItems" 
              :key="item.to"
              :to="item.to"
              class="nav-item"
              :class="{ 'nav-item--active': isActive(item.to) }"
            >
              <span v-html="item.icon" class="nav-icon"></span>
              {{ item.label }}
            </router-link>
          </nav>

          <!-- Right side -->
          <div class="flex items-center gap-3">
            <!-- Catalog Button -->
            <button 
              class="catalog-btn"
              @click="toggleMenu"
            >
              <span class="catalog-icon" :class="{ 'catalog-icon--open': menuOpen }">
                <span></span>
                <span></span>
                <span></span>
              </span>
              Каталог
              <svg class="h-4 w-4 transition-transform duration-200" :class="{'rotate-180': menuOpen}" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.207l3.71-3.976a.75.75 0 111.08 1.04l-4.24 4.54a.75.75 0 01-1.08 0l-4.24-4.54a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
              </svg>
            </button>

            <template v-if="user">
              <div class="user-menu">
                <button class="user-trigger">
                  <div class="user-avatar">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                  <span class="user-name">{{ user.name }}</span>
                  <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                <div class="user-dropdown">
                  <div class="user-dropdown-header">
                    <div class="user-avatar-lg">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <div class="font-semibold text-slate-800">{{ user.name }}</div>
                      <div class="text-xs text-slate-500">{{ user.email }}</div>
                    </div>
                  </div>
                  <div class="user-dropdown-divider"></div>
                  <router-link to="/dashboard" class="user-dropdown-item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Личный кабинет
                  </router-link>
                  <router-link v-if="authStore.isTeacher" to="/teacher" class="user-dropdown-item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Панель преподавателя
                  </router-link>
                  <div class="user-dropdown-divider"></div>
                  <button @click="logout" class="user-dropdown-item user-dropdown-item--danger">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Выйти
                  </button>
                </div>
              </div>
            </template>
            <template v-else>
              <router-link to="/login" class="btn btn-secondary btn-sm">
                Войти
              </router-link>
            </template>
          </div>
        </div>

        <!-- Mega Menu -->
        <transition name="menu-fade">
          <MegaMenu
            v-if="menuOpen"
            :is-teacher="authStore.isTeacher"
            :study-items="studyItems"
            :teacher-items="teacherItems"
            :resource-items="resourceItems"
            @close="menuOpen = false"
          />
        </transition>
      </div>
    </header>

    <!-- Breadcrumb -->
    <div class="breadcrumb-bar" v-if="breadcrumbs.length > 1">
      <div class="container mx-auto">
        <nav class="breadcrumb">
          <router-link to="/" class="breadcrumb-item">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
          </router-link>
          <template v-for="(crumb, index) in breadcrumbs" :key="index">
            <span class="breadcrumb-separator">/</span>
            <router-link 
              v-if="index < breadcrumbs.length - 1"
              :to="crumb.to" 
              class="breadcrumb-item"
            >
              {{ crumb.label }}
            </router-link>
            <span v-else class="breadcrumb-item breadcrumb-item--active">
              {{ crumb.label }}
            </span>
          </template>
        </nav>
      </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto p-4 md:p-6">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="container mx-auto">
        <div class="footer-content">
          <div class="footer-brand">
            <span class="brand-icon">🔥</span>
            <span>Phoenix LMS</span>
          </div>
          <p class="footer-text"><a href="https://github.com/KuptsovM/phoenix-lms">https://github.com/KuptsovM/phoenix-lms</a></p>
          <div class="footer-links">
            <a href="#" class="footer-link">О нас</a>
            <a href="#" class="footer-link">Поддержка</a>
            <a href="#" class="footer-link">Документация</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from './stores/auth'
import MegaMenu from './components/MegaMenu.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const user = computed(() => authStore.user)
const menuOpen = ref(false)

const navItems = [
  { to: '/dashboard', label: 'Главная', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>' },
  { to: '/courses', label: 'Курсы', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>' },
]

const studyItems = [
  { to: '/dashboard', label: 'Главная', desc: 'Лента и быстрые действия', icon: 'M3.75 6.75l6.25-3 6.25 3-6.25 3-6.25-3z M3.75 10.5l6.25 3 6.25-3', tint: '#38bdf8' },
  { to: '/courses', label: 'Курсы', desc: 'Все учебные программы', icon: 'M4 6h12M4 10h12M4 14h12', tint: '#34d399' },
  { to: '/tests/1', label: 'Тесты', desc: 'Проверка знаний', icon: 'M9 7h6m-6 3h6m-6 3h3M5 7h.01M5 10h.01M5 13h.01', tint: '#a78bfa' },
]

const teacherItems = [
  { to: '/teacher', label: 'Дашборд', desc: 'Метрики и управление', icon: 'M3 13l4-8 4 8 3-6 3 6', tint: '#fbbf24' },
  { to: '/teacher/courses', label: 'Курсы', desc: 'Создать и редактировать', icon: 'M4 6h12v9H4z M9 10h6 M9 13h6', tint: '#38bdf8' },
  { to: '/teacher/lectures', label: 'Лекции', desc: 'Материалы и порядок', icon: 'M5 7h10M5 11h6M5 15h4', tint: '#34d399' },
  { to: '/teacher/tests', label: 'Тесты', desc: 'Вопросы и попытки', icon: 'M6 7h12M6 11h8M6 15h5 M3 7h.01M3 11h.01M3 15h.01', tint: '#a78bfa' },
]

const resourceItems = [
  { to: '/login', label: 'Подсказки по входу', desc: 'Student / Teacher — password', icon: 'M3 8l7-5 7 5v7a2 2 0 01-2 2H5a2 2 0 01-2-2z', tint: '#fb923c' },
  { to: '/', label: 'Поддержка', desc: 'support@phoenix.lms', icon: 'M3 5h14v10H3z M7 9h6', tint: '#94a3b8' },
]

const breadcrumbs = computed(() => {
  const paths = route.path.split('/').filter(Boolean)
  const crumbs = [{ to: '/', label: 'Главная' }]
  
  let currentPath = ''
  paths.forEach((path, index) => {
    currentPath += `/${path}`
    const label = path.charAt(0).toUpperCase() + path.slice(1).replace(/-/g, ' ')
    crumbs.push({ to: currentPath, label })
  })
  
  return crumbs
})

const isActive = (path) => {
  return route.path === path || route.path.startsWith(path + '/')
}

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value
}
</script>

<style scoped>
.app-shell {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background:
    radial-gradient(circle at top, rgba(14, 165, 233, 0.15), transparent 28%),
    linear-gradient(180deg, #f8fafc 0%, #eef6ff 100%);
}

/* Topbar */
.topbar {
  position: sticky;
  top: 0;
  z-index: 30;
  background: linear-gradient(180deg, rgba(15, 23, 42, 0.98) 0%, rgba(15, 23, 42, 0.95) 100%);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(148, 163, 184, 0.12);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
}

.brand-block {
  display: flex;
  flex-direction: column;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.4rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  background: linear-gradient(135deg, #fff 0%, #67e8f9 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.brand-icon {
  font-size: 1.5rem;
  -webkit-text-fill-color: initial;
}

.brand-subtitle {
  margin: 0;
  color: #64748b;
  font-size: 0.75rem;
  font-weight: 500;
}

/* Navigation */
.nav-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 12px;
  color: #94a3b8;
  font-weight: 500;
  font-size: 0.9rem;
  transition: all 0.2s ease;
}

.nav-item:hover {
  color: #e2e8f0;
  background: rgba(255, 255, 255, 0.05);
}

.nav-item--active {
  color: #fff;
  background: rgba(56, 189, 248, 0.15);
}

.nav-icon {
  display: flex;
}

/* Catalog Button */
.catalog-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #e2e8f0;
  font-weight: 500;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.catalog-btn:hover {
  background: rgba(255, 255, 255, 0.12);
  border-color: rgba(56, 189, 248, 0.3);
}

.catalog-icon {
  display: flex;
  flex-direction: column;
  gap: 3px;
  width: 18px;
}

.catalog-icon span {
  display: block;
  height: 2px;
  background: currentColor;
  border-radius: 1px;
  transition: all 0.2s ease;
}

.catalog-icon--open span:nth-child(1) {
  transform: translateY(5px) rotate(45deg);
}

.catalog-icon--open span:nth-child(2) {
  opacity: 0;
}

.catalog-icon--open span:nth-child(3) {
  transform: translateY(-5px) rotate(-45deg);
}

/* User Menu */
.user-menu {
  position: relative;
}

.user-trigger {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.35rem 0.75rem 0.35rem 0.35rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #f8fafc;
  cursor: pointer;
  transition: all 0.2s ease;
}

.user-trigger:hover {
  background: rgba(255, 255, 255, 0.12);
}

.user-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: linear-gradient(135deg, #38bdf8, #818cf8);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.75rem;
}

.user-name {
  font-weight: 500;
  font-size: 0.9rem;
}

.user-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  width: 240px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.2s ease;
  z-index: 50;
}

.user-menu:hover .user-dropdown {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.user-dropdown-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
}

.user-avatar-lg {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #38bdf8, #818cf8);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  color: white;
}

.user-dropdown-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 0 0.5rem;
}

.user-dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  color: #475569;
  font-weight: 500;
  font-size: 0.9rem;
  transition: all 0.15s ease;
}

.user-dropdown-item:hover {
  background: #f8fafc;
  color: #0f172a;
}

.user-dropdown-item--danger {
  color: #ef4444;
}

.user-dropdown-item--danger:hover {
  background: #fef2f2;
  color: #dc2626;
}

/* Breadcrumb */
.breadcrumb-bar {
  background: white;
  border-bottom: 1px solid #e2e8f0;
  padding: 0.75rem 0;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.breadcrumb-item {
  color: #64748b;
  transition: color 0.15s ease;
}

.breadcrumb-item:hover {
  color: #0f172a;
}

.breadcrumb-item--active {
  color: #0f172a;
  font-weight: 500;
}

.breadcrumb-separator {
  color: #cbd5e1;
}

/* Footer */
.footer {
  margin-top: auto;
  background: white;
  border-top: 1px solid #e2e8f0;
  padding: 2rem 0;
}

.footer-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  text-align: center;
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 700;
  color: #0f172a;
}

.footer-text {
  color: #64748b;
  font-size: 0.875rem;
}

.footer-links {
  display: flex;
  gap: 1.5rem;
  margin-top: 0.5rem;
}

.footer-link {
  color: #64748b;
  font-size: 0.875rem;
  transition: color 0.15s ease;
}

.footer-link:hover {
  color: #0f172a;
}

/* Animations */
.menu-fade-enter-active,
.menu-fade-leave-active {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.menu-fade-enter-from,
.menu-fade-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
