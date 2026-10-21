# Taskflow

Taskflow is a small, local task-management application for organizing deadlines and priorities. It provides a dashboard and a task list with search, status filters, task creation, status updates, and deletion.

## Features

- Dashboard with task counts, upcoming tasks, and deadline agenda
- Create tasks with a name, deadline, priority, and optional description
- Change task status or delete a task
- Search tasks by name and filter by status
- Responsive layout for desktop and mobile screens
- In-memory task handling; no database or backend server is required to use the frontend

## Technology

- Vue 3
- Vite 5
- CSS

## Requirements

- Node.js 18 or later
- npm

## Run locally

From the project root:

```bash
cd frontend
npm install
npm run dev
```

Open the local address printed by Vite, usually `http://localhost:5173`.

To stop the development server, press `Ctrl+C` in the terminal where it is running.

## Data behavior

Tasks are kept only in the browser application's memory while the page is open. They are not saved to a database, browser storage, or a remote service. Reloading or closing the page clears all tasks.

The application does not require an account or personal information. Do not enter sensitive information into task names or descriptions.

## Task fields

| Field | Required | Description |
| --- | --- | --- |
| Name | Yes | Task title, up to 255 characters |
| Description | No | Optional task notes |
| Deadline | Yes | Task due date |
| Priority | Yes | Low, medium, or high |
| Status | Automatically set | Not started, in progress, or completed |

Status values used by the application:

- `belum_dikerjakan` — Not started
- `sedang_dikerjakan` — In progress
- `selesai` — Completed

Priority values used by the application:

- `rendah` — Low
- `sedang` — Medium
- `tinggi` — High

## Production build

From the `frontend` directory:

```bash
npm run build
```

The generated static files are written to `frontend/dist`.

## Project structure

```text
.
├── frontend/
│   ├── src/
│   │   ├── views/
│   │   │   ├── Dashboard.vue
│   │   │   └── TaskList.vue
│   │   ├── App.vue
│   │   ├── api.js
│   │   └── style.css
│   ├── index.html
│   └── package.json
└── docs/
```

The `backend/` directory contains the previous Laravel implementation. It is not used by the current frontend-only application; running `php artisan serve` or configuring a database is not needed.

## Troubleshooting

### The browser shows `ERR_CONNECTION_REFUSED`

Make sure the Vite development server is still running. From the `frontend` directory, start it with:

```bash
npm run dev
```

Keep the terminal open while using the application and open the URL printed by Vite.

### The page opens but tasks are empty

This is expected after reloading or reopening the page because tasks are temporary and held in memory only. Add the tasks again for the current session.

### Port 5173 is already in use

Stop the other process using the port or use a different Vite port:

```bash
npm run dev -- --port 5174
```
