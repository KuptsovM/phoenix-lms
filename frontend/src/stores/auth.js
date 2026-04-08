import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isTeacher: (state) => state.user?.roles?.some(role => role.name === 'teacher' || role.name === 'admin'),
    isStudent: (state) => state.user?.roles?.some(role => role.name === 'student')
  },

  actions: {
    async login(email, password) {
      try {
        const response = await axios.post('/api/login', { email, password })
        const { token, user } = response.data
        
        this.token = token
        this.user = user
        localStorage.setItem('token', token)
        
        // Устанавливаем токен для axios
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return true
      } catch (error) {
        console.error('Login error:', error)
        return false
      }
    },

    async logout() {
      try {
        await axios.post('/api/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']
      }
    },

    async fetchUser() {
      if (!this.token) return
      
      try {
        const response = await axios.get('/api/user')
        this.user = response.data
      } catch (error) {
        console.error('Fetch user error:', error)
        await this.logout()
      }
    },

    init() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        this.fetchUser()
      }
    }
  }
})
