import { defineStore } from 'pinia'
import axios from 'axios'

export const useStudentStore = defineStore('student', {
  state: () => ({
    enrollments: [],
    enrolledCourseIds: new Set(),
    viewedLectureIds: new Set(),
    dashboard: null,
    loadingEnrollments: false,
    loadingDashboard: false,
    enrollingCourseId: null,
  }),

  getters: {
    isEnrolled: (state) => (courseId) => state.enrolledCourseIds.has(Number(courseId)),
    isLectureViewed: (state) => (lectureId) => state.viewedLectureIds.has(Number(lectureId)),
    getEnrollment: (state) => (courseId) =>
      state.enrollments.find((e) => e.id === Number(courseId)),
    inProgressCourses: (state) => state.enrollments.filter((e) => !e.completed_at),
    completedCourses: (state) => state.enrollments.filter((e) => !!e.completed_at),
  },

  actions: {
    async fetchEnrollments() {
      this.loadingEnrollments = true
      try {
        const { data } = await axios.get('/api/my/courses')
        this.enrollments = data.data
        this.enrolledCourseIds = new Set(data.data.map((e) => Number(e.id)))
      } finally {
        this.loadingEnrollments = false
      }
    },

    async fetchDashboard() {
      this.loadingDashboard = true
      try {
        const { data } = await axios.get('/api/my/dashboard')
        this.dashboard = data
      } finally {
        this.loadingDashboard = false
      }
    },

    async enroll(courseId) {
      this.enrollingCourseId = Number(courseId)
      try {
        const { data } = await axios.post(`/api/courses/${courseId}/enroll`)
        this.enrolledCourseIds.add(Number(courseId))
        await this.fetchEnrollments()
        return data
      } finally {
        this.enrollingCourseId = null
      }
    },

    async unenroll(courseId) {
      await axios.delete(`/api/courses/${courseId}/unenroll`)
      this.enrolledCourseIds.delete(Number(courseId))
      this.enrollments = this.enrollments.filter((e) => e.id !== Number(courseId))
    },

    async checkEnrollment(courseId) {
      try {
        const { data } = await axios.get(`/api/courses/${courseId}/enrollment`)
        if (data.enrolled) {
          this.enrolledCourseIds.add(Number(courseId))
          // Populate viewed lecture ids if provided
          if (data.viewed_lecture_ids) {
            data.viewed_lecture_ids.forEach((id) => this.viewedLectureIds.add(Number(id)))
          }
        }
        return data
      } catch {
        return { enrolled: false }
      }
    },

    async completeLecture(lectureId) {
      const { data } = await axios.post(`/api/lectures/${lectureId}/complete`)
      this.viewedLectureIds.add(Number(lectureId))
      return data.data
    },

    setViewedLectures(ids = []) {
      ids.forEach((id) => this.viewedLectureIds.add(Number(id)))
    },

    reset() {
      this.enrollments = []
      this.enrolledCourseIds = new Set()
      this.viewedLectureIds = new Set()
      this.dashboard = null
    },
  },
})
