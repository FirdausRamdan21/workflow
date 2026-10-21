# Taskflow

Taskflow is a lightweight task and deadline management application. It provides a responsive dashboard for reviewing task status and upcoming deadlines, along with a simple interface for creating, searching, filtering, updating the status of, and deleting tasks.

## Features

- **Task dashboard** — View task counts by status, upcoming tasks, and deadline reminders.
- **Task management** — Create and delete tasks, and update their status.
- **Search and filters** — Search tasks by name and filter the list by status.
- **Priority and deadlines** — Assign a priority and due date to each task.
- **Responsive interface** — Use the dashboard and task list on desktop, tablet, and mobile screens.
- **JSON API** — Access task operations through a Laravel API.

> Taskflow currently has no user accounts or authentication. The `user_id` field in the task table is a placeholder.

## Technology Stack

| Component | Technology |
| --- | --- |
| Backend | PHP 8.3+, Laravel 13 |
| Frontend | Vue 3, Vite 5 |
| HTTP client | Axios |
| Database | SQLite by default |
| Styling | CSS |

## Requirements

Install the following tools before getting started:

- PHP 8.3 or later
- Composer
- Node.js 18 or later and npm

## Getting Started

Run the backend and frontend in separate terminals.

### 1. Set up the backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

The default environment configuration uses SQLite. Create the database file if it does not exist, then run the migrations:

```bash
touch database/database.sqlite
php artisan migrate
```

Start the API server:

```bash
php artisan serve
```

By default, the backend is available at `http://localhost:8000`.

### 2. Set up the frontend

In a second terminal, from the project root:

```bash
cd frontend
npm install
npm run dev
```

Open the local URL printed by Vite, usually `http://localhost:5173`.

The frontend currently sends API requests to `http://localhost:8000/api`. If the backend uses a different host or port, update `API_BASE_URL` in `frontend/src/api.js` to match.

### 3. Create a production frontend build

From the `frontend` directory:

```bash
npm run build
```

Vite writes the production build to `frontend/dist`.

## Using Taskflow

1. Open the frontend URL in your browser.
2. Review task counts, upcoming tasks, and deadline reminders on the dashboard.
3. Open **Tasks** to search, filter, or manage the task list.
4. Select **Add task** and provide a name, deadline, and priority. A description is optional.
5. Use each task's status control to change its status. Use the delete control to remove a task.

Available statuses:

- `belum_dikerjakan` — Not started
- `sedang_dikerjakan` — In progress
- `selesai` — Completed

Available priorities:

- `rendah` — Low
- `sedang` — Medium
- `tinggi` — High

## API

The API base URL for local development is:

```text
http://localhost:8000/api
```

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/tasks` | List tasks, ordered by deadline |
| `POST` | `/tasks` | Create a task |
| `GET` | `/tasks/{id}` | Get a task |
| `PUT` | `/tasks/{id}` | Update a task |
| `DELETE` | `/tasks/{id}` | Delete a task |
| `GET` | `/tasks/summary` | Get task counts by status |

### Create a task

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "nama": "Complete mathematics exercises",
    "deskripsi": "Finish chapter five exercises",
    "deadline": "2026-12-31",
    "prioritas": "tinggi"
  }'
```

The task name, deadline, and priority are required. The name must not exceed 255 characters. A description is optional. When omitted, the status defaults to `belum_dikerjakan`.

The API validates task fields and returns JSON responses. Validation errors use Laravel's standard validation response format.

For endpoint details and sample responses, see [API documentation](docs/API_DOCUMENTATION.md).

## Data Storage

The task table contains:

| Field | Description |
| --- | --- |
| `id` | Task identifier |
| `user_id` | Placeholder user identifier; defaults to `1` |
| `nama` | Required task name |
| `deskripsi` | Optional task description |
| `deadline` | Required due date |
| `status` | Task status |
| `prioritas` | Task priority |
| `created_at`, `updated_at` | Record timestamps |

SQLite is configured for local development. See [database documentation](docs/DATABASE_SCHEMA.md) for the schema details.

## Project Structure

```text
.
├── backend/
│   ├── app/Http/Controllers/TaskController.php
│   ├── app/Models/Task.php
│   ├── database/migrations/
│   └── routes/api.php
├── docs/
│   ├── API_DOCUMENTATION.md
│   ├── DATABASE_SCHEMA.md
│   └── REQUIREMENTS.md
└── frontend/
    ├── src/
    │   ├── views/
    │   ├── App.vue
    │   ├── api.js
    │   └── style.css
    └── package.json
```

## Testing and Validation

Run the backend test suite from the `backend` directory:

```bash
php artisan test
```

Check that the frontend compiles for production from the `frontend` directory:

```bash
npm run build
```

## Troubleshooting

### The browser shows `ERR_CONNECTION_REFUSED`

Make sure the relevant development server is running and leave its terminal open:

- Backend: run `php artisan serve` from `backend`.
- Frontend: run `npm run dev` from `frontend`.

### The dashboard cannot load tasks

Confirm that the backend is available at `http://localhost:8000` and that the database has been migrated:

```bash
cd backend
php artisan migrate
```

If the backend uses another host or port, update `API_BASE_URL` in `frontend/src/api.js`.

### The default port is already in use

Start Laravel on another port:

```bash
php artisan serve --port=8001
```

If you change the backend port, update the frontend API base URL as well. To change Vite's port, edit the server configuration in `frontend/vite.config.js`.

## Current Scope

Taskflow is a single-user task management application without authentication. The frontend supports creating and deleting tasks and changing task status. The API also supports updating task details. Authentication, user profiles, team collaboration, and project management are not part of the current application.