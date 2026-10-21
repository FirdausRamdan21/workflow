let tasks = []
let nextId = 1

const cloneTask = (task) => ({ ...task })

const sortedTasks = () =>
  [...tasks]
    .sort((first, second) => first.deadline.localeCompare(second.deadline))
    .map(cloneTask)

export const taskAPI = {
  async getTasks() {
    const data = sortedTasks()
    return { data: { data, count: data.length } }
  },

  async getTask(id) {
    const task = tasks.find((item) => item.id === Number(id))
    if (!task) throw new Error('Task not found')
    return { data: { data: cloneTask(task) } }
  },

  async createTask(data) {
    const task = {
      id: nextId++,
      nama: data.nama,
      deskripsi: data.deskripsi || null,
      deadline: data.deadline,
      prioritas: data.prioritas,
      status: data.status || 'belum_dikerjakan'
    }
    tasks.push(task)
    return { data: { data: cloneTask(task) } }
  },

  async updateTask(id, data) {
    const task = tasks.find((item) => item.id === Number(id))
    if (!task) throw new Error('Task not found')
    Object.assign(task, data)
    return { data: { data: cloneTask(task) } }
  },

  async deleteTask(id) {
    const index = tasks.findIndex((item) => item.id === Number(id))
    if (index === -1) throw new Error('Task not found')
    tasks.splice(index, 1)
    return { data: { message: 'Task deleted successfully' } }
  },

  async getSummary() {
    const selesai = tasks.filter((task) => task.status === 'selesai').length
    const sedang_dikerjakan = tasks.filter((task) => task.status === 'sedang_dikerjakan').length
    const belum_selesai = tasks.filter((task) => task.status !== 'selesai').length
    return {
      data: {
        total: tasks.length,
        selesai,
        sedang_dikerjakan,
        belum_selesai
      }
    }
  }
}

export default taskAPI
