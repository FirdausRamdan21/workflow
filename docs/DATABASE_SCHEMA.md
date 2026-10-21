# Database Schema - Taskflow

## Deskripsi Umum

Database Taskflow menggunakan SQLite untuk development dan dapat dengan mudah dimigrasi ke MySQL untuk production. Database berisi tabel utama `tasks` yang menyimpan semua informasi tugas pengguna.

## Tabel: tasks

Tabel ini menyimpan semua tugas yang dibuat oleh pengguna.

### Struktur Kolom

| Kolom | Tipe | Null | Default | Keterangan |
|-------|------|------|---------|-----------|
| id | bigint (UNSIGNED) | NO | AUTO_INCREMENT | Primary Key - ID unik tugas |
| user_id | bigint (UNSIGNED) | NO | 1 | Foreign Key ke users table (placeholder untuk fase 1) |
| nama | varchar(255) | NO | - | Nama/judul tugas (required) |
| deskripsi | longtext | YES | NULL | Deskripsi detail tugas (optional) |
| deadline | date | NO | - | Tanggal tenggat waktu tugas (required) |
| status | enum | NO | 'belum_dikerjakan' | Status tugas: belum_dikerjakan, sedang_dikerjakan, selesai |
| prioritas | enum | NO | - | Prioritas tugas: rendah, sedang, tinggi (required) |
| created_at | timestamp | YES | NULL | Waktu pembuatan tugas |
| updated_at | timestamp | YES | NULL | Waktu modifikasi terakhir |

### Enum Values

#### Status
- `belum_dikerjakan` - Tugas belum mulai dikerjakan
- `sedang_dikerjakan` - Tugas sedang dalam proses pengerjaan
- `selesai` - Tugas sudah selesai

#### Prioritas
- `rendah` - Prioritas rendah
- `sedang` - Prioritas sedang
- `tinggi` - Prioritas tinggi/urgent

## Migration File

File migration: `database/migrations/2026_10_20_065120_create_tasks_table.php`

```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id')->default(1);
    $table->string('nama');
    $table->text('deskripsi')->nullable();
    $table->date('deadline');
    $table->enum('status', ['belum_dikerjakan', 'sedang_dikerjakan', 'selesai'])->default('belum_dikerjakan');
    $table->enum('prioritas', ['rendah', 'sedang', 'tinggi']);
    $table->timestamps();
});
```

## Entity Relationship Diagram (ERD)

```
┌──────────────────────────────────┐
│           users                  │
├──────────────────────────────────┤
│ id (PK)                          │
│ name                             │
│ email                            │
│ password                         │
│ created_at                       │
│ updated_at                       │
└──────────────────────────────────┘
            │ 1
            │
            │ N (relasi di fase 2+)
            │
            ▼
┌──────────────────────────────────┐
│           tasks                  │
├──────────────────────────────────┤
│ id (PK)                          │
│ user_id (FK)                     │
│ nama                             │
│ deskripsi                        │
│ deadline                         │
│ status (enum)                    │
│ prioritas (enum)                 │
│ created_at                       │
│ updated_at                       │
└──────────────────────────────────┘
```

### Relasi

- **Fase 1 (Current)**: user_id digunakan sebagai placeholder (hardcoded ke 1)
- **Fase 2+**: Akan ditambahkan foreign key relationship ke users table dengan cascading delete

## Indeks

Untuk optimasi query:

```sql
-- Index pada deadline untuk sorting
CREATE INDEX idx_tasks_deadline ON tasks(deadline);

-- Index pada status untuk filtering
CREATE INDEX idx_tasks_status ON tasks(status);

-- Index pada user_id untuk multi-user lookup
CREATE INDEX idx_tasks_user_id ON tasks(user_id);

-- Composite index untuk common queries
CREATE INDEX idx_tasks_user_deadline ON tasks(user_id, deadline);
```

## Contoh Data

### Sample Records

```json
[
  {
    "id": 1,
    "user_id": 1,
    "nama": "Tugas Matematika",
    "deskripsi": "Mengerjakan latihan soal bab 5 tentang integral",
    "deadline": "2026-10-25",
    "status": "sedang_dikerjakan",
    "prioritas": "tinggi",
    "created_at": "2026-10-20T07:07:22.000000Z",
    "updated_at": "2026-10-20T07:10:14.000000Z"
  },
  {
    "id": 2,
    "user_id": 1,
    "nama": "Reading Assignment Bahasa Inggris",
    "deskripsi": null,
    "deadline": "2026-10-28",
    "status": "belum_dikerjakan",
    "prioritas": "rendah",
    "created_at": "2026-10-20T08:00:00.000000Z",
    "updated_at": "2026-10-20T08:00:00.000000Z"
  },
  {
    "id": 3,
    "user_id": 1,
    "nama": "Project Fisika",
    "deskripsi": "Membuat model tata surya dengan bola styrofoam",
    "deadline": "2026-10-22",
    "status": "selesai",
    "prioritas": "sedang",
    "created_at": "2026-10-15T10:00:00.000000Z",
    "updated_at": "2026-10-20T14:30:00.000000Z"
  }
]
```

## Queries Umum

### Dapatkan Semua Tugas Terurut by Deadline

```sql
SELECT * FROM tasks 
WHERE user_id = 1 
ORDER BY deadline ASC;
```

### Dapatkan Tugas Belum Selesai

```sql
SELECT * FROM tasks 
WHERE user_id = 1 
  AND status != 'selesai' 
ORDER BY deadline ASC;
```

### Hitung Tugas by Status

```sql
SELECT 
  COUNT(*) as total,
  SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
  SUM(CASE WHEN status = 'sedang_dikerjakan' THEN 1 ELSE 0 END) as sedang_dikerjakan,
  SUM(CASE WHEN status IN ('belum_dikerjakan', 'sedang_dikerjakan') THEN 1 ELSE 0 END) as belum_selesai
FROM tasks 
WHERE user_id = 1;
```

### Dapatkan Tugas Urgent (Prioritas Tinggi, Belum Selesai)

```sql
SELECT * FROM tasks 
WHERE user_id = 1 
  AND prioritas = 'tinggi' 
  AND status != 'selesai' 
ORDER BY deadline ASC 
LIMIT 5;
```

## Scaling untuk Production

### Rekomendasi Migration ke MySQL

```bash
# 1. Update .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskflow
DB_USERNAME=root
DB_PASSWORD=password

# 2. Create database
CREATE DATABASE taskflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 3. Run migrations
php artisan migrate
```

### Optimization untuk Scale Besar

1. **Partitioning**: Partition table by user_id untuk query faster
2. **Caching**: Cache summary statistics dengan Redis
3. **Archive**: Archive tasks yang selesai > 1 tahun ke separate table
4. **Full-text Search**: Add fulltext index pada `nama` dan `deskripsi`

## Catatan

- Fase 1: Database dirancang minimal dan fokus pada core functionality
- user_id placeholder dapat di-update di fase 2 dengan authentication
- Timestamps (created_at, updated_at) otomatis diisi oleh Laravel
- Database dapat dibackup dengan mudah: `sqlite3 database.sqlite .dump > backup.sql`

## File Lokasi

- SQLite DB: `backend/database/database.sqlite`
- Migration: `backend/database/migrations/2026_10_20_065120_create_tasks_table.php`
- Model: `backend/app/Models/Task.php`
