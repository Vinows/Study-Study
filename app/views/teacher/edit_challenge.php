<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header('Location: /login');
    exit;
}
if (!isset($challenge) || !is_array($challenge)) {
    header('Location: /teacher/challenges');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack - Edit Tantangan</title>
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
        .form-actions { display: flex; justify-content: flex-end; gap: 16px; margin-top: 18px; }
        .btn-secondary { border: 1px solid #cbd5e1; background: #fff; color: #334155; padding: 14px 24px; border-radius: 16px; text-decoration: none; font-weight: 700; }
        .btn-primary { border: none; background: #2563eb; color: #fff; padding: 14px 24px; border-radius: 16px; font-weight: 700; cursor: pointer; }
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
        <h1 class="page-title">Edit Tantangan</h1>
        <p class="page-subtitle">Perbarui detail tantangan agar siswa mendapatkan instruksi yang tepat.</p>

        <div class="form-card">
            <form action="/teacher/challenges/<?= $challenge['id'] ?>/edit" method="POST">
                <div class="field-grid">
                    <div class="field-group full">
                        <label for="title">Judul Tantangan</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($challenge['title']) ?>" required>
                    </div>
                    <div class="field-group full">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" rows="5"><?= htmlspecialchars($challenge['description']) ?></textarea>
                    </div>
                    <div class="field-group">
                        <label for="category">Mata Pelajaran</label>
                        <select id="category" name="category" required>
                            <option value="Pelajaran" <?= $challenge['category'] === 'Pelajaran' ? 'selected' : '' ?>>Pelajaran</option>
                            <option value="Bahasa Inggris" <?= $challenge['category'] === 'Bahasa Inggris' ? 'selected' : '' ?>>Bahasa Inggris</option>
                            <option value="Matematika" <?= $challenge['category'] === 'Matematika' ? 'selected' : '' ?>>Matematika</option>
                            <option value="Sains" <?= $challenge['category'] === 'Sains' ? 'selected' : '' ?>>Sains</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="challenge_type">Tipe Tantangan</label>
                        <select id="challenge_type" name="challenge_type" required>
                            <option value="Tugas" <?= $challenge['challenge_type'] === 'Tugas' ? 'selected' : '' ?>>Tugas</option>
                            <option value="Kuis" <?= $challenge['challenge_type'] === 'Kuis' ? 'selected' : '' ?>>Kuis</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="week_number">Tingkat/Kelas</label>
                        <input type="number" id="week_number" name="week_number" min="1" value="<?= htmlspecialchars($challenge['week_number']) ?>" required>
                    </div>
                    <div class="field-group">
                        <label for="points">Poin</label>
                        <input type="number" id="points" name="points" min="0" value="<?= htmlspecialchars($challenge['points']) ?>" required>
                    </div>
                    <div class="field-group full">
                        <label for="due_date">Waktu Tenggat</label>
                        <input type="datetime-local" id="due_date" name="due_date" value="<?= $challenge['due_date'] ? date('Y-m-d\TH:i', strtotime($challenge['due_date'])) : '' ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="/teacher/challenges" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
