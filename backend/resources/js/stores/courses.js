import { defineStore } from 'pinia'
import axios from 'axios'

export const useCourseStore = defineStore('courses', {
  state: () => ({
    courses: [],
    currentCourse: null,
    lectures: [],
    materials: [],
    tests: [],
    loading: false,
    error: null
  }),

  getters: {
    getCoursesByStatus: (state) => (status) => {
      return state.courses.filter(course => course.status === status)
    },
    
    getPublishedCourses: (state) => {
      return state.courses.filter(course => course.status === 'published')
    }
  },

  actions: {
    async fetchCourses() {
      this.loading = true
      try {
        const response = await axios.get('/api/courses')
        this.courses = response.data
      } catch (error) {
        this.error = error.message
        console.error('Fetch courses error:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchCourse(id) {
      this.loading = true
      try {
        const response = await axios.get(`/api/courses/${id}`)
        this.currentCourse = response.data
      } catch (error) {
        this.error = error.message
        console.error('Fetch course error:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchLectures(courseId) {
      this.loading = true
      try {
        const response = await axios.get(`/api/courses/${courseId}/lectures`)
        this.lectures = response.data
      } catch (error) {
        this.error = error.message
        console.error('Fetch lectures error:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchMaterials(lectureId) {
      this.loading = true
      try {
        const response = await axios.get(`/api/lectures/${lectureId}/materials`)
        this.materials = response.data
      } catch (error) {
        this.error = error.message
        console.error('Fetch materials error:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchTests(courseId) {
      this.loading = true
      try {
        const response = await axios.get(`/api/courses/${courseId}/tests`)
        this.tests = response.data
      } catch (error) {
        this.error = error.message
        console.error('Fetch tests error:', error)
      } finally {
        this.loading = false
      }
    },

    async submitTestAnswers(testId, answers) {
      try {
        const response = await axios.post(`/api/tests/${testId}/submit`, { answers })
        return response.data
      } catch (error) {
        this.error = error.message
        console.error('Submit test error:', error)
        throw error
      }
    },

    // Teacher actions
    async createCourse(courseData) {
      try {
        const response = await axios.post('/api/courses', courseData)
        this.courses.push(response.data)
        return response.data
      } catch (error) {
        this.error = error.message
        console.error('Create course error:', error)
        throw error
      }
    },

    async updateCourse(id, courseData) {
      try {
        const response = await axios.put(`/api/courses/${id}`, courseData)
        const index = this.courses.findIndex(course => course.id === id)
        if (index !== -1) {
          this.courses[index] = response.data
        }
        return response.data
      } catch (error) {
        this.error = error.message
        console.error('Update course error:', error)
        throw error
      }
    },

    async deleteCourse(id) {
      try {
        await axios.delete(`/api/courses/${id}`)
        this.courses = this.courses.filter(course => course.id !== id)
      } catch (error) {
        this.error = error.message
        console.error('Delete course error:', error)
        throw error
      }
    }
  }
})
