<template>
  <div class="app-shell">
    <aside class="sidebar">
      <a class="brand" href="#" @click.prevent="view = 'dashboard'">
        <span class="brand-mark" aria-hidden="true">✓</span>
        <span>Taskflow</span>
      </a>

      <p class="nav-label">MENU</p>
      <nav class="navigation" aria-label="Navigasi utama">
        <button
          class="nav-item"
          :class="{ active: view === 'dashboard' }"
          :aria-current="view === 'dashboard' ? 'page' : undefined"
          @click="view = 'dashboard'"
        >
          <span class="nav-icon" aria-hidden="true">▦</span>
          Dashboard
        </button>
        <button
          class="nav-item"
          :class="{ active: view === 'tasks' }"
          :aria-current="view === 'tasks' ? 'page' : undefined"
          @click="view = 'tasks'"
        >
          <span class="nav-icon" aria-hidden="true">✓</span>
          Tugas
        </button>
      </nav>

      <div class="sidebar-footer">
        <span class="footer-mark" aria-hidden="true">✓</span>
        <span>Atur tugas, selesaikan harimu.</span>
      </div>
    </aside>

    <main class="page-content">
      <Dashboard
        v-if="view === 'dashboard'"
        @view-change="view = $event"
      />
      <TaskList v-else />
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Dashboard from './views/Dashboard.vue'
import TaskList from './views/TaskList.vue'

const view = ref('dashboard')
</script>

<style scoped>
.app-shell {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 228px minmax(0, 1fr);
  background: var(--page-bg);
}

.sidebar {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  padding: 30px 18px 20px;
  background: var(--teal);
  color: #f7fbfa;
}

.brand {
  display: flex;
  align-items: center;
  gap: 11px;
  padding: 0 12px;
  color: inherit;
  font-size: 20px;
  font-weight: 750;
  letter-spacing: -0.04em;
  text-decoration: none;
}

.brand-mark,
.footer-mark {
  display: inline-grid;
  width: 30px;
  height: 30px;
  place-items: center;
  border-radius: 9px;
  background: rgba(255, 255, 255, 0.16);
  font-size: 18px;
  font-weight: 700;
}

.nav-label {
  margin: 52px 12px 12px;
  color: rgba(247, 251, 250, 0.58);
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.13em;
}

.navigation {
  display: grid;
  gap: 7px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  padding: 12px;
  border: 0;
  border-radius: 10px;
  background: transparent;
  color: rgba(247, 251, 250, 0.76);
  font: inherit;
  font-size: 13px;
  font-weight: 600;
  text-align: left;
  cursor: pointer;
  transition: background 160ms ease, color 160ms ease;
}

.nav-item:hover,
.nav-item.active {
  background: rgba(255, 255, 255, 0.14);
  color: #fff;
}

.nav-icon {
  display: inline-grid;
  width: 20px;
  place-items: center;
  font-size: 17px;
}

.sidebar-footer {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: auto;
  padding: 18px 10px 0;
  border-top: 1px solid rgba(255, 255, 255, 0.14);
  color: rgba(247, 251, 250, 0.68);
  font-size: 11px;
  line-height: 1.5;
}

.footer-mark {
  flex: 0 0 auto;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  font-size: 14px;
}

.page-content {
  min-width: 0;
  padding: 34px clamp(22px, 3.2vw, 52px);
}

@media (max-width: 760px) {
  .app-shell {
    grid-template-columns: 1fr;
    grid-template-rows: auto 1fr;
  }

  .sidebar {
    position: sticky;
    z-index: 5;
    top: 0;
    min-height: auto;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
  }

  .brand {
    padding: 0;
    font-size: 18px;
  }

  .brand-mark {
    width: 28px;
    height: 28px;
  }

  .nav-label,
  .sidebar-footer {
    display: none;
  }

  .navigation {
    display: flex;
    gap: 4px;
  }

  .nav-item {
    width: auto;
    gap: 7px;
    padding: 9px 10px;
    font-size: 12px;
  }

  .nav-icon {
    width: auto;
  }

  .page-content {
    padding: 24px 16px;
  }
}

@media (max-width: 390px) {
  .sidebar {
    padding-inline: 11px;
  }

  .brand {
    gap: 7px;
    font-size: 16px;
  }

  .nav-item {
    gap: 5px;
    padding: 8px;
    font-size: 11px;
  }
}
</style>
