<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header('Location: /login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack - Buat Tantangan</title>
    <link rel="stylesheet" href="/css/home.css">
    <style>
        body { background: #eff6ff; margin: 0; font-family: Inter, sans-serif; color: #0f172a; }
        .page-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: #0f172a; color: #f8fafc; padding: 32px; }
        .sidebar .logo { font-size: 2rem; font-weight: 900; margin-bottom: 2rem; }
        .menu { display: flex; flex-direction: column; gap: 18px; }
        .menu-item { color: #cbd5e1; text-decoration: none; font-weight: 700; padding: 14px 16px; border-radius: 16px; }
        .menu-item.active, .menu-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .main { flex: 1; padding: 36px; }
        .back-link { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 18px; color: #2563eb; text-decoration: none; font-weight: 700; }
        .page-title { font-size: 2.4rem; margin: 0 0 8px; }
        .page-subtitle { color: #475569; margin: 0 0 24px; }
        .form-card { background: #fff; border-radius: 32px; padding: 34px; box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08); }
        .field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .field-group { display: flex; flex-direction: column; gap: 10px; margin-bottom: 18px; }
        .field-group.full { grid-column: span 2; }
        .field-group label { font-weight: 700; }
        .field-group input,
        .field-group textarea,
        .field-group select { width: 100%; padding: 14px 16px; border: 1px solid #cbd5e1; border-radius: 16px; font-size: 1rem; }
        .tag-box { display: flex; gap: 14px; flex-wrap: wrap; }
        .tag-item { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 18px; padding: 18px 20px; cursor: pointer; transition: border-color 0.2s; }
        .tag-item.active { border-color: #2563eb; background: #eff6ff; }
        .form-actions { display: flex; justify-content: flex-end; gap: 16px; margin-top: 18px; }
        .btn-secondary { border: 1px solid #cbd5e1; background: #fff; color: #334155; padding: 14px 24px; border-radius: 16px; text-decoration: none; font-weight: 700; }
        .btn-primary { border: none; background: #2563eb; color: #fff; padding: 14px 24px; border-radius: 16px; font-weight: 700; cursor: pointer; }
        .choice-cards { display: flex; gap: 12px; }
        .choice-card { display: flex; align-items: center; gap: 12px; padding: 16px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; cursor: pointer; flex: 1; }
        .choice-card input { display: none; }
        .choice-card .choice-title { font-weight: 800; }
        .choice-card .choice-desc { color: #6b7280; font-size: 0.95rem; }
        .choice-card input:checked + .choice-content { border-color: #2563eb; }
        .choice-card.selected { border-color: #2563eb; background: #eff6ff; }
    </style>
</head>
<body>
<div class="page-layout">
    <aside class="sidebar">
        <div class="logo">StudyTrack</div>
        <nav class="menu">
            <a href="/teacher" class="menu-item">Dashboard</a>
            <a href="/teacher/challenges" class="menu-item active">Daftar tantangan</a>
            <a href="/logout" class="menu-item">Keluar</a>
        </nav>
    </aside>
    <main class="main">
        <a href="/teacher/challenges" class="back-link">← Kembali ke daftar tantangan</a>
        <h1 class="page-title">Buat Tantangan Baru</h1>
        <p class="page-subtitle">Isi detail tantangan untuk siswa dan simpan.</p>

        <div class="form-card">
            <form action="/teacher/challenges/create" method="POST" enctype="multipart/form-data">
                <div class="field-grid">
                    <div class="field-group full">
                        <label for="title">Judul Tantangan</label>
                        <input type="text" id="title" name="title" placeholder="Masukkan judul tantangan" required>
                    </div>
                    <div class="field-group full">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" rows="5" placeholder="Masukkan deskripsi tantangan (opsional)"></textarea>
                    </div>
                    <div class="field-group">
                        <label for="category">Mata Pelajaran</label>
                        <select id="category" name="category" required>
                            <option value="Pelajaran">Pelajaran</option>
                            <option value="Bahasa Inggris">Bahasa Inggris</option>
                            <option value="Matematika">Matematika</option>
                            <option value="Sains">Sains</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="challenge_type">Tipe Tantangan</label>
                        <div class="choice-cards">
                            <label class="choice-card">
                                <input type="radio" name="challenge_type" value="Tugas" checked>
                                <div class="choice-content">
                                    <div class="choice-title">Tugas</div>
                                    <div class="choice-desc">Siswa mengerjakan tugas dan mengumpulkan hasil</div>
                                </div>
                            </label>
                            <label class="choice-card">
                                <input type="radio" name="challenge_type" value="Kuis">
                                <div class="choice-content">
                                    <div class="choice-title">Kuis</div>
                                    <div class="choice-desc">Siswa menjawab pertanyaan dalam bentuk kuis</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="field-group">
                        <label for="week_number">Tingkat/Kelas</label>
                        <input type="number" id="week_number" name="week_number" placeholder="Contoh: 1" min="1" value="1" required>
                    </div>
                    <div class="field-group">
                        <label for="points">Poin</label>
                        <input type="number" id="points" name="points" placeholder="Contoh: 100" min="0" value="0" required>
                    </div>
                    <div class="field-group full">
                        <label for="due_date">Tenggat Waktu</label>
                        <input type="datetime-local" id="due_date" name="due_date">
                    </div>
                    <div class="field-group full">
                        <label for="attachment">Lampiran (opsional)</label>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <input type="file" id="attachment" name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png">
                            <small style="color:#64748b;">Maks. 10MB (PDF, DOC, DOCX, PPT, PPTX, JPG, PNG)</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="/teacher/challenges" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Buat Tantangan</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
