<template>
  <section class="task-page">
    <header class="page-header">
      <div>
        <p class="eyebrow">RUANG KERJA</p>
        <h1>Daftar Tugas</h1>
        <p class="page-description">Atur prioritas dan pantau deadline tugasmu.</p>
      </div>
      <button class="add-button" @click="showModal = true">
        <span aria-hidden="true">＋</span>
        Tambah tugas
      </button>
    </header>

    <section class="task-panel">
      <div class="toolbar">
        <label class="search-box">
          <span aria-hidden="true">⌕</span>
          <input v-model="searchQuery" type="search" placeholder="Cari tugas...">
        </label>
        <div class="filters" aria-label="Filter status">
          <button
            v-for="status in statusOptions"
            :key="status.value"
            class="filter-button"
            :class="{ active: selectedStatus === status.value }"
            :aria-pressed="selectedStatus === status.value"
            @click="selectedStatus = status.value"
          >
            {{ status.label }}
          </button>
        </div>
      </div>

      <div v-if="noticeMessage" class="operation-notice" role="alert">
        <span>{{ noticeMessage }}</span>
        <button aria-label="Tutup pesan" @click="noticeMessage = ''">×</button>
      </div>
      <div v-if="errorMessage" class="error-message" role="alert">
        <span>{{ errorMessage }}</span>
        <button class="retry-button" @click="fetchTasks">Coba lagi</button>
      </div>
      <div v-else-if="loading" class="list-message" role="status">Memuat daftar tugas...</div>
      <div v-else-if="filteredTasks.length === 0" class="list-message empty-message">
        <span aria-hidden="true">▤</span>
        <strong>{{ tasks.length ? 'Tugas tidak ditemukan' : 'Belum ada tugas' }}</strong>
        <p>{{ tasks.length ? 'Coba ubah kata kunci atau filter status.' : 'Tambahkan tugas pertamamu untuk mulai.' }}</p>
        <button v-if="!tasks.length" class="text-button" @click="showModal = true">Tambah tugas</button>
      </div>
      <div v-else class="task-list">
        <article v-for="task in filteredTasks" :key="task.id" class="task-row" :class="`priority-${task.prioritas}`">
          <span class="priority-indicator" aria-hidden="true"></span>
          <div class="task-info">
            <h2>{{ task.nama }}</h2>
            <p>{{ task.deskripsi || 'Tidak ada deskripsi' }}</p>
          </div>
          <div class="task-meta">
            <span class="priority-label" :class="`priority-label-${task.prioritas}`">
              Prioritas {{ priorityLabels[task.prioritas] || task.prioritas }}
            </span>
            <time :datetime="dateKey(task.deadline)">
              <span aria-hidden="true">▦</span>
              {{ formatDate(task.deadline) }}
            </time>
          </div>
          <div class="task-actions">
            <label class="sr-only" :for="`status-${task.id}`">Status {{ task.nama }}</label>
            <select :id="`status-${task.id}`" v-model="task.status" @change="updateStatus(task)">
              <option value="belum_dikerjakan">Belum dikerjakan</option>
              <option value="sedang_dikerjakan">Sedang dikerjakan</option>
              <option value="selesai">Selesai</option>
            </select>
            <button class="delete-button" :aria-label="`Hapus ${task.nama}`" @click="deleteTask(task.id)">×</button>
          </div>
        </article>
      </div>
      <footer v-if="!loading && !errorMessage && tasks.length" class="list-footer">
        Menampilkan {{ filteredTasks.length }} dari {{ tasks.length }} tugas
      </footer>
    </section>

    <div v-if="showModal" class="modal" @click.self="closeModal">
      <section class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <header class="modal-header">
          <div>
            <p class="eyebrow">TUGAS BARU</p>
            <h2 id="modal-title">Tambah tugas</h2>
          </div>
          <button class="modal-close" aria-label="Tutup" @click="closeModal">×</button>
        </header>
        <form @submit.prevent="createTask">
          <label>
            Nama tugas
            <input v-model.trim="form.nama" type="text" maxlength="255" placeholder="Contoh: Kerjakan latihan matematika" required>
          </label>
          <label>
            Deskripsi <span class="optional-label">Opsional</span>
            <textarea v-model.trim="form.deskripsi" rows="3" placeholder="Tambahkan catatan tugas"></textarea>
          </label>
          <div class="form-row">
            <label>
              Deadline
              <input v-model="form.deadline" type="date" required>
            </label>
            <label>
              Prioritas
              <select v-model="form.prioritas" required>
                <option value="" disabled>Pilih prioritas</option>
                <option value="rendah">Rendah</option>
                <option value="sedang">Sedang</option>
                <option value="tinggi">Tinggi</option>
              </select>
            </label>
          </div>
          <p v-if="formError" class="form-error" role="alert">{{ formError }}</p>
          <div class="modal-actions">
            <button type="button" class="cancel-button" :disabled="saving" @click="closeModal">Batal</button>
            <button type="submit" class="save-button" :disabled="saving">
              {{ saving ? 'Menyimpan...' : 'Simpan tugas' }}
            </button>
          </div>
        </form>
      </section>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { taskAPI } from '../api'

const tasks = ref([])
const showModal = ref(false)
const selectedStatus = ref('')
const searchQuery = ref('')
const loading = ref(false)
const saving = ref(false)
const errorMessage = ref('')
const noticeMessage = ref('')
const formError = ref('')
const form = ref({ nama: '', deskripsi: '', deadline: '', prioritas: '' })

const statusOptions = [
  { value: '', label: 'Semua' },
  { value: 'belum_dikerjakan', label: 'Belum' },
  { value: 'sedang_dikerjakan', label: 'Proses' },
  { value: 'selesai', label: 'Selesai' }
]

const priorityLabels = { rendah: 'Rendah', sedang: 'Sedang', tinggi: 'Tinggi' }

const filteredTasks = computed(() => tasks.value.filter((task) =>
  (!selectedStatus.value || task.status === selectedStatus.value) &&
  (!searchQuery.value || task.nama.toLowerCase().includes(searchQuery.value.toLowerCase()))
))

const dateKey = (value) => String(value ?? '').slice(0, 10)

const formatDate = (value) => {
  const key = dateKey(value)
  if (!key) return '-'
  const [year, month, day] = key.split('-').map(Number)
  return new Date(year, month - 1, day).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const fetchTasks = async () => {
  loading.value = true
  errorMessage.value = ''
  noticeMessage.value = ''
  try {
    const { data } = await taskAPI.getTasks()
    tasks.value = data.data
  } catch (error) {
    console.error('Failed to load tasks:', error)
    errorMessage.value = 'Daftar tugas gagal dimuat. Muat ulang halaman lalu coba lagi.'
  } finally {
    loading.value = false
  }
}

const createTask = async () => {
  if (saving.value) return
  saving.value = true
  formError.value = ''
  try {
    await taskAPI.createTask(form.value)
    closeModal()
    await fetchTasks()
  } catch (error) {
    console.error('Failed to create task:', error)
    formError.value = 'Tugas gagal disimpan. Periksa kembali isian lalu coba lagi.'
  } finally {
    saving.value = false
  }
}

const updateStatus = async (task) => {
  try {
    await taskAPI.updateTask(task.id, { status: task.status })
    await fetchTasks()
  } catch (error) {
    console.error('Failed to update task status:', error)
    await fetchTasks()
    noticeMessage.value = 'Status tugas gagal diperbarui. Daftar dimuat ulang.'
  }
}

const deleteTask = async (id) => {
  if (!window.confirm('Hapus tugas ini?')) return
  try {
    await taskAPI.deleteTask(id)
    await fetchTasks()
  } catch (error) {
    console.error('Failed to delete task:', error)
    noticeMessage.value = 'Tugas gagal dihapus. Muat ulang halaman lalu coba lagi.'
  }
}

const closeModal = () => {
  showModal.value = false
  formError.value = ''
  form.value = { nama: '', deskripsi: '', deadline: '', prioritas: '' }
}

onMounted(fetchTasks)
</script>

<style scoped>
.task-page {
  width: min(100%, 1120px);
  margin: 0 auto;
}

.page-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 26px;
}

.eyebrow {
  margin-bottom: 5px;
  color: var(--teal);
  font-size: 10px;
  font-weight: 750;
  letter-spacing: 0.13em;
}

.page-header h1 {
  margin: 0 0 5px;
  color: var(--ink);
  font-size: clamp(24px, 3vw, 30px);
  letter-spacing: -0.04em;
}

.page-description {
  margin: 0;
  color: var(--muted);
  font-size: 12px;
}

.add-button,
.save-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 11px 15px;
  border: 0;
  border-radius: 8px;
  background: var(--teal-dark);
  color: white;
  font: inherit;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
}

.add-button:hover,
.save-button:hover:not(:disabled) {
  background: #143f3c;
}

.add-button span {
  font-size: 18px;
  line-height: 0.7;
}

.task-panel {
  overflow: hidden;
  border: 1px solid var(--line);
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 8px 24px rgba(31, 69, 65, 0.04);
}

.toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px;
  border-bottom: 1px solid var(--line);
}

.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  width: min(100%, 260px);
  padding: 8px 11px;
  border: 1px solid var(--line);
  border-radius: 7px;
  color: var(--muted);
}

.search-box:focus-within {
  border-color: var(--teal);
  box-shadow: 0 0 0 3px rgba(39, 105, 99, 0.1);
}

.search-box > span {
  font-size: 19px;
  line-height: 0.7;
}

.search-box input {
  width: 100%;
  border: 0;
  outline: 0;
  background: transparent;
  color: var(--ink);
  font: inherit;
  font-size: 11px;
}

.filters {
  display: flex;
  align-items: center;
  gap: 5px;
}

.filter-button {
  padding: 8px 10px;
  border: 0;
  border-radius: 6px;
  background: transparent;
  color: var(--muted);
  font: inherit;
  font-size: 10px;
  font-weight: 650;
  white-space: nowrap;
  cursor: pointer;
}

.filter-button:hover,
.filter-button.active {
  background: #e7f1ef;
  color: var(--teal-dark);
}

.task-list {
  display: grid;
}

.task-row {
  position: relative;
  display: grid;
  grid-template-columns: minmax(150px, 1fr) minmax(145px, 190px) auto;
  align-items: center;
  gap: 16px;
  min-height: 78px;
  padding: 14px 18px;
  border-bottom: 1px solid #eef1f0;
}

.task-row:last-child {
  border-bottom: 0;
}

.priority-indicator {
  position: absolute;
  inset: 0 auto 0 0;
  width: 3px;
  background: var(--teal);
}

.priority-tinggi .priority-indicator {
  background: var(--coral);
}

.priority-sedang .priority-indicator {
  background: var(--lavender);
}

.task-info {
  min-width: 0;
  padding-left: 3px;
}

.task-info h2 {
  overflow: hidden;
  margin: 0 0 4px;
  color: var(--ink);
  font-size: 12px;
  font-weight: 700;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.task-info p {
  overflow: hidden;
  margin: 0;
  color: var(--muted);
  font-size: 10px;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.task-meta {
  display: grid;
  justify-items: start;
  gap: 6px;
}

.priority-label {
  padding: 4px 7px;
  border-radius: 99px;
  background: #e9f2f0;
  color: var(--teal);
  font-size: 9px;
  font-weight: 700;
}

.priority-label-tinggi {
  background: #fff0e9;
  color: #d75e3e;
}

.priority-label-sedang {
  background: #efedfa;
  color: #7169ad;
}

.task-meta time {
  display: flex;
  align-items: center;
  gap: 5px;
  color: var(--muted);
  font-size: 10px;
}

.task-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.task-actions select,
.modal-content input,
.modal-content textarea,
.modal-content select {
  border: 1px solid var(--line);
  border-radius: 7px;
  background: white;
  color: var(--ink);
  font: inherit;
  font-size: 10px;
}

.task-actions select {
  max-width: 145px;
  padding: 8px;
}

.delete-button,
.modal-close {
  display: grid;
  width: 30px;
  height: 30px;
  flex: 0 0 auto;
  place-items: center;
  border: 0;
  border-radius: 7px;
  background: transparent;
  color: #9b5b57;
  font: inherit;
  font-size: 20px;
  cursor: pointer;
}

.delete-button:hover,
.modal-close:hover {
  background: #fff0ef;
}

.list-footer {
  padding: 12px 18px;
  border-top: 1px solid var(--line);
  color: var(--muted);
  font-size: 10px;
}

.list-message {
  padding: 40px 18px;
  color: var(--muted);
  font-size: 12px;
  text-align: center;
}

.empty-message > span {
  display: grid;
  width: 38px;
  height: 38px;
  place-items: center;
  margin: 0 auto 10px;
  border-radius: 11px;
  background: #e7f1ef;
  color: var(--teal);
  font-size: 19px;
}

.empty-message strong {
  display: block;
  color: var(--ink);
  font-size: 13px;
}

.empty-message p {
  margin: 5px 0 10px;
}

.text-button,
.retry-button {
  padding: 0;
  border: 0;
  background: transparent;
  color: var(--teal);
  font: inherit;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
}

.error-message {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 18px;
  background: #fff7f6;
  color: #a13932;
  font-size: 11px;
}

.operation-notice {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 11px 16px;
  border-bottom: 1px solid #f2c4c1;
  background: #fff7f6;
  color: #a13932;
  font-size: 11px;
}

.operation-notice button {
  display: grid;
  width: 24px;
  height: 24px;
  flex: 0 0 auto;
  place-items: center;
  border: 0;
  border-radius: 6px;
  background: transparent;
  color: inherit;
  font: inherit;
  font-size: 18px;
  cursor: pointer;
}

.operation-notice button:hover {
  background: #ffe9e7;
}

.modal {
  position: fixed;
  z-index: 10;
  inset: 0;
  display: grid;
  place-items: center;
  padding: 20px;
  background: rgba(12, 37, 35, 0.48);
}

.modal-content {
  width: min(100%, 480px);
  max-height: min(90vh, 720px);
  overflow-y: auto;
  padding: 24px;
  border-radius: 14px;
  background: #fff;
  box-shadow: 0 22px 70px rgba(13, 42, 39, 0.2);
}

.modal-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 20px;
}

.modal-header h2 {
  margin: 0;
  color: var(--ink);
  font-size: 20px;
  letter-spacing: -0.03em;
}

.modal-close {
  width: 28px;
  height: 28px;
  color: var(--muted);
  font-size: 24px;
}

.modal-content form {
  display: grid;
  gap: 15px;
}

.modal-content label {
  display: grid;
  gap: 7px;
  color: var(--ink);
  font-size: 11px;
  font-weight: 700;
}

.modal-content input,
.modal-content textarea,
.modal-content select {
  width: 100%;
  padding: 10px 11px;
  font-size: 12px;
}

.modal-content textarea {
  resize: vertical;
}

.modal-content input:focus,
.modal-content textarea:focus,
.modal-content select:focus {
  outline: 2px solid rgba(39, 105, 99, 0.25);
  border-color: var(--teal);
}

.optional-label {
  color: var(--muted);
  font-size: 10px;
  font-weight: 400;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
}

.form-error {
  margin: 0;
  color: #a13932;
  font-size: 11px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 9px;
  margin-top: 5px;
}

.cancel-button {
  padding: 10px 13px;
  border: 1px solid var(--line);
  border-radius: 8px;
  background: white;
  color: var(--ink);
  font: inherit;
  font-size: 11px;
  font-weight: 650;
  cursor: pointer;
}

.cancel-button:hover {
  background: var(--page-bg);
}

.save-button {
  padding-inline: 15px;
  font-size: 11px;
}

.save-button:disabled,
.cancel-button:disabled {
  cursor: wait;
  opacity: 0.65;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  clip-path: inset(50%);
}

@media (max-width: 760px) {
  .page-header {
    align-items: flex-start;
  }

  .toolbar {
    align-items: stretch;
    flex-direction: column;
  }

  .search-box {
    width: 100%;
  }

  .filters {
    overflow-x: auto;
  }

  .task-row {
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 10px;
  }

  .task-meta {
    grid-column: 1;
    grid-row: 2;
    display: flex;
    align-items: center;
    gap: 9px;
    padding-left: 3px;
  }

  .task-actions {
    grid-column: 2;
    grid-row: 1 / span 2;
  }
}

@media (max-width: 480px) {
  .page-header {
    gap: 10px;
  }

  .add-button {
    padding: 10px;
    font-size: 0;
  }

  .add-button span {
    font-size: 20px;
  }

  .task-actions {
    gap: 4px;
  }

  .task-actions select {
    max-width: 112px;
    padding: 7px 5px;
    font-size: 9px;
  }

  .delete-button {
    width: 26px;
    height: 26px;
  }

  .modal-content {
    padding: 19px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
