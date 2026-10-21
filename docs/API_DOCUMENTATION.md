# API Documentation - Taskflow

## Base URL

```
http://localhost:8000/api
```

## Response Format

Semua responses menggunakan format JSON.

### Success Response

```json
{
  "data": { /* resource data */ },
  "message": "Operation successful"
}
```

### Error Response

```json
{
  "message": "Error message",
  "exception": "Exception class name",
  "errors": { /* validation errors */ }
}
```

## Endpoints

### 1. List All Tasks

**GET** `/tasks`

Mendapatkan daftar semua tugas terurut berdasarkan deadline (ascending).

#### Query Parameters

| Parameter | Type | Keterangan |
|-----------|------|-----------|
| (none) | - | - |

#### Response (200 OK)

```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "nama": "Tugas Matematika",
      "deskripsi": "Mengerjakan latihan soal bab 5",
      "deadline": "2026-10-25T00:00:00.000000Z",
      "status": "belum_dikerjakan",
      "prioritas": "tinggi",
      "created_at": "2026-10-20T07:07:22.000000Z",
      "updated_at": "2026-10-20T07:07:22.000000Z"
    }
  ],
  "count": 1
}
```

#### cURL Example

```bash
curl -X GET http://localhost:8000/api/tasks \
  -H "Accept: application/json"
```

---

### 2. Create New Task

**POST** `/tasks`

Membuat tugas baru.

#### Request Body

| Field | Type | Required | Keterangan |
|-------|------|----------|-----------|
| nama | string | ✓ | Nama tugas (max 255 karakter) |
| deskripsi | text | - | Deskripsi tugas |
| deadline | date | ✓ | Format: YYYY-MM-DD |
| prioritas | enum | ✓ | Nilai: rendah, sedang, tinggi |
| status | enum | - | Nilai: belum_dikerjakan, sedang_dikerjakan, selesai (default: belum_dikerjakan) |

#### Request Example

```json
{
  "nama": "Tugas Matematika",
  "deskripsi": "Mengerjakan latihan soal bab 5",
  "deadline": "2026-10-25",
  "prioritas": "tinggi"
}
```

#### Response (201 Created)

```json
{
  "message": "Task created successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "nama": "Tugas Matematika",
    "deskripsi": "Mengerjakan latihan soal bab 5",
    "deadline": "2026-10-25T00:00:00.000000Z",
    "status": "belum_dikerjakan",
    "prioritas": "tinggi",
    "created_at": "2026-10-20T07:07:22.000000Z",
    "updated_at": "2026-10-20T07:07:22.000000Z"
  }
}
```

#### cURL Example

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "nama": "Tugas Matematika",
    "deskripsi": "Mengerjakan latihan soal bab 5",
    "deadline": "2026-10-25",
    "prioritas": "tinggi"
  }'
```

---

### 3. Get Task Detail

**GET** `/tasks/{id}`

Mendapatkan detail tugas spesifik berdasarkan ID.

#### URL Parameters

| Parameter | Type | Keterangan |
|-----------|------|-----------|
| id | integer | Task ID |

#### Response (200 OK)

```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "nama": "Tugas Matematika",
    "deskripsi": "Mengerjakan latihan soal bab 5",
    "deadline": "2026-10-25T00:00:00.000000Z",
    "status": "belum_dikerjakan",
    "prioritas": "tinggi",
    "created_at": "2026-10-20T07:07:22.000000Z",
    "updated_at": "2026-10-20T07:07:22.000000Z"
  }
}
```

#### Error Response (404 Not Found)

```json
{
  "message": "No query results for model [App\\Models\\Task]."
}
```

#### cURL Example

```bash
curl -X GET http://localhost:8000/api/tasks/1 \
  -H "Accept: application/json"
```

---

### 4. Update Task

**PUT** `/tasks/{id}`

Mengupdate data tugas spesifik.

#### URL Parameters

| Parameter | Type | Keterangan |
|-----------|------|-----------|
| id | integer | Task ID |

#### Request Body

| Field | Type | Required | Keterangan |
|-------|------|----------|-----------|
| nama | string | - | Nama tugas (max 255 karakter) |
| deskripsi | text | - | Deskripsi tugas |
| deadline | date | - | Format: YYYY-MM-DD |
| prioritas | enum | - | Nilai: rendah, sedang, tinggi |
| status | enum | - | Nilai: belum_dikerjakan, sedang_dikerjakan, selesai |

#### Request Example

```json
{
  "status": "sedang_dikerjakan"
}
```

#### Response (200 OK)

```json
{
  "message": "Task updated successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "nama": "Tugas Matematika",
    "deskripsi": "Mengerjakan latihan soal bab 5",
    "deadline": "2026-10-25T00:00:00.000000Z",
    "status": "sedang_dikerjakan",
    "prioritas": "tinggi",
    "created_at": "2026-10-20T07:07:22.000000Z",
    "updated_at": "2026-10-20T07:10:14.000000Z"
  }
}
```

#### cURL Example

```bash
curl -X PUT http://localhost:8000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "status": "sedang_dikerjakan"
  }'
```

---

### 5. Delete Task

**DELETE** `/tasks/{id}`

Menghapus tugas spesifik.

#### URL Parameters

| Parameter | Type | Keterangan |
|-----------|------|-----------|
| id | integer | Task ID |

#### Response (200 OK)

```json
{
  "message": "Task deleted successfully"
}
```

#### cURL Example

```bash
curl -X DELETE http://localhost:8000/api/tasks/1 \
  -H "Accept: application/json"
```

---

### 6. Get Summary Statistics

**GET** `/tasks/summary`

Mendapatkan ringkasan statistik tugas (total, selesai, sedang dikerjakan, belum selesai).

#### Query Parameters

| Parameter | Type | Keterangan |
|-----------|------|-----------|
| (none) | - | - |

#### Response (200 OK)

```json
{
  "total": 10,
  "selesai": 3,
  "sedang_dikerjakan": 4,
  "belum_selesai": 7
}
```

#### cURL Example

```bash
curl -X GET http://localhost:8000/api/tasks/summary \
  -H "Accept: application/json"
```

---

## Validation Rules

### Create Task

- `nama`: required, string, max 255
- `deadline`: required, date format (YYYY-MM-DD)
- `prioritas`: required, in: rendah, sedang, tinggi
- `deskripsi`: optional, string

### Update Task

- `nama`: optional, string, max 255
- `deadline`: optional, date format (YYYY-MM-DD)
- `prioritas`: optional, in: rendah, sedang, tinggi
- `status`: optional, in: belum_dikerjakan, sedang_dikerjakan, selesai
- `deskripsi`: optional, string

### Error Response (422 Unprocessable Entity)

```json
{
  "message": "The nama field is required.",
  "errors": {
    "nama": ["The nama field is required."],
    "deadline": ["The deadline field must be a valid date."]
  }
}
```

---

## Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request berhasil |
| 201 | Created - Resource berhasil dibuat |
| 404 | Not Found - Resource tidak ditemukan |
| 422 | Unprocessable Entity - Validasi gagal |
| 500 | Internal Server Error - Server error |

---

## Task Status Enum

| Value | Label | Deskripsi |
|-------|-------|-----------|
| belum_dikerjakan | Belum Dikerjakan | Tugas belum dimulai |
| sedang_dikerjakan | Sedang Dikerjakan | Tugas sedang dalam proses |
| selesai | Selesai | Tugas sudah selesai |

---

## Task Priority Enum

| Value | Label | Deskripsi |
|-------|-------|-----------|
| rendah | Rendah | Prioritas rendah |
| sedang | Sedang | Prioritas sedang |
| tinggi | Tinggi | Prioritas tinggi/urgent |

---

## Code Examples

### JavaScript (Axios)

```javascript
import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: { 'Accept': 'application/json' }
})

// Get all tasks
api.get('/tasks').then(res => console.log(res.data))

// Create task
api.post('/tasks', {
  nama: 'Tugas Baru',
  deadline: '2026-10-25',
  prioritas: 'tinggi'
}).then(res => console.log(res.data))

// Update task
api.put('/tasks/1', { status: 'sedang_dikerjakan' })
  .then(res => console.log(res.data))

// Delete task
api.delete('/tasks/1').then(res => console.log(res.data))

// Get summary
api.get('/tasks/summary').then(res => console.log(res.data))
```

### Python (Requests)

```python
import requests

BASE_URL = 'http://localhost:8000/api'

# Get all tasks
response = requests.get(f'{BASE_URL}/tasks')
print(response.json())

# Create task
task_data = {
    'nama': 'Tugas Baru',
    'deadline': '2026-10-25',
    'prioritas': 'tinggi'
}
response = requests.post(f'{BASE_URL}/tasks', json=task_data)
print(response.json())

# Update task
update_data = {'status': 'selesai'}
response = requests.put(f'{BASE_URL}/tasks/1', json=update_data)
print(response.json())

# Delete task
response = requests.delete(f'{BASE_URL}/tasks/1')
print(response.json())
```

---

## Testing dengan Postman

1. Import atau buat Collection baru
2. Setup environment variable: `base_url = http://localhost:8000/api`
3. Buat requests sesuai endpoints di atas
4. Test setiap endpoint

### Postman Collection (JSON)

```json
{
  "info": {
    "name": "Taskflow API",
    "description": "Taskflow Task Management API"
  },
  "item": [
    {
      "name": "Get All Tasks",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/tasks"
      }
    },
    {
      "name": "Create Task",
      "request": {
        "method": "POST",
        "url": "{{base_url}}/tasks",
        "body": {
          "mode": "raw",
          "raw": "{\"nama\":\"Tugas Baru\",\"deadline\":\"2026-10-25\",\"prioritas\":\"tinggi\"}"
        }
      }
    }
  ]
}
```

---

## Rate Limiting

Tidak ada rate limiting di fase development. Untuk production, pertimbangkan:
- Menggunakan Laravel rate limiting middleware
- Implement API throttling (misal: 60 requests/minute per user)

## CORS

CORS sudah dikonfigurasi di Laravel middleware. Frontend dapat access API dari `http://localhost:5173`.

## Authentication

Fase 1: Tidak ada authentication. user_id hardcoded ke 1.
Fase 2+: Akan ditambahkan JWT atau Laravel Sanctum untuk authentication.
