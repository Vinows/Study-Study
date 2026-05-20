<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../app/config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header('Location: /login');
    exit;
}

$teacher_name = $_SESSION['user_name'] ?? 'Guru';
$result = $conn->query("SELECT * FROM challenges ORDER BY id DESC");
$challenges = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $challenges[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack - Tantangan Guru</title>
    <link rel="stylesheet" href="/css/home.css">
    <style>
        body { background: #ebf2ff; color: #1f2937; margin: 0; font-family: Inter, sans-serif; }
        .teacher-dashboard { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: #0f172a; color: #f8fafc; padding: 32px; display: flex; flex-direction: column; }
        .sidebar .logo { font-size: 2rem; font-weight: 900; margin-bottom: 2rem; letter-spacing: -0.03em; }
        .menu { display: flex; flex-direction: column; gap: 18px; }
        .menu-item { color: #cbd5e1; text-decoration: none; font-weight: 700; padding: 14px 16px; border-radius: 16px; transition: background 0.2s; }
        .menu-item:hover, .menu-item.active { background: rgba(255,255,255,0.08); color: #fff; }
        .main-content { flex: 1; padding: 34px; }
        .top-bar { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; margin-bottom: 30px; }
        .page-title { font-size: 2.4rem; margin: 0; }
        .page-subtitle { color: #64748b; margin-top: 8px; }
        .btn-primary { background: #1d4ed8; color: #fff; border: none; border-radius: 14px; padding: 14px 22px; font-weight: 700; cursor: pointer; }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; }
        .challenge-card { background: #fff; border-radius: 32px; padding: 30px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08); position: relative; }
        .challenge-top { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
        .challenge-title { font-size: 1.3rem; margin: 0 0 10px; }
        .badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 999px; background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.86rem; }
        .challenge-description { margin: 0 0 18px; color: #475569; line-height: 1.7; }
        .meta-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-bottom: 18px; }
        .meta-item { background: #f8fafc; padding: 14px 16px; border-radius: 18px; color: #334155; font-size: 0.95rem; }
        .tag-list { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .tag-pill { background: #e2e8f0; color: #1e293b; padding: 8px 12px; border-radius: 999px; font-size: 0.9rem; }
        .action-bar { display: flex; justify-content: space-between; align-items: center; gap: 16px; }
        .action-button { border: 1px solid #cbd5e1; border-radius: 14px; padding: 10px 14px; background: #fff; color: #1f2937; text-decoration: none; font-weight: 700; }
        .action-button.delete { border-color: #fecaca; color: #b91c1c; }
    </style>
</head>
<body>
<div class="teacher-dashboard">
    <aside class="sidebar">
        <div class="logo">StudyTrack</div>
        <nav class="menu">
            <a href="/teacher" class="menu-item">Dashboard</a>
            <a href="/teacher/challenges" class="menu-item active">Daftar tantangan</a>
            <a href="/logout" class="menu-item">Keluar</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h1 class="page-title">Daftar tantangan</h1>
                <p class="page-subtitle">Buat dan kelola tantangan siswa dari sini.</p>
            </div>
            <a href="/teacher/challenges/create" class="btn-primary">+ Buat Tantangan</a>
        </div>

        <div class="card-grid">
            <?php if (empty($challenges)): ?>
                <div class="challenge-card">
                    <p>Tidak ada tantangan saat ini. Klik Buat Tantangan untuk menambah.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($challenges as $challenge): ?>
                <div class="challenge-card">
                    <div class="challenge-top">
                        <div>
                            <h2 class="challenge-title"><?= htmlspecialchars($challenge['title']) ?></h2>
                            <div class="badge"><?= htmlspecialchars($challenge['challenge_type']) ?></div>
                        </div>
                        <span class="badge"><?= htmlspecialchars($challenge['status'] === 'active' ? 'In Progress' : 'Inactive') ?></span>
                    </div>
                    <p class="challenge-description"><?= htmlspecialchars($challenge['description']) ?></p>
                    <div class="tag-list">
                        <span class="tag-pill"><?= htmlspecialchars($challenge['category']) ?></span>
                        <span class="tag-pill"><?= htmlspecialchars($challenge['points']) ?> Poin</span>
                    </div>
                    <div class="meta-list">
                        <div class="meta-item">Minggu ke-<?= htmlspecialchars($challenge['week_number']) ?></div>
                        <div class="meta-item">Tenggat: <?= $challenge['due_date'] ? date('d M Y', strtotime($challenge['due_date'])) : 'Belum ditentukan' ?></div>
                    </div>
                    <div class="action-bar">
                        <a href="/teacher/challenges/<?= $challenge['id'] ?>/edit" class="action-button">Edit</a>
                        <a href="/teacher/challenges/<?= $challenge['id'] ?>/delete" class="action-button delete">Hapus</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>
</body>
</html>
