<template>
  <div id="app" class="app-shell">
    <!-- Header -->
    <header class="topbar">
      <div class="container mx-auto">
        <div class="flex items-center justify-between gap-2 sm:gap-4 py-2 sm:py-3">
          <!-- Logo -->
          <div class="brand-block">
            <router-link to="/" class="brand-link">
              <span class="brand-icon">🔥</span>
              <span class="brand-name">Phoenix LMS</span>
            </router-link>
          </div>

          <!-- Right side -->
          <div class="flex items-center gap-2 md:gap-3">
            <!-- Mobile Menu Button -->
            <button 
              class="md:hidden p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition-colors"
              @click="mobileMenuOpen = !mobileMenuOpen"
            >
              <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
            <!-- Catalog Button (desktop) -->
            <button 
              class="hidden md:flex catalog-btn"
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
                  <router-link v-if="!authStore.isTeacher" to="/my-courses" class="user-dropdown-item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Мои курсы
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

        <!-- Mega Menu (desktop) -->
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

    <!-- Mobile Menu Panel -->
    <transition name="mobile-menu">
      <div v-if="mobileMenuOpen" class="mobile-menu-overlay" @click="mobileMenuOpen = false">
        <div class="mobile-menu" @click.stop>
          <!-- Mobile User Info -->
          <div v-if="user" class="mobile-user-section">
            <div class="mobile-user-avatar">
              {{ user.name.charAt(0).toUpperCase() }}
            </div>
            <div class="mobile-user-info">
              <div class="mobile-user-name">{{ user.name }}</div>
              <div class="mobile-user-email">{{ user.email }}</div>
            </div>
          </div>

          <!-- Mobile Navigation -->
          <nav class="mobile-nav">
            <router-link 
              v-for="item in navItems" 
              :key="item.to"
              :to="item.to"
              class="mobile-nav-item"
              :class="{ 'mobile-nav-item--active': isActive(item.to) }"
              @click="mobileMenuOpen = false"
            >
              <span v-html="item.icon" class="mobile-nav-icon"></span>
              {{ item.label }}
            </router-link>

            <div class="mobile-nav-divider"></div>

            <!-- Study Items -->
            <router-link 
              v-for="item in studyItems"
              :key="item.to"
              :to="item.to"
              class="mobile-nav-item mobile-nav-item--sub"
              @click="mobileMenuOpen = false"
            >
              <span class="mobile-nav-icon" v-html="item.icon"></span>
              {{ item.label }}
            </router-link>

            <!-- Teacher Items -->
            <template v-if="authStore.isTeacher">
              <div class="mobile-nav-divider"></div>
              <div class="mobile-nav-label">Преподаватель</div>
              <router-link 
                v-for="item in teacherItems"
                :key="item.to"
                :to="item.to"
                class="mobile-nav-item mobile-nav-item--sub"
                @click="mobileMenuOpen = false"
              >
                <span class="mobile-nav-icon" v-html="item.icon"></span>
                {{ item.label }}
              </router-link>
            </template>

            <div class="mobile-nav-divider"></div>

            <!-- Resources -->
            <router-link 
              v-for="item in resourceItems"
              :key="item.to"
              :to="item.to"
              class="mobile-nav-item mobile-nav-item--sub"
              @click="mobileMenuOpen = false"
            >
              <span class="mobile-nav-icon" v-html="item.icon"></span>
              {{ item.label }}
            </router-link>
          </nav>

          <!-- Mobile User Actions -->
          <div class="mobile-actions">
            <template v-if="user">
              <router-link to="/dashboard" class="mobile-action-item" @click="mobileMenuOpen = false">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Личный кабинет
              </router-link>
              <router-link v-if="authStore.isTeacher" to="/teacher" class="mobile-action-item" @click="mobileMenuOpen = false">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Панель преподавателя
              </router-link>
              <button @click="handleLogout" class="mobile-action-item mobile-action-item--danger">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Выйти
              </button>
            </template>
            <template v-else>
              <router-link to="/login" class="mobile-action-item mobile-action-item--primary" @click="mobileMenuOpen = false">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Войти
              </router-link>
            </template>
          </div>
        </div>
      </div>
    </transition>

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
    <main class="container mx-auto px-3 sm:px-4 py-4 md:p-6">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="container mx-auto px-4">
        <div class="footer-content">
          <div class="footer-brand">
            <span class="brand-icon">🔥</span>
            <span>Phoenix LMS</span>
          </div>
          <p class="footer-text"><a href="https://github.com/KuptsovM/phoenix-lms" class="break-all">github.com/KuptsovM/phoenix-lms</a></p>
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
const mobileMenuOpen = ref(false)

const navItems = [
  { to: '/dashboard', label: 'Главная', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>' },
  { to: '/my-courses', label: 'Мои курсы', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>' },
  { to: '/courses', label: 'Каталог', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>' },
]

const studyItems = [
  { to: '/dashboard', label: 'Главная', desc: 'Лента и быстрые действия', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', tint: '#38bdf8' },
  { to: '/my-courses', label: 'Мои курсы', desc: 'Прогресс и записи', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', tint: '#34d399' },
  { to: '/courses', label: 'Каталог курсов', desc: 'Все учебные программы', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', tint: '#a78bfa' },
]

const teacherItems = [
  { to: '/teacher', label: 'Дашборд', desc: 'Метрики и управление', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', tint: '#fbbf24' },
  { to: '/teacher/courses', label: 'Мои курсы', desc: 'Создать и редактировать', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', tint: '#38bdf8' },
  { to: '/teacher/lectures', label: 'Лекции', desc: 'Материалы и порядок', icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', tint: '#34d399' },
  { to: '/teacher/tests', label: 'Тесты', desc: 'Вопросы и попытки', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', tint: '#a78bfa' },
]

const resourceItems = [
  { to: '/login', label: 'Подсказки по входу', desc: 'Student / Teacher — password', icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', tint: '#fb923c' },
  { to: '/', label: 'Поддержка', desc: 'support@phoenix.lms', icon: 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', tint: '#94a3b8' },
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
  mobileMenuOpen.value = false
  router.push('/login')
}

const handleLogout = async () => {
  await logout()
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
  padding: 1.5rem 0;
}

@media (min-width: 640px) {
  .footer {
    padding: 2rem 0;
  }
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
  font-size: 0.8rem;
}

@media (min-width: 640px) {
  .footer-text {
    font-size: 0.875rem;
  }
}

.footer-links {
  display: flex;
  gap: 1rem;
  margin-top: 0.5rem;
  flex-wrap: wrap;
  justify-content: center;
}

.footer-link {
  color: #64748b;
  font-size: 0.8rem;
  transition: color 0.15s ease;
}

@media (min-width: 640px) {
  .footer-links {
    gap: 1.5rem;
  }

  .footer-link {
    font-size: 0.875rem;
  }
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

/* Mobile Menu */
.mobile-menu-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 50;
  display: flex;
  justify-content: flex-end;
}

.mobile-menu {
  width: 100%;
  max-width: 320px;
  height: 100%;
  background: white;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.mobile-user-section {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: white;
}

.mobile-user-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #38bdf8, #818cf8);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.25rem;
}

.mobile-user-info {
  flex: 1;
  min-width: 0;
}

.mobile-user-name {
  font-weight: 600;
  font-size: 1rem;
}

.mobile-user-email {
  font-size: 0.8rem;
  color: #94a3b8;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mobile-nav {
  flex: 1;
  padding: 1rem;
  overflow-y: auto;
}

.mobile-nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.875rem 1rem;
  border-radius: 12px;
  color: #475569;
  font-weight: 500;
  font-size: 0.95rem;
  transition: all 0.15s ease;
  text-decoration: none;
}

.mobile-nav-item:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.mobile-nav-item--active {
  background: rgba(56, 189, 248, 0.1);
  color: #0284c7;
}

.mobile-nav-item--sub {
  padding-left: 1.5rem;
  font-size: 0.9rem;
}

.mobile-nav-icon {
  display: flex;
  width: 20px;
  height: 20px;
}

.mobile-nav-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 0.75rem 0;
}

.mobile-nav-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 0 1rem;
  margin-bottom: 0.25rem;
}

.mobile-actions {
  padding: 1rem;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
}

.mobile-action-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.875rem 1rem;
  border-radius: 12px;
  color: #475569;
  font-weight: 500;
  font-size: 0.95rem;
  text-decoration: none;
  transition: all 0.15s ease;
  width: 100%;
  border: none;
  background: transparent;
  cursor: pointer;
}

.mobile-action-item:hover {
  background: #e2e8f0;
  color: #0f172a;
}

.mobile-action-item--primary {
  background: #0f172a;
  color: white;
}

.mobile-action-item--primary:hover {
  background: #1e293b;
  color: white;
}

.mobile-action-item--danger {
  color: #ef4444;
}

.mobile-action-item--danger:hover {
  background: #fef2f2;
  color: #dc2626;
}

/* Mobile Menu Animation */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-menu-enter-from .mobile-menu,
.mobile-menu-leave-to .mobile-menu {
  transform: translateX(100%);
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  background: transparent;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .brand-name {
    display: none;
  }

  .brand-link {
    font-size: 1.25rem;
  }

  .user-name {
    display: none;
  }
}
</style>
