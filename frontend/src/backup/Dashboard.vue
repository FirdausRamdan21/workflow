<template>
  <div class="dashboard">
    <div class="dashboard-header">
      <h2>Dashboard</h2>
      <button class="btn btn-primary" @click="$emit('view-change', 'tasks')">
        ➕ Lihat Semua Tugas
      </button>
    </div>

    <div v-if="loading" class="text-center mt-20">
      <div class="spinner"></div>
      <p class="text-muted mt-20">Memuat data...</p>
    </div>

    <div v-else class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-content">
          <p class="stat-label">Total Tugas</p>
          <p class="stat-value">{{ summary.total }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
          <p class="stat-label">Selesai</p>
          <p class="stat-value">{{ summary.selesai }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-content">
          <p class="stat-label">Sedang Dikerjakan</p>
          <p class="stat-value">{{ summary.sedang_dikerjakan }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">❌</div>
        <div class="stat-content">
          <p class="stat-label">Belum Selesai</p>
          <p class="stat-value">{{ summary.belum_selesai }}</p>
        </div>
      </div>
    </div>

    <div class="quick-actions card mt-20">
      <div class="card-header">
        <h3>Aksi Cepat</h3>
      </div>
      <div class="actions-grid">
        <button class="btn btn-primary" @click="$emit('view-change', 'tasks')">
          📋 Daftar Tugas
        </button>
        <button class="btn btn-secondary" @click="refreshSummary">
          🔄 Refresh Data
        </button>
      </div>
    </div>

    <div v-if="error" class="error-message card mt-20">
      <p>⚠️ {{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { taskAPI } from '../api'

const summary = ref({
  total: 0,
  selesai: 0,
  sedang_dikerjakan: 0,
  belum_selesai: 0
})

const loading = ref(false)
const error = ref(null)

const fetchSummary = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await taskAPI.getSummary()
    summary.value = response.data
  } catch (err) {
    error.value = 'Gagal memuat data. Pastikan backend sedang running.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const refreshSummary = () => {
  fetchSummary()
}

onMounted(() => {
  fetchSummary()
})
</script>

<style scoped>
.dashboard {
  padding: 10px 0;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.dashboard-header h2 {
  font-size: 28px;
  color: #333;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 15px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.stat-icon {
  font-size: 32px;
  min-width: 50px;
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 12px;
  color: #999;
  margin-bottom: 5px;
  text-transform: uppercase;
  font-weight: 600;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #667eea;
}

.quick-actions {
  background: white;
  border-radius: 8px;
  padding: 20px;
}

.quick-actions h3 {
  font-size: 18px;
  margin-bottom: 15px;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 10px;
}

.error-message {
  background: #ffebee;
  border-left: 4px solid #f44336;
  color: #c62828;
}

.mt-20 {
  margin-top: 20px;
}

@media (max-width: 768px) {
  .dashboard-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>
