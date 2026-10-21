<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskflow - Sistem Manajemen Tugas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
                'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
                sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 600px;
            text-align: center;
        }
        
        .logo {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 32px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .description {
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .info {
            background: #f5f5f5;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-radius: 4px;
        }
        
        .info h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .info p {
            color: #666;
            font-size: 13px;
            margin: 5px 0;
            font-family: 'Courier New', monospace;
        }
        
        .status {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            margin: 10px 5px;
            font-weight: 600;
        }
        
        .status.backend {
            background: #667eea;
        }
        
        .status.database {
            background: #764ba2;
        }
        
        .links {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
            border: 2px solid #ddd;
        }
        
        .btn-secondary:hover {
            background: #e8e8e8;
            border-color: #bbb;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">📋</div>
        
        <h1>Taskflow</h1>
        <p class="subtitle">Sistem Manajemen Tugas & Deadline</p>
        
        <div class="description">
            Aplikasi web untuk mencatat tugas sekolah, menentukan tenggat waktu, mengatur prioritas, dan memantau progres tugas Anda.
        </div>
        
        <div>
            <span class="status backend">✓ Backend Running</span>
            <span class="status database">✓ Database Ready</span>
        </div>
        
        <div class="info">
            <h3>📚 Fitur Utama</h3>
            <p>✓ Tambah tugas dengan nama, deskripsi, dan deadline</p>
            <p>✓ Ubah status: belum dikerjakan → sedang dikerjakan → selesai</p>
            <p>✓ Tentukan prioritas: Rendah, Sedang, atau Tinggi</p>
            <p>✓ Tampilkan daftar tugas berdasarkan deadline</p>
            <p>✓ Ringkasan jumlah tugas selesai dan belum selesai</p>
        </div>
        
        <div class="info">
            <h3>🔧 Teknologi</h3>
            <p>Backend: Laravel 11 (PHP)</p>
            <p>Frontend: Vue.js 3 + Vite</p>
            <p>Database: SQLite (Development)</p>
        </div>
        
        <div class="info">
            <h3>📡 API Endpoints</h3>
            <p>POST /api/tasks - Buat tugas baru</p>
            <p>GET /api/tasks - Daftar semua tugas</p>
            <p>GET /api/tasks/{id} - Detail tugas</p>
            <p>PUT /api/tasks/{id} - Update tugas</p>
            <p>DELETE /api/tasks/{id} - Hapus tugas</p>
            <p>GET /api/tasks/summary - Ringkasan statistik</p>
        </div>
        
        <div class="links">
            <a href="http://localhost:5173" class="btn btn-primary">🚀 Buka Aplikasi Frontend</a>
            <a href="http://localhost:8000/api/tasks" class="btn btn-secondary">📊 Lihat API Tasks</a>
        </div>
        
        <div class="footer">
            <p>Taskflow v1.0 | Project Manajemen Tugas Sekolah</p>
        </div>
    </div>
</body>
</html>
