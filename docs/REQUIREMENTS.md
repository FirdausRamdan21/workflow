# Requirements Document - Taskflow

## 1. Problem Statement

Siswa membutuhkan cara terstruktur untuk:
- Mencatat tugas sekolah secara digital
- Menentukan deadline untuk setiap tugas
- Mengatur prioritas tugas
- Melacak progress pengerjaan tugas
- Melihat ringkasan progress secara keseluruhan

Tanpa sistem terstruktur, siswa sering kali:
- Lupa deadline tugas
- Mengerjakan tugas yang tidak prioritas terlebih dahulu
- Tidak mengetahui berapa banyak tugas yang sudah selesai
- Kesulitan mengelola banyak tugas sekaligus

## 2. Solution Overview

Aplikasi web **Taskflow** - Sistem Manajemen Tugas dan Deadline, yang memungkinkan siswa untuk:
- Membuat, membaca, mengupdate, dan menghapus tugas (CRUD)
- Mengatur status dan prioritas setiap tugas
- Melihat daftar tugas terurut berdasarkan deadline
- Melihat ringkasan statistik progress

## 3. Functional Requirements

### 3.1 Manajemen Tugas

#### FR-001: Tambah Tugas Baru
- **Deskripsi**: User dapat menambahkan tugas baru ke sistem
- **Input**: 
  - Nama tugas (required)
  - Deskripsi tugas (optional)
  - Deadline (required)
  - Prioritas (required)
- **Output**: Tugas baru terlihat di daftar tugas
- **Validasi**:
  - Nama tugas tidak boleh kosong (required)
  - Deadline harus format date yang valid
  - Prioritas harus salah satu dari: rendah, sedang, tinggi

#### FR-002: Lihat Daftar Tugas
- **Deskripsi**: User dapat melihat semua tugas yang telah dibuat
- **Output**: 
  - List tugas terurut berdasarkan deadline (ascending)
  - Menampilkan: nama, deskripsi, deadline, status, prioritas
- **Sorting**: Default by deadline (nearest first)
- **Filtering**: Dapat di-filter by status (opsional fase 1)

#### FR-003: Lihat Detail Tugas
- **Deskripsi**: User dapat melihat detail lengkap tugas tertentu
- **Input**: Task ID
- **Output**: Semua informasi tugas

#### FR-004: Update Tugas
- **Deskripsi**: User dapat mengupdate informasi tugas yang sudah ada
- **Input**: Task ID dan field yang ingin diupdate (nama, deskripsi, deadline, prioritas, status)
- **Output**: Tugas terupdate dengan informasi baru

#### FR-005: Hapus Tugas
- **Deskripsi**: User dapat menghapus tugas dari sistem
- **Input**: Task ID
- **Output**: Tugas dihapus dari daftar
- **Konfirmasi**: Menampilkan dialog konfirmasi sebelum delete

### 3.2 Status Manajemen

#### FR-006: Ubah Status Tugas
- **Deskripsi**: User dapat mengubah status pengerjaan tugas
- **Status Values**: 
  - `belum_dikerjakan` - Belum dimulai
  - `sedang_dikerjakan` - Sedang dikerjakan
  - `selesai` - Sudah selesai
- **Default Status**: belum_dikerjakan
- **UI**: Dropdown atau buttons di task card

### 3.3 Prioritas Manajemen

#### FR-007: Tentukan Prioritas Tugas
- **Deskripsi**: User dapat menentukan dan mengubah prioritas tugas
- **Priority Values**:
  - `rendah` - Prioritas rendah
  - `sedang` - Prioritas sedang
  - `tinggi` - Prioritas tinggi/urgent
- **Required**: Prioritas wajib diisi saat membuat tugas
- **Visual**: Badge dengan warna berbeda untuk setiap prioritas

### 3.4 Dashboard & Ringkasan

#### FR-008: Lihat Dashboard
- **Deskripsi**: User dapat melihat ringkasan statistik tugas di halaman dashboard
- **Metrics**:
  - Total tugas keseluruhan
  - Jumlah tugas selesai
  - Jumlah tugas sedang dikerjakan
  - Jumlah tugas belum selesai (belum_dikerjakan + sedang_dikerjakan)
- **Update**: Real-time atau manual refresh

#### FR-009: Tampilkan Breakdown Status
- **Deskripsi**: Dashboard menampilkan breakdown jumlah tugas per status
- **Visual**: Cards atau widgets dengan angka dan icon
- **Real-time Update**: Summary update setelah CRUD action

## 4. Non-Functional Requirements

### 4.1 Performa

- **NFR-001**: Aplikasi harus load dalam waktu < 3 detik di koneksi 3G
- **NFR-002**: API response time < 500ms untuk 90% requests
- **NFR-003**: Aplikasi harus support minimal 100 concurrent users (development)

### 4.2 Usability

- **NFR-004**: Interface harus responsive (mobile, tablet, desktop)
- **NFR-005**: Minimum color contrast ratio 4.5:1 untuk text
- **NFR-006**: Semua fitur harus accessible dengan keyboard (Tab, Enter, Escape)
- **NFR-007**: Loading indicator harus ditampilkan saat fetch data

### 4.3 Reliability

- **NFR-008**: Uptime minimal 99% di production
- **NFR-009**: Error handling harus user-friendly (bukan error codes)
- **NFR-010**: Data persist ke database setelah setiap CRUD operation

### 4.4 Security

- **NFR-011**: Database connection menggunakan SSL (production)
- **NFR-012**: Input validation pada frontend dan backend
- **NFR-013**: CORS hanya allow frontend origin
- **NFR-014**: Fase 1: Basic security; Fase 2: Add authentication (JWT/Sanctum)

### 4.5 Maintainability

- **NFR-015**: Code harus documented dengan comments
- **NFR-016**: API harus return consistent response format (JSON)
- **NFR-017**: Error messages harus clear dan actionable

## 5. Constraints & Limitations

### 5.1 Fase 1 (MVP)

- **No Authentication**: User tidak perlu login (user_id hardcoded ke 1)
- **No Notifications**: Tidak ada reminder atau notification untuk deadline
- **No Calendar Integration**: Tidak ada integrasi dengan kalender
- **No AI Features**: Tidak ada recommendation atau auto-categorization
- **Single User**: Aplikasi dirancang untuk single user (multi-user di fase 2+)
- **SQLite Database**: Development menggunakan SQLite (production: MySQL)

### 5.2 Scope Out (Future Phases)

- User authentication & multi-user support
- Email/push notifications untuk deadline mendekati
- Calendar integration (Google Calendar, Outlook)
- Recurring tasks
- Task categories/tags
- Collaboration & task sharing
- Mobile app
- Analytics & insights

## 6. Data Requirements

### 6.1 Data Model

**Tasks Table**
```
- id (bigint): Primary key
- user_id (bigint): User ID (default: 1 untuk fase 1)
- nama (string): Task name
- deskripsi (text): Task description
- deadline (date): Deadline date
- status (enum): belum_dikerjakan | sedang_dikerjakan | selesai
- prioritas (enum): rendah | sedang | tinggi
- created_at (timestamp): Creation time
- updated_at (timestamp): Last update time
```

### 6.2 Data Retention

- Semua data task disimpan indefinitely
- Archive old tasks (> 1 tahun) di future (fase 3+)

## 7. User Stories

### User Story 1: Student membuat tugas baru
```
As a student
I want to quickly add a new task with deadline and priority
So that I don't forget the task
```

**Acceptance Criteria:**
- Form mudah diakses (button di halaman utama)
- Form memvalidasi input sebelum submit
- After submit, tugas langsung muncul di daftar

### User Story 2: Student track progress
```
As a student
I want to see my overall progress
So that I can manage my workload effectively
```

**Acceptance Criteria:**
- Dashboard menampilkan total tugas dan breakdown status
- Data update real-time setelah action
- Metrics mudah dipahami (menggunakan angka dan visual)

### User Story 3: Student ubah status tugas
```
As a student
I want to update task status (belum/sedang/selesai)
So that I can track which tasks are in progress
```

**Acceptance Criteria:**
- Status change mudah dan instant
- Status perubahan langsung tercermin di list dan dashboard
- Tidak perlu navigasi ke halaman terpisah

### User Story 4: Student filter tugas
```
As a student
I want to filter tasks by status or priority
So that I can focus on what needs to be done
```

**Acceptance Criteria:**
- Filter buttons/dropdown mudah diakses
- Filter action instant (client-side)
- Dapat clear filter dengan mudah

## 8. Acceptance Criteria (General)

- [ ] Semua CRUD operations (Create, Read, Update, Delete) working
- [ ] Data persist ke database dengan benar
- [ ] API responses menggunakan format JSON yang konsisten
- [ ] Frontend dapat communicate dengan backend tanpa CORS errors
- [ ] Sorting by deadline working correctly
- [ ] Summary statistics akurat
- [ ] Error messages user-friendly dan clear
- [ ] Responsive design tested di mobile (320px), tablet (768px), desktop (1024px)
- [ ] No console errors atau warnings
- [ ] Loading indicators ditampilkan saat fetch data

## 9. Success Metrics

### 9.1 Technical Metrics

- **API Uptime**: 99%+ di production
- **Response Time**: Average < 200ms per request
- **Error Rate**: < 0.1% failed requests
- **Test Coverage**: > 80% untuk critical paths

### 9.2 User Metrics

- **Feature Usage**: > 80% users menggunakan semua fitur utama
- **Task Completion**: > 70% tasks ditandai selesai
- **User Retention**: > 80% users aktif weekly (future)

## 10. Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11 (PHP 8.2+) |
| Frontend | Vue.js 3 + Vite |
| Database | SQLite (dev) / MySQL (production) |
| HTTP Client | Axios |
| Styling | CSS3 + Tailwind-like approach |

## 11. Deliverables Fase 1

- [x] Backend API dengan endpoints CRUD
- [x] Frontend SPA dengan Vue.js 3
- [x] Database migration dan model
- [x] Dashboard dengan summary
- [x] Task list dengan filtering
- [x] Create/Edit task modal
- [x] Responsive design
- [ ] Unit tests (optional fase 1)
- [ ] API documentation
- [ ] Setup guide
- [ ] User manual

## 12. Timeline

| Fase | Task | Duration | Status |
|------|------|----------|--------|
| 1 | Setup & Core CRUD | 1 day | ✅ Complete |
| 2 | Frontend UI & Integration | 2 days | In Progress |
| 3 | Testing & QA | 1 day | Pending |
| 4 | Documentation | 1 day | Pending |
| 5 | Deployment Prep | 1 day | Pending |

## 13. Approval & Sign-off

- [ ] Product Owner: Approved
- [ ] Tech Lead: Approved
- [ ] QA Lead: Approved

---

**Document Version**: 1.0  
**Last Updated**: October 20, 2026  
**Status**: Draft → Ready for Development
