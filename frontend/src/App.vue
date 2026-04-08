<template>
  <div id="app" class="app-shell">
    <!-- ─── Header ─── -->
    <header class="topbar">
      <div class="topbar-inner">
        <!-- Logo -->
        <router-link to="/" class="brand-link">
          <span class="brand-icon">🔥</span>
          <span class="brand-name">Phoenix LMS</span>
        </router-link>

        <!-- Desktop: Catalog + User -->
        <div class="desktop-right">
          <button class="catalog-btn" @click="toggleMenu">
            <span class="catalog-icon" :class="{ 'catalog-icon--open': menuOpen }">
              <span></span><span></span><span></span>
            </span>
            Каталог
          </button>

          <template v-if="user">
            <div class="user-menu">
              <button class="user-trigger">
                <div class="avatar">{{ initial }}</div>
                <span class="user-name">{{ user.name }}</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div class="user-dropdown">
                <div class="dropdown-header">
                  <div class="avatar avatar--lg">{{ initial }}</div>
                  <div>
                    <div class="font-semibold text-slate-800 text-sm">{{ user.name }}</div>
                    <div class="text-xs text-slate-500">{{ user.email }}</div>
                  </div>
                </div>
                <div class="dropdown-divider"></div>
                <router-link to="/dashboard" class="dropdown-item">
                  <IconHome /> Личный кабинет
                </router-link>
                <router-link v-if="!authStore.isTeacher" to="/my-courses" class="dropdown-item">
                  <IconCourses /> Мои курсы
                </router-link>
                <router-link v-if="authStore.isTeacher" to="/teacher" class="dropdown-item">
                  <IconTeacher /> Панель преподавателя
                </router-link>
                <div class="dropdown-divider"></div>
                <button @click="handleLogout" class="dropdown-item dropdown-item--danger">
                  <IconLogout /> Выйти
                </button>
              </div>
            </div>
          </template>
          <template v-else>
            <router-link to="/login" class="btn-login">Войти</router-link>
          </template>
        </div>

        <!-- Mobile: Avatar + Burger -->
        <div class="mobile-right">
          <div v-if="user" class="avatar avatar--sm" @click="drawerOpen = true">{{ initial }}</div>
          <button class="burger" @click="drawerOpen = !drawerOpen" :aria-expanded="drawerOpen">
            <svg v-if="!drawerOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Desktop Mega Menu -->
      <transition name="menu-fade">
        <MegaMenu
          v-if="menuOpen"
          :is-teacher="authStore.isTeacher"
          :study-items="studyItems"
          :teacher-items="teacherItems"
          @close="menuOpen = false"
        />
      </transition>
    </header>

    <!-- ─── Mobile Drawer ─── -->
    <Teleport to="body">
      <transition name="overlay-fade">
        <div v-if="drawerOpen" class="drawer-overlay" @click="drawerOpen = false" />
      </transition>
      <transition name="drawer-slide">
        <nav v-if="drawerOpen" class="drawer" @click.stop>
          <!-- User info -->
          <div v-if="user" class="drawer-user">
            <div class="avatar avatar--lg">{{ initial }}</div>
            <div class="drawer-user-info">
              <div class="drawer-user-name">{{ user.name }}</div>
              <div class="drawer-user-email">{{ user.email }}</div>
            </div>
            <button class="drawer-close" @click="drawerOpen = false">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div v-else class="drawer-guest">
            <span class="brand-icon">🔥</span>
            <span class="font-bold text-slate-800">Phoenix LMS</span>
            <button class="drawer-close ml-auto" @click="drawerOpen = false">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Nav links -->
          <div class="drawer-body">
            <div class="drawer-section">
              <router-link v-for="item in mobileNavItems" :key="item.to"
                :to="item.to" class="drawer-link" :class="{ 'drawer-link--active': isActive(item.to) }"
                @click="drawerOpen = false">
                <span class="drawer-link-icon" v-html="item.svg"></span>
                <span>{{ item.label }}</span>
              </router-link>
            </div>

            <template v-if="authStore.isTeacher">
              <div class="drawer-label">Преподаватель</div>
              <div class="drawer-section">
                <router-link v-for="item in teacherItems" :key="item.to"
                  :to="item.to" class="drawer-link" :class="{ 'drawer-link--active': isActive(item.to) }"
                  @click="drawerOpen = false">
                  <span class="drawer-link-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="item.icon" />
                    </svg>
                  </span>
                  <span>{{ item.label }}</span>
                </router-link>
              </div>
            </template>
          </div>

          <!-- Footer actions -->
          <div class="drawer-footer">
            <template v-if="user">
              <button @click="handleLogout" class="drawer-logout">
                <IconLogout /> Выйти из аккаунта
              </button>
            </template>
            <template v-else>
              <router-link to="/login" class="drawer-login-btn" @click="drawerOpen = false">
                Войти в систему
              </router-link>
            </template>
          </div>
        </nav>
      </transition>
    </Teleport>

    <!-- ─── Breadcrumb ─── -->
    <div class="breadcrumb-bar" v-if="breadcrumbs.length > 1">
      <div class="topbar-inner">
        <nav class="breadcrumb">
          <router-link to="/" class="breadcrumb-item">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
          </router-link>
          <template v-for="(crumb, i) in breadcrumbs" :key="i">
            <span class="breadcrumb-sep">/</span>
            <router-link v-if="i < breadcrumbs.length - 1" :to="crumb.to" class="breadcrumb-item">{{ crumb.label }}</router-link>
            <span v-else class="breadcrumb-item breadcrumb-item--active">{{ crumb.label }}</span>
          </template>
        </nav>
      </div>
    </div>

    <!-- ─── Content ─── -->
    <main class="page-content">
      <router-view />
    </main>

    <!-- ─── Footer ─── -->
    <footer class="site-footer">
      <div class="topbar-inner footer-inner">
        <div class="footer-brand">
          <span>🔥</span> Phoenix LMS
        </div>
        <div class="footer-links">
          <a href="#" class="footer-link">О нас</a>
          <a href="#" class="footer-link">Поддержка</a>
          <a href="#" class="footer-link">Документация</a>
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

// ── Mini icon components ──────────────────────────────────────────────────
const IconHome = { template: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>` }
const IconCourses = { template: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>` }
const IconTeacher = { template: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>` }
const IconLogout = { template: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>` }

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const user = computed(() => authStore.user)
const initial = computed(() => user.value?.name?.charAt(0).toUpperCase() ?? '?')
const menuOpen = ref(false)
const drawerOpen = ref(false)

// Desktop Mega Menu data
const studyItems = [
  { to: '/dashboard',  label: 'Главная',       desc: 'Лента и быстрые действия',  icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', tint: '#38bdf8' },
  { to: '/my-courses', label: 'Мои курсы',     desc: 'Прогресс и записи',          icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', tint: '#34d399' },
  { to: '/courses',    label: 'Каталог курсов', desc: 'Все учебные программы',     icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', tint: '#a78bfa' },
]

const teacherItems = [
  { to: '/teacher',          label: 'Дашборд',  desc: 'Метрики и управление',      icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', tint: '#fbbf24' },
  { to: '/teacher/courses',  label: 'Курсы',    desc: 'Создать и редактировать',   icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', tint: '#38bdf8' },
  { to: '/teacher/lectures', label: 'Лекции',   desc: 'Материалы и порядок',       icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z', tint: '#34d399' },
  { to: '/teacher/tests',    label: 'Тесты',    desc: 'Вопросы и попытки',         icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', tint: '#a78bfa' },
]

// Mobile drawer nav — flat, no duplicates
const mobileNavItems = computed(() => {
  const base = [
    { to: '/dashboard',  label: 'Главная',  svg: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>` },
    { to: '/courses',    label: 'Каталог',  svg: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>` },
  ]
  if (user.value && !authStore.isTeacher) {
    base.splice(1, 0, { to: '/my-courses', label: 'Мои курсы', svg: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>` })
  }
  return base
})

const breadcrumbs = computed(() => {
  const paths = route.path.split('/').filter(Boolean)
  const crumbs = []
  let current = ''
  paths.forEach(p => {
    current += `/${p}`
    crumbs.push({ to: current, label: p.charAt(0).toUpperCase() + p.slice(1).replace(/-/g, ' ') })
  })
  return crumbs
})

const isActive = (path) => route.path === path || route.path.startsWith(path + '/')

const handleLogout = async () => {
  drawerOpen.value = false
  await authStore.logout()
  router.push('/login')
}

const toggleMenu = () => { menuOpen.value = !menuOpen.value }
</script>

<style scoped>
/* ── Shell ─────────────────────────────────────────────────── */
.app-shell {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background:
    radial-gradient(circle at top, rgba(14, 165, 233, 0.12), transparent 30%),
    linear-gradient(180deg, #f8fafc 0%, #eef6ff 100%);
}

/* ── Topbar ─────────────────────────────────────────────────── */
.topbar {
  position: sticky;
  top: 0;
  z-index: 40;
  background: rgba(15, 23, 42, 0.97);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(148, 163, 184, 0.1);
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.18);
}

.topbar-inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  height: 56px;
}

@media (min-width: 768px) {
  .topbar-inner { padding: 0 1.5rem; height: 60px; }
}

/* ── Brand ──────────────────────────────────────────────────── */
.brand-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.2rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  background: linear-gradient(135deg, #fff 0%, #67e8f9 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  flex-shrink: 0;
  text-decoration: none;
}
.brand-icon {
  font-size: 1.3rem;
  -webkit-text-fill-color: initial;
}

/* ── Desktop right ──────────────────────────────────────────── */
.desktop-right {
  display: none;
  align-items: center;
  gap: 0.5rem;
  margin-left: auto;
}
@media (min-width: 768px) {
  .desktop-right { display: flex; }
}

/* ── Mobile right ───────────────────────────────────────────── */
.mobile-right {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-left: auto;
}
@media (min-width: 768px) {
  .mobile-right { display: none; }
}

/* ── Catalog button ─────────────────────────────────────────── */
.catalog-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.45rem 0.9rem;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #e2e8f0;
  font-weight: 500;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}
.catalog-btn:hover {
  background: rgba(255, 255, 255, 0.13);
  border-color: rgba(56, 189, 248, 0.35);
}
.catalog-icon {
  display: flex; flex-direction: column; gap: 3px; width: 16px;
}
.catalog-icon span {
  display: block; height: 2px; background: currentColor; border-radius: 1px; transition: all 0.2s;
}
.catalog-icon--open span:nth-child(1) { transform: translateY(5px) rotate(45deg); }
.catalog-icon--open span:nth-child(2) { opacity: 0; }
.catalog-icon--open span:nth-child(3) { transform: translateY(-5px) rotate(-45deg); }

/* ── Avatar ─────────────────────────────────────────────────── */
.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, #38bdf8, #818cf8);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.8rem;
  color: white;
  flex-shrink: 0;
  cursor: pointer;
}
.avatar--sm { width: 28px; height: 28px; font-size: 0.75rem; }
.avatar--lg { width: 40px; height: 40px; font-size: 1rem; }

/* ── Burger ─────────────────────────────────────────────────── */
.burger {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #e2e8f0;
  cursor: pointer;
  transition: all 0.2s;
}
.burger:hover { background: rgba(255, 255, 255, 0.14); }

/* ── User dropdown (desktop) ────────────────────────────────── */
.user-menu { position: relative; }
.user-trigger {
  display: flex; align-items: center; gap: 0.5rem;
  padding: 0.3rem 0.7rem 0.3rem 0.3rem;
  border-radius: 999px;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.1);
  color: #f8fafc;
  cursor: pointer;
  transition: all 0.2s;
}
.user-trigger:hover { background: rgba(255,255,255,0.13); }
.user-name { font-weight: 500; font-size: 0.875rem; }

.user-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  min-width: 220px;
  background: white;
  border-radius: 14px;
  box-shadow: 0 16px 40px rgba(0,0,0,0.18);
  opacity: 0;
  visibility: hidden;
  transform: translateY(-6px);
  transition: all 0.2s;
  z-index: 60;
  overflow: hidden;
}
.user-menu:hover .user-dropdown,
.user-menu:focus-within .user-dropdown {
  opacity: 1; visibility: visible; transform: translateY(0);
}
.dropdown-header {
  display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem;
}
.dropdown-divider { height: 1px; background: #e2e8f0; }
.dropdown-item {
  display: flex; align-items: center; gap: 0.625rem;
  padding: 0.625rem 1rem;
  color: #475569; font-weight: 500; font-size: 0.875rem;
  transition: all 0.15s;
  text-decoration: none;
  width: 100%; border: none; background: none; cursor: pointer; text-align: left;
}
.dropdown-item:hover { background: #f8fafc; color: #0f172a; }
.dropdown-item--danger { color: #ef4444; }
.dropdown-item--danger:hover { background: #fef2f2; color: #dc2626; }

.btn-login {
  display: flex; align-items: center;
  padding: 0.45rem 1rem;
  border-radius: 10px;
  background: rgba(56, 189, 248, 0.15);
  border: 1px solid rgba(56, 189, 248, 0.3);
  color: #7dd3fc;
  font-weight: 600; font-size: 0.875rem;
  text-decoration: none;
  transition: all 0.2s;
}
.btn-login:hover { background: rgba(56, 189, 248, 0.25); color: #e0f2fe; }

/* ── Drawer ─────────────────────────────────────────────────── */
.drawer-overlay {
  position: fixed; inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: 50;
}
.drawer {
  position: fixed;
  top: 0; right: 0; bottom: 0;
  width: min(300px, 85vw);
  background: white;
  z-index: 51;
  display: flex;
  flex-direction: column;
  box-shadow: -8px 0 40px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.drawer-user {
  display: flex; align-items: center; gap: 0.75rem;
  padding: 1.25rem 1rem 1.25rem 1.25rem;
  background: linear-gradient(135deg, #0f172a, #1e293b);
  flex-shrink: 0;
}
.drawer-guest {
  display: flex; align-items: center; gap: 0.5rem;
  padding: 1.25rem;
  border-bottom: 1px solid #e2e8f0;
  flex-shrink: 0;
}
.drawer-user-info { flex: 1; min-width: 0; }
.drawer-user-name { font-weight: 600; font-size: 0.9rem; color: white; }
.drawer-user-email {
  font-size: 0.75rem; color: #94a3b8;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.drawer-close {
  padding: 0.375rem; border-radius: 8px; border: none; background: rgba(255,255,255,0.1);
  color: #94a3b8; cursor: pointer; transition: all 0.15s; flex-shrink: 0;
}
.drawer-close:hover { background: rgba(255,255,255,0.18); color: white; }

.drawer-body { flex: 1; overflow-y: auto; padding: 0.75rem; }
.drawer-section { display: flex; flex-direction: column; gap: 2px; margin-bottom: 0.5rem; }
.drawer-label {
  font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em;
  text-transform: uppercase; color: #94a3b8;
  padding: 0.75rem 0.75rem 0.375rem;
}
.drawer-link {
  display: flex; align-items: center; gap: 0.75rem;
  padding: 0.75rem 0.875rem;
  border-radius: 10px;
  color: #374151; font-weight: 500; font-size: 0.9rem;
  text-decoration: none; transition: all 0.15s;
}
.drawer-link:hover { background: #f1f5f9; color: #0f172a; }
.drawer-link--active { background: rgba(56, 189, 248, 0.1); color: #0284c7; }
.drawer-link-icon { display: flex; color: #6b7280; flex-shrink: 0; }
.drawer-link--active .drawer-link-icon { color: #0284c7; }

.drawer-footer {
  padding: 0.875rem 1rem;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
  flex-shrink: 0;
}
.drawer-logout {
  display: flex; align-items: center; gap: 0.625rem;
  width: 100%; padding: 0.75rem 1rem;
  border-radius: 10px; border: none;
  background: #fef2f2; color: #ef4444;
  font-weight: 600; font-size: 0.875rem;
  cursor: pointer; transition: all 0.15s;
}
.drawer-logout:hover { background: #fee2e2; color: #dc2626; }
.drawer-login-btn {
  display: flex; align-items: center; justify-content: center;
  width: 100%; padding: 0.75rem;
  border-radius: 10px;
  background: #0f172a; color: white;
  font-weight: 600; font-size: 0.875rem;
  text-decoration: none; transition: all 0.15s;
}
.drawer-login-btn:hover { background: #1e293b; }

/* ── Animations ─────────────────────────────────────────────── */
.menu-fade-enter-active, .menu-fade-leave-active { transition: all 0.2s ease; }
.menu-fade-enter-from, .menu-fade-leave-to { opacity: 0; transform: translateY(-6px); }

.overlay-fade-enter-active, .overlay-fade-leave-active { transition: opacity 0.25s ease; }
.overlay-fade-enter-from, .overlay-fade-leave-to { opacity: 0; }

.drawer-slide-enter-active, .drawer-slide-leave-active { transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1); }
.drawer-slide-enter-from, .drawer-slide-leave-to { transform: translateX(100%); }

/* ── Breadcrumb ─────────────────────────────────────────────── */
.breadcrumb-bar {
  background: white; border-bottom: 1px solid #e2e8f0; padding: 0.6rem 0;
}
.breadcrumb { display: flex; align-items: center; gap: 0.375rem; font-size: 0.8rem; }
.breadcrumb-item { color: #64748b; transition: color 0.15s; text-decoration: none; }
.breadcrumb-item:hover { color: #0f172a; }
.breadcrumb-item--active { color: #0f172a; font-weight: 500; }
.breadcrumb-sep { color: #cbd5e1; }

/* ── Content ────────────────────────────────────────────────── */
.page-content {
  flex: 1;
  max-width: 1280px;
  width: 100%;
  margin: 0 auto;
  padding: 1rem;
}
@media (min-width: 768px) { .page-content { padding: 1.5rem; } }

/* ── Footer ─────────────────────────────────────────────────── */
.site-footer {
  background: white; border-top: 1px solid #e2e8f0; padding: 1.25rem 0;
}
.footer-inner {
  justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;
}
.footer-brand {
  display: flex; align-items: center; gap: 0.4rem;
  font-weight: 700; color: #0f172a; font-size: 0.9rem;
}
.footer-links { display: flex; gap: 1.25rem; flex-wrap: wrap; }
.footer-link { color: #64748b; font-size: 0.8rem; text-decoration: none; transition: color 0.15s; }
.footer-link:hover { color: #0f172a; }
</style>
