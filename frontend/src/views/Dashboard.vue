<template>
  <div class="dashboard-layout">
    <section class="dashboard-content">
      <header class="dashboard-header">
        <div>
          <p class="eyebrow">{{ todayLabel }}</p>
          <h1>Dashboard Tugas</h1>
          <p class="greeting">Ringkasan tugas dan deadline kamu.</p>
        </div>
        <button class="add-task-button" @click="$emit('view-change', 'tasks')">
          <span aria-hidden="true">＋</span>
          Kelola tugas
        </button>
      </header>

      <div v-if="errorMessage" class="feedback error-feedback" role="alert">
        <span>{{ errorMessage }}</span>
        <button class="retry-button" @click="fetchTasks">Coba lagi</button>
      </div>
      <div v-else-if="loading" class="feedback" role="status">Memuat ringkasan tugas...</div>
      <template v-else>
        <section class="status-cards" aria-label="Ringkasan status tugas">
          <article
            v-for="status in statuses"
            :key="status.key"
            class="status-card"
            :class="`status-${status.key}`"
          >
            <div class="status-card-top">
              <span class="status-label">{{ status.label }}</span>
              <span class="status-symbol" aria-hidden="true">{{ status.symbol }}</span>
            </div>
            <strong class="status-count">{{ status.count }}</strong>
            <div class="progress-track" aria-hidden="true">
              <span :style="{ width: `${progress(status.count)}%` }"></span>
            </div>
            <span class="status-caption">{{ progress(status.count) }}% dari semua tugas</span>
          </article>
        </section>

        <div class="section-heading summary-heading">
          <div>
            <h2>Ringkasan</h2>
            <p>Kondisi tugas saat ini</p>
          </div>
        </div>
        <section class="summary-grid" aria-label="Statistik tugas">
          <article v-for="item in summaryItems" :key="item.label" class="summary-card">
            <span class="summary-icon" :class="`summary-${item.tone}`" aria-hidden="true">
              {{ item.symbol }}
            </span>
            <div>
              <strong>{{ item.value }}</strong>
              <span>{{ item.label }}</span>
            </div>
          </article>
        </section>

        <section class="upcoming-section">
          <div class="section-heading">
            <div>
              <h2>Tugas berikutnya</h2>
              <p>Deadline terdekat yang belum selesai</p>
            </div>
            <button class="text-button" @click="$emit('view-change', 'tasks')">Lihat semua</button>
          </div>

          <div v-if="upcomingTasks.length" class="upcoming-list">
            <article
              v-for="task in upcomingTasks"
              :key="task.id"
              class="upcoming-item"
              :class="`priority-${task.prioritas}`"
            >
              <span class="priority-bar" aria-hidden="true"></span>
              <div class="upcoming-details">
                <strong>{{ task.nama }}</strong>
                <span>{{ task.deskripsi || 'Tidak ada deskripsi' }}</span>
              </div>
              <span class="task-status" :class="`badge-${task.status}`">
                {{ statusLabel(task.status) }}
              </span>
              <time class="upcoming-date" :datetime="dateKey(task.deadline)">
                {{ formatDate(task.deadline, { day: 'numeric', month: 'short' }) }}
              </time>
            </article>
          </div>
          <div v-else class="empty-state">
            <span aria-hidden="true">✓</span>
            <strong>{{ tasks.length ? 'Semua tugas sudah selesai' : 'Belum ada tugas' }}</strong>
            <p>{{ tasks.length ? 'Tidak ada deadline aktif dalam waktu dekat.' : 'Tambahkan tugas untuk mulai mengatur pekerjaanmu.' }}</p>
            <button v-if="!tasks.length" class="text-button" @click="$emit('view-change', 'tasks')">
              Tambah tugas
            </button>
          </div>
        </section>
      </template>
    </section>

    <aside class="agenda-panel">
      <div class="agenda-heading">
        <span class="calendar-icon" aria-hidden="true">▦</span>
        <div>
          <h2>Agenda deadline</h2>
          <p>{{ monthLabel }}</p>
        </div>
      </div>

      <div v-if="!loading && !errorMessage && agendaGroups.length" class="agenda-list">
        <section v-for="group in agendaGroups" :key="group.date" class="agenda-day">
          <h3>{{ formatDate(group.date, { weekday: 'short', day: 'numeric', month: 'short' }) }}</h3>
          <div
            v-for="task in group.tasks"
            :key="task.id"
            class="agenda-task"
            :class="`priority-${task.prioritas}`"
          >
            <span class="agenda-dot" aria-hidden="true"></span>
            <div>
              <strong>{{ task.nama }}</strong>
              <span>{{ statusLabel(task.status) }} · {{ priorityLabel(task.prioritas) }}</span>
            </div>
          </div>
        </section>
      </div>
      <div v-else-if="loading" class="agenda-empty">Memuat agenda...</div>
      <div v-else-if="errorMessage" class="agenda-empty">Agenda belum dapat dimuat.</div>
      <div v-else class="agenda-empty">Belum ada deadline aktif mendatang.</div>
    </aside>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { taskAPI } from '../api'

defineEmits(['view-change'])

const tasks = ref([])
const loading = ref(false)
const errorMessage = ref('')

const todayKey = () => {
  const now = new Date()
  const localDate = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
  return localDate.toISOString().slice(0, 10)
}

const dateKey = (value) => String(value ?? '').slice(0, 10)

const formatDate = (value, options) => {
  const key = dateKey(value)
  if (!key) return '-'
  const [year, month, day] = key.split('-').map(Number)
  return new Date(year, month - 1, day).toLocaleDateString('id-ID', options)
}

const todayLabel = computed(() => formatDate(todayKey(), { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }))
const monthLabel = computed(() => formatDate(todayKey(), { month: 'long', year: 'numeric' }))

const counts = computed(() => ({
  belum_dikerjakan: tasks.value.filter((task) => task.status === 'belum_dikerjakan').length,
  sedang_dikerjakan: tasks.value.filter((task) => task.status === 'sedang_dikerjakan').length,
  selesai: tasks.value.filter((task) => task.status === 'selesai').length
}))

const statuses = computed(() => [
  { key: 'belum_dikerjakan', label: 'Belum dikerjakan', count: counts.value.belum_dikerjakan, symbol: '○' },
  { key: 'sedang_dikerjakan', label: 'Sedang dikerjakan', count: counts.value.sedang_dikerjakan, symbol: '◷' },
  { key: 'selesai', label: 'Selesai', count: counts.value.selesai, symbol: '✓' }
])

const overdueTasks = computed(() =>
  tasks.value.filter((task) => task.status !== 'selesai' && dateKey(task.deadline) < todayKey())
)

const dueSoonTasks = computed(() =>
  tasks.value.filter((task) => {
    const dueDate = dateKey(task.deadline)
    if (task.status === 'selesai' || dueDate < todayKey()) return false
    const dueTime = new Date(`${dueDate}T00:00:00`).getTime()
    const todayTime = new Date(`${todayKey()}T00:00:00`).getTime()
    return (dueTime - todayTime) / 86400000 <= 7
  })
)

const summaryItems = computed(() => [
  { label: 'Semua tugas', value: tasks.value.length, symbol: '▤', tone: 'teal' },
  { label: 'Deadline 7 hari', value: dueSoonTasks.value.length, symbol: '◷', tone: 'orange' },
  { label: 'Terlambat', value: overdueTasks.value.length, symbol: '!', tone: 'red' },
  { label: 'Prioritas tinggi', value: tasks.value.filter((task) => task.prioritas === 'tinggi' && task.status !== 'selesai').length, symbol: '↑', tone: 'purple' }
])

const upcomingTasks = computed(() =>
  tasks.value
    .filter((task) => task.status !== 'selesai' && dateKey(task.deadline) >= todayKey())
    .slice(0, 4)
)

const agendaGroups = computed(() => {
  const groups = new Map()
  tasks.value
    .filter((task) => task.status !== 'selesai' && dateKey(task.deadline) >= todayKey())
    .forEach((task) => {
      const date = dateKey(task.deadline)
      if (!groups.has(date)) groups.set(date, [])
      groups.get(date).push(task)
    })
  return [...groups.entries()].slice(0, 5).map(([date, groupTasks]) => ({ date, tasks: groupTasks }))
})

const progress = (count) => tasks.value.length ? Math.round((count / tasks.value.length) * 100) : 0

const statusLabel = (status) => ({
  belum_dikerjakan: 'Belum',
  sedang_dikerjakan: 'Proses',
  selesai: 'Selesai'
}[status] ?? status)

const priorityLabel = (priority) => ({
  rendah: 'Rendah',
  sedang: 'Sedang',
  tinggi: 'Tinggi'
}[priority] ?? priority)

const fetchTasks = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const { data } = await taskAPI.getTasks()
    tasks.value = data.data
  } catch (error) {
    console.error('Failed to load dashboard tasks:', error)
    errorMessage.value = 'Ringkasan tugas gagal dimuat. Periksa koneksi ke server lalu coba lagi.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchTasks)
</script>

<style scoped>
.dashboard-layout {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 250px;
  gap: 28px;
  max-width: 1460px;
  margin: 0 auto;
}

.dashboard-content {
  min-width: 0;
}

.dashboard-header,
.section-heading,
.status-card-top,
.agenda-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.dashboard-header {
  margin-bottom: 28px;
}

.eyebrow {
  margin-bottom: 5px;
  color: var(--teal);
  font-size: 12px;
  font-weight: 700;
  text-transform: capitalize;
}

h1,
h2,
h3,
p {
  margin-top: 0;
}

.dashboard-header h1 {
  margin-bottom: 5px;
  color: var(--ink);
  font-size: clamp(24px, 3vw, 30px);
  letter-spacing: -0.04em;
}

.greeting,
.section-heading p,
.agenda-heading p {
  margin-bottom: 0;
  color: var(--muted);
  font-size: 12px;
}

.add-task-button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  flex: 0 0 auto;
  padding: 11px 15px;
  border: 0;
  border-radius: 8px;
  background: var(--teal-dark);
  color: #fff;
  font: inherit;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: background 150ms ease, transform 150ms ease;
}

.add-task-button:hover {
  transform: translateY(-1px);
  background: #143f3c;
}

.add-task-button span {
  font-size: 18px;
  line-height: 0.7;
}

.status-cards {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}

.status-card {
  min-width: 0;
  padding: 17px;
  border-radius: 11px;
  background: var(--teal);
  color: #fff;
}

.status-sedang_dikerjakan {
  background: var(--coral);
}

.status-selesai {
  background: var(--lavender);
}

.status-label {
  font-size: 12px;
  font-weight: 650;
}

.status-symbol {
  display: grid;
  width: 25px;
  height: 25px;
  place-items: center;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  font-size: 14px;
}

.status-count {
  display: block;
  margin: 14px 0 12px;
  font-size: 28px;
  line-height: 1;
}

.progress-track {
  height: 5px;
  overflow: hidden;
  border-radius: 9px;
  background: rgba(0, 0, 0, 0.2);
}

.progress-track span {
  display: block;
  height: 100%;
  border-radius: inherit;
  background: rgba(255, 255, 255, 0.92);
}

.status-caption {
  display: block;
  margin-top: 7px;
  color: rgba(255, 255, 255, 0.78);
  font-size: 10px;
}

.summary-heading {
  margin: 28px 0 14px;
}

.section-heading h2,
.agenda-heading h2 {
  margin-bottom: 4px;
  color: var(--ink);
  font-size: 15px;
  letter-spacing: -0.02em;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.summary-card {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 74px;
  padding: 13px;
  border: 1px solid var(--line);
  border-radius: 10px;
  background: #fff;
}

.summary-icon {
  display: grid;
  width: 36px;
  height: 36px;
  flex: 0 0 auto;
  place-items: center;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 700;
}

.summary-teal { background: #e3f1ef; color: var(--teal); }
.summary-orange { background: #fff0e9; color: #e66b43; }
.summary-red { background: #ffebeb; color: #d9534f; }
.summary-purple { background: #efedfa; color: #7b73b7; }

.summary-card div {
  display: grid;
  gap: 3px;
}

.summary-card strong {
  color: var(--ink);
  font-size: 18px;
}

.summary-card div span {
  color: var(--muted);
  font-size: 10px;
}

.upcoming-section {
  margin-top: 28px;
}

.text-button,
.retry-button {
  padding: 0;
  border: 0;
  background: none;
  color: var(--teal);
  font: inherit;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
}

.upcoming-list {
  display: grid;
  gap: 9px;
  margin-top: 14px;
}

.upcoming-item {
  position: relative;
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 65px;
  overflow: hidden;
  padding: 12px 14px;
  border: 1px solid var(--line);
  border-radius: 9px;
  background: #fff;
}

.priority-bar {
  position: absolute;
  inset: 0 auto 0 0;
  width: 4px;
  background: var(--teal);
}

.priority-tinggi .priority-bar,
.agenda-task.priority-tinggi .agenda-dot { background: var(--coral); }
.priority-sedang .priority-bar,
.agenda-task.priority-sedang .agenda-dot { background: var(--lavender); }
.priority-rendah .priority-bar,
.agenda-task.priority-rendah .agenda-dot { background: var(--teal); }

.upcoming-details {
  display: grid;
  min-width: 0;
  flex: 1;
  gap: 3px;
  padding-left: 4px;
}

.upcoming-details strong,
.agenda-task strong {
  overflow: hidden;
  color: var(--ink);
  font-size: 11px;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.upcoming-details > span {
  overflow: hidden;
  color: var(--muted);
  font-size: 10px;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.task-status {
  flex: 0 0 auto;
  padding: 5px 7px;
  border-radius: 99px;
  font-size: 9px;
  font-weight: 700;
}

.badge-belum_dikerjakan { background: #f0f2f1; color: #62706e; }
.badge-sedang_dikerjakan { background: #fff0e9; color: #d75e3e; }
.badge-selesai { background: #e6f3ea; color: #398550; }

.upcoming-date {
  flex: 0 0 auto;
  color: var(--muted);
  font-size: 10px;
}

.empty-state,
.feedback {
  margin-top: 16px;
  padding: 24px 18px;
  border: 1px dashed var(--line);
  border-radius: 10px;
  background: #fff;
  color: var(--muted);
  text-align: center;
}

.empty-state > span {
  display: grid;
  width: 34px;
  height: 34px;
  place-items: center;
  margin: 0 auto 10px;
  border-radius: 50%;
  background: #e6f3ea;
  color: #398550;
  font-weight: 700;
}

.empty-state strong {
  display: block;
  color: var(--ink);
  font-size: 12px;
}

.empty-state p {
  margin: 5px 0 10px;
  font-size: 11px;
}

.feedback {
  border-style: solid;
  font-size: 12px;
}

.error-feedback {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  border-color: #f2c4c1;
  background: #fff7f6;
  color: #a13932;
  text-align: left;
}

.agenda-panel {
  align-self: stretch;
  min-height: 440px;
  padding: 20px 17px;
  border-radius: 18px;
  background: var(--aqua);
}

.agenda-heading {
  justify-content: flex-start;
  margin-bottom: 28px;
}

.calendar-icon {
  display: grid;
  width: 34px;
  height: 34px;
  flex: 0 0 auto;
  place-items: center;
  border-radius: 9px;
  background: var(--teal-dark);
  color: #fff;
  font-size: 17px;
}

.agenda-heading h2 {
  font-size: 13px;
}

.agenda-heading p {
  font-size: 10px;
  text-transform: capitalize;
}

.agenda-list {
  display: grid;
  gap: 22px;
}

.agenda-day h3 {
  margin-bottom: 11px;
  color: var(--teal);
  font-size: 10px;
  font-weight: 700;
  text-transform: capitalize;
}

.agenda-day {
  display: grid;
  gap: 12px;
}

.agenda-task {
  display: flex;
  align-items: flex-start;
  gap: 9px;
}

.agenda-dot {
  width: 3px;
  min-height: 30px;
  flex: 0 0 3px;
  border-radius: 5px;
  background: var(--teal);
}

.agenda-task > div {
  display: grid;
  min-width: 0;
  gap: 4px;
}

.agenda-task strong {
  font-size: 10px;
}

.agenda-task > div > span {
  color: var(--muted);
  font-size: 9px;
}

.agenda-empty {
  padding: 16px 0;
  color: var(--muted);
  font-size: 11px;
  line-height: 1.6;
}

@media (max-width: 1050px) {
  .dashboard-layout {
    grid-template-columns: minmax(0, 1fr) 220px;
    gap: 18px;
  }

  .status-cards {
    gap: 9px;
  }

  .status-card {
    padding: 13px;
  }

  .status-label {
    font-size: 10px;
  }
}

@media (max-width: 820px) {
  .dashboard-layout {
    grid-template-columns: minmax(0, 1fr);
  }

  .agenda-panel {
    min-height: auto;
  }

  .agenda-list {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 520px) {
  .dashboard-header {
    align-items: flex-start;
  }

  .add-task-button {
    padding: 10px;
    font-size: 0;
  }

  .add-task-button span {
    font-size: 20px;
  }

  .status-cards {
    grid-template-columns: 1fr;
  }

  .status-card {
    padding: 13px 15px;
  }

  .status-count {
    margin: 10px 0;
    font-size: 24px;
  }

  .summary-grid {
    gap: 8px;
  }

  .summary-card {
    gap: 8px;
    min-height: 66px;
    padding: 9px;
  }

  .summary-icon {
    width: 30px;
    height: 30px;
  }

  .summary-card strong {
    font-size: 16px;
  }

  .summary-card div span {
    font-size: 9px;
  }

  .upcoming-item {
    flex-wrap: wrap;
    gap: 7px;
  }

  .upcoming-details {
    flex-basis: calc(100% - 16px);
  }

  .task-status {
    margin-left: 4px;
  }

  .agenda-list {
    grid-template-columns: 1fr;
  }
}
</style>
