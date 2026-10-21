import axios from 'axios'

const API_BASE_URL = 'http://localhost:8000/api'

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

export const taskAPI = {
  // Get all tasks
  getTasks() {
    return api.get('/tasks')
  },

  // Get task by ID
  getTask(id) {
    return api.get(`/tasks/${id}`)
  },

  // Create new task
  createTask(data) {
    return api.post('/tasks', data)
  },

  // Update task
  updateTask(id, data) {
    return api.put(`/tasks/${id}`, data)
  },

  // Delete task
  deleteTask(id) {
    return api.delete(`/tasks/${id}`)
  },

  // Get summary statistics
  getSummary() {
    return api.get('/tasks/summary')
  }
}

export default api
