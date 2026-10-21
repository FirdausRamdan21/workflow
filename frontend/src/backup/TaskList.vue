<template>
  <div class="task-list">
    <div class="list-header">
      <h2>Daftar Tugas</h2>
      <button class="btn btn-primary" @click="showCreateModal = true">
        ➕ Tambah Tugas Baru
      </button>
    </div>

    <div class="filters card">
      <div class="filter-group">
        <label>Filter Status:</label>
        <div class="filter-buttons">
          <button 
            v-for="status in statuses" 
            :key="status" 
            class="filter-btn"
            :class="{ active: selectedStatus === status }"
            @click="selectedStatus = selectedStatus === status ? '' : status"
          >
            {{ statusLabels[status] }}
          </button>
        </div>
      </div>
      <button class="btn btn-secondary" @click="refreshTasks">🔄 Refresh</button>
    </div>

    <div v-if="loading" class="text-center mt-20">
      <div class="spinner"></div>
      <p class="text-muted mt-20">Memuat tugas...</p>
    </div>

    <div v-else-if="filteredTasks.length === 0" class="empty-state card">
      <p class="empty-icon">📭</p>
      <p class="empty-text">Tidak ada tugas. Mulai dengan menambah tugas baru!</p>
    </div>

    <div v-else class="tasks-container">
      <div v-for="task in filteredTasks" :key="task.id" class="task-card card">
        <div class="task-header">
          <div>
            <h3>{{ task.nama }}</h3>
            <p class="task-desc">{{ task.deskripsi || '(Tanpa deskripsi)' }}</p>
          </div>
          <div class="task-badges">
            <span :class="['badge', statusBadgeClass(task.status)]">
              {{ statusLabels[task.status] }}
            </span>
            <span :class="['badge', priorityBadgeClass(task.prioritas)]">
              {{ priorityLabels[task.prioritas] }}
            </span>
          </div>
        </div>

        <div class="task-body">
          <div class="deadline">
            <span class="deadline-icon">📅</span>
            <span>{{ formatDate(task.deadline) }}</span>
          </div>
        </div>

        <div class="task-actions">
          <select class="status-select" v-model="task.status" @change="updateTaskStatus(task)">
            <option value="belum_dikerjakan">Belum Dikerjakan</option>
            <option value="sedang_dikerjakan">Sedang Dikerjakan</option>
            <option value="selesai">Selesai</option>
          </select>
          <button class="btn btn-danger" @click="confirmDelete(task.id)">
            🗑️ Hapus
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h2>Tambah Tugas Baru</h2>
          <button class="modal-close" @click="closeModal">✕</button>
        </div>

        <form @submit.prevent="createTask">
          <div class="form-group">
            <label>Nama Tugas *</label>
            <input v-model="form.nama" type="text" placeholder="Contoh: Tugas Matematika" required>
          </div>

          <div class="form-group">
            <label>Deskripsi</label>
            <textarea v-model="form.deskripsi" placeholder="Tulis deskripsi tugas..." rows="3"></textarea>
          </div>

          <div class="form-group">
            <label>Deadline *</label>
            <input v-model="form.deadline" type="date" required>
          </div>

          <div class="form-group">
            <label>Prioritas *</label>
            <select v-model="form.prioritas" required>
              <option value="">-- Pilih Prioritas --</option>
              <option value="rendah">Rendah</option>
              <option value="sedang">Sedang</option>
              <option value="tinggi">Tinggi</option>
            </select>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary" :disabled="formLoading">
              <span v-if="formLoading" class="spinner" style="margin-right: 10px;"></span>
              Simpan Tugas
            </button>
            <button type="button" class="btn btn-outline" @click="closeModal">Batal</button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="error" class="error-message card mt-20">
      <p>⚠️ {{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { taskAPI } from '../api'

const tasks = ref([])
const loading = ref(false)
const formLoading = ref(false)
const error = ref(null)
const showCreateModal = ref(false)
const selectedStatus = ref('')

const form = ref({
  nama: '',
  deskripsi: '',
  deadline: '',
  prioritas: ''
})

const statuses = ['belum_dikerjakan', 'sedang_dikerjakan', 'selesai']
const statusLabels = {
  'belum_dikerjakan': 'Belum Dikerjakan',
  'sedang_dikerjakan': 'Sedang Dikerjakan',
  'selesai': 'Selesai'
}

const priorityLabels = {
  'rendah': 'Rendah',
  'sedang': 'Sedang',
  'tinggi': 'Tinggi'
}

const filteredTasks = computed(() => {
  if (!selectedStatus.value) return tasks.value
  return tasks.value.filter(task => task.status === selectedStatus.value)
})

const fetchTasks = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await taskAPI.getTasks()
    tasks.value = response.data.data
  } catch (err) {
    error.value = 'Gagal memuat tugas. Pastikan backend sedang running.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const createTask = async () => {
  if (!form.value.nama || !form.value.deadline || !form.value.prioritas) {
    error.value = 'Mohon isi semua field yang wajib diisi.'
    return
  }

  formLoading.value = true
  error.value = null
  try {
    await taskAPI.createTask(form.value)
    resetForm()
    showCreateModal.value = false
    fetchTasks()
  } catch (err) {
    error.value = 'Gagal membuat tugas: ' + (err.response?.data?.message || err.message)
    console.error(err)
  } finally {
    formLoading.value = false
  }
}

const updateTaskStatus = async (task) => {
  try {
    await taskAPI.updateTask(task.id, { status: task.status })
    fetchTasks()
  } catch (err) {
    error.value = 'Gagal mengubah status tugas.'
    console.error(err)
    fetchTasks() // Revert
  }
}

const confirmDelete = async (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
    try {
      await taskAPI.deleteTask(id)
      fetchTasks()
    } catch (err) {
      error.value = 'Gagal menghapus tugas.'
      console.error(err)
    }
  }
}

const resetForm = () => {
  form.value = {
    nama: '',
    deskripsi: '',
    deadline: '',
    prioritas: ''
  }
}

const closeModal = () => {
  showCreateModal.value = false
  resetForm()
}

const refreshTasks = () => {
  fetchTasks()
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
}

const statusBadgeClass = (status) => {
  const classes = {
    'belum_dikerjakan': 'badge-light',
    'sedang_dikerjakan': 'badge-warning',
    'selesai': 'badge-success'
  }
  return classes[status] || 'badge-light'
}

const priorityBadgeClass = (priority) => {
  const classes = {
    'rendah': 'badge-info',
    'sedang': 'badge-warning',
    'tinggi': 'badge-danger'
  }
  return classes[priority] || 'badge-light'
}

onMounted(() => {
  fetchTasks()
})
</script>

<style scoped>
.task-list {
  padding: 10px 0;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.list-header h2 {
  font-size: 28px;
  color: #333;
}

.filters {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  margin-bottom: 20px;
}

.filter-group {
  flex: 1;
}

.filter-group label {
  display: block;
  margin-bottom: 10px;
  font-weight: 600;
  font-size: 14px;
}

.filter-buttons {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.filter-btn {
  padding: 8px 16px;
  border: 2px solid #ddd;
  background: white;
  border-radius: 20px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.filter-btn:hover {
  border-color: #667eea;
  color: #667eea;
}

.filter-btn.active {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

.tasks-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.task-card {
  display: flex;
  flex-direction: column;
  gap: 15px;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.task-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.task-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 10px;
}

.task-header h3 {
  font-size: 16px;
  color: #333;
  margin-bottom: 5px;
}

.task-desc {
  font-size: 13px;
  color: #999;
  line-height: 1.5;
}

.task-badges {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.task-body {
  padding: 10px 0;
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
}

.deadline {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #666;
}

.deadline-icon {
  font-size: 16px;
}

.task-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.status-select {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 12px;
  cursor: pointer;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
}

.empty-icon {
  font-size: 48px;
  margin-bottom: 20px;
}

.empty-text {
  color: #999;
  font-size: 14px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  font-size: 14px;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
  font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.error-message {
  background: #ffebee;
  border-left: 4px solid #f44336;
  color: #c62828;
}

@media (max-width: 768px) {
  .list-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }

  .filters {
    flex-direction: column;
    align-items: flex-start;
  }

  .tasks-container {
    grid-template-columns: 1fr;
  }

  .task-actions {
    grid-template-columns: 1fr;
  }
}
</style>
