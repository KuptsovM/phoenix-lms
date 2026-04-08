import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Компоненты
import Login from '../components/auth/Login.vue'
import Dashboard from '../components/Dashboard.vue'
import CoursesList from '../components/courses/CoursesList.vue'
import CourseView from '../components/courses/CourseView.vue'
import LectureView from '../components/lectures/LectureView.vue'
import TestView from '../components/tests/TestView.vue'
import MyCourses from '../components/student/MyCourses.vue'
import TeacherDashboard from '../components/teacher/TeacherDashboard.vue'
import CourseManage from '../components/teacher/CourseManage.vue'
import CourseStudents from '../components/teacher/CourseStudents.vue'
import LectureManage from '../components/teacher/LectureManage.vue'
import TestManage from '../components/teacher/TestManage.vue'

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/courses',
    name: 'courses',
    component: CoursesList,
    meta: { requiresAuth: true }
  },
  {
    path: '/courses/:id',
    name: 'course-view',
    component: CourseView,
    meta: { requiresAuth: true }
  },
  {
    path: '/my-courses',
    name: 'my-courses',
    component: MyCourses,
    meta: { requiresAuth: true }
  },
  {
    path: '/lectures/:id',
    name: 'lecture-view',
    component: LectureView,
    meta: { requiresAuth: true }
  },
  {
    path: '/tests/:id',
    name: 'test-view',
    component: TestView,
    meta: { requiresAuth: true }
  },
  // Teacher routes
  {
    path: '/teacher',
    name: 'teacher-dashboard',
    component: TeacherDashboard,
    meta: { requiresAuth: true, requiresTeacher: true }
  },
  {
    path: '/teacher/courses',
    name: 'teacher-courses',
    component: CourseManage,
    meta: { requiresAuth: true, requiresTeacher: true }
  },
  {
    path: '/teacher/lectures',
    name: 'teacher-lectures',
    component: LectureManage,
    meta: { requiresAuth: true, requiresTeacher: true }
  },
  {
    path: '/teacher/tests',
    name: 'teacher-tests',
    component: TestManage,
    meta: { requiresAuth: true, requiresTeacher: true }
  },
  {
    path: '/teacher/courses/:courseId/students',
    name: 'teacher-course-students',
    component: CourseStudents,
    meta: { requiresAuth: true, requiresTeacher: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next('/dashboard')
  } else if (to.meta.requiresTeacher && !authStore.isTeacher) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
