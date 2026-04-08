import { defineStore } from 'pinia'
import axios from 'axios'

export const useCourseStore = defineStore('courses', {
  state: () => ({
    courses: [],
    currentCourse: null,
    lectures: [],
    currentLecture: null,
    tests: [],
    currentTest: null,
    currentAttempt: null,
    questions: [],
    materials: [],
    loading: false,
    error: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0
    }
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
    // ==================== КУРСЫ ====================
    
    async fetchCourses(params = {}) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get('/api/courses', { params })
        this.courses = response.data.data
        this.pagination = {
          current_page: response.data.meta.current_page,
          last_page: response.data.meta.last_page,
          per_page: response.data.meta.per_page,
          total: response.data.meta.total
        }
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchCourse(id) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`/api/courses/${id}`)
        this.currentCourse = response.data
        return response.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async createCourse(courseData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post('/api/courses', courseData)
        this.courses.unshift(response.data.data)
        return response.data.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateCourse(id, courseData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.put(`/api/courses/${id}`, courseData)
        const index = this.courses.findIndex(c => c.id === id)
        if (index !== -1) {
          this.courses[index] = response.data.data
        }
        if (this.currentCourse?.id === id) {
          this.currentCourse = { ...this.currentCourse, ...response.data.data }
        }
        return response.data.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteCourse(id) {
      this.loading = true
      this.error = null
      try {
        await axios.delete(`/api/courses/${id}`)
        this.courses = this.courses.filter(c => c.id !== id)
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    // ==================== ЛЕКЦИИ ====================

    async fetchLectures(courseId) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`/api/courses/${courseId}/lectures`)
        this.lectures = (response.data.data || []).map(this.normalizeLecture)
        return this.lectures
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchLecture(id) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`/api/lectures/${id}`)
        this.currentLecture = response.data
        return response.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchMaterials(lectureId) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`/api/lectures/${lectureId}/materials`)
        this.materials = response.data.data || []
        return this.materials
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async createLecture(courseId, lectureData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post(`/api/courses/${courseId}/lectures`, lectureData)
        const normalized = this.normalizeLecture(response.data.data)
        this.lectures.push(normalized)
        return normalized
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateLecture(id, lectureData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.put(`/api/lectures/${id}`, lectureData)
        const index = this.lectures.findIndex(l => l.id === id)
        const normalized = this.normalizeLecture(response.data.data)
        if (index !== -1) {
          this.lectures[index] = { ...this.lectures[index], ...normalized }
        }
        if (this.currentLecture?.id === id) {
          this.currentLecture = { ...this.currentLecture, ...normalized }
        }
        return normalized
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteLecture(id) {
      this.loading = true
      this.error = null
      try {
        await axios.delete(`/api/lectures/${id}`)
        this.lectures = this.lectures.filter(l => l.id !== id)
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async reorderLectures(courseId, lectures) {
      this.loading = true
      this.error = null
      try {
        await axios.post(`/api/courses/${courseId}/lectures/reorder`, { lectures })
        lectures.forEach((item, index) => {
          const lecture = this.lectures.find(l => l.id === item.id)
          if (lecture) lecture.order = item.order
        })
        this.lectures.sort((a, b) => a.order - b.order)
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    // ==================== МАТЕРИАЛЫ ЛЕКЦИЙ ====================

    async uploadMaterial(lectureId, formData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post(`/api/lectures/${lectureId}/materials`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        if (this.currentLecture?.materials) {
          this.currentLecture.materials.push(response.data.data)
        }
        return response.data.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteMaterial(materialId) {
      this.loading = true
      this.error = null
      try {
        await axios.delete(`/api/materials/${materialId}`)
        if (this.currentLecture?.materials) {
          this.currentLecture.materials = this.currentLecture.materials.filter(m => m.id !== materialId)
        }
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    // ==================== ТЕСТЫ ====================

    async fetchTests(courseId) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`/api/courses/${courseId}/tests`)
        this.tests = (response.data.data || []).map(this.normalizeTest)
        return this.tests
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchTest(id) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`/api/tests/${id}`)
        this.currentTest = this.normalizeTest(response.data)
        this.questions = response.data.questions || []
        return this.currentTest
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async createTest(courseId, testData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post(`/api/courses/${courseId}/tests`, testData)
        const normalized = this.normalizeTest(response.data.data)
        this.tests.push(normalized)
        return normalized
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateTest(id, testData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.put(`/api/tests/${id}`, testData)
        const index = this.tests.findIndex(t => t.id === id)
        const normalized = this.normalizeTest(response.data.data)
        if (index !== -1) {
          this.tests[index] = { ...this.tests[index], ...normalized }
        }
        if (this.currentTest?.id === id) {
          this.currentTest = { ...this.currentTest, ...normalized }
        }
        return normalized
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteTest(id) {
      this.loading = true
      this.error = null
      try {
        await axios.delete(`/api/tests/${id}`)
        this.tests = this.tests.filter(t => t.id !== id)
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    // ==================== ВОПРОСЫ ТЕСТОВ ====================

    async addQuestion(testId, questionData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post(`/api/tests/${testId}/questions`, questionData)
        this.questions.push(response.data.data)
        if (this.currentTest) {
          this.currentTest.questions_count++
        }
        return response.data.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateQuestion(questionId, questionData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.put(`/api/questions/${questionId}`, questionData)
        const index = this.questions.findIndex(q => q.id === questionId)
        if (index !== -1) {
          this.questions[index] = response.data.data
        }
        return response.data.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteQuestion(questionId) {
      this.loading = true
      this.error = null
      try {
        await axios.delete(`/api/questions/${questionId}`)
        this.questions = this.questions.filter(q => q.id !== questionId)
        if (this.currentTest) {
          this.currentTest.questions_count--
        }
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async bulkAddQuestions(testId, questions) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post(`/api/tests/${testId}/questions/bulk`, { questions })
        await this.fetchTest(testId)
        return response.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    // ==================== ПРОХОЖДЕНИЕ ТЕСТОВ ====================

    async submitTestAnswers(testId, answers, timeSpent) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post(`/api/tests/${testId}/submit`, {
          answers,
          time_spent: timeSpent
        })
        return response.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async startTestAttempt(testId) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post(`/api/tests/${testId}/attempts`)
        this.currentAttempt = response.data.data
        return this.currentAttempt
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchTestAttempt(attemptId) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`/api/attempts/${attemptId}`)
        this.currentAttempt = response.data.data
        return this.currentAttempt
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async submitTestAttempt(attemptId, answers, timeSpent) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post(`/api/attempts/${attemptId}/submit`, {
          answers,
          time_spent: timeSpent
        })
        return response.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async saveTestAttemptDraft(attemptId, answers, timeSpent = null) {
      this.error = null
      try {
        const response = await axios.post(`/api/attempts/${attemptId}/draft`, {
          answers,
          time_spent: timeSpent
        })
        return response.data.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      }
    },

    async getTestResults(testId) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get(`/api/tests/${testId}/results`)
        return response.data.data
      } catch (error) {
        this.error = this.handleError(error)
        throw error
      } finally {
        this.loading = false
      }
    },

    // ==================== УТИЛИТЫ ====================

    handleError(error) {
      if (error.response) {
        if (error.response.status === 422) {
          return error.response.data.message || 'Ошибка валидации'
        }
        if (error.response.status === 403) {
          return error.response.data.message || 'Доступ запрещен'
        }
        if (error.response.status === 404) {
          return 'Ресурс не найден'
        }
        return error.response.data.message || 'Ошибка сервера'
      }
      if (error.request) {
        return 'Нет соединения с сервером'
      }
      return error.message || 'Неизвестная ошибка'
    },

    clearError() {
      this.error = null
    },

    clearCurrentCourse() {
      this.currentCourse = null
      this.lectures = []
      this.tests = []
    },

    clearCurrentTest() {
      this.currentTest = null
      this.questions = []
      this.currentAttempt = null
    },

    normalizeLecture(lecture = {}) {
      return {
        ...lecture,
        course_id: lecture.course_id != null ? Number(lecture.course_id) : null
      }
    },

    normalizeTest(test = {}) {
      return {
        ...test,
        course_id: test.course_id != null ? Number(test.course_id) : null,
        questions_count: Number(test.questions_count || 0)
      }
    }
  }
})
