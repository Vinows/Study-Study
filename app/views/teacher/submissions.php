<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header('Location: /login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submisi Siswa - StudyTrack</title>
    <link rel="stylesheet" href="/css/home.css">
    <style>
        body { font-family: Inter, sans-serif; background: #f1f5f9; color:#0f172a; margin:0 }
        .page { display:flex; min-height:100vh }
        .sidebar { width:280px; background:#0f172a; color:#fff; padding:32px }
        .main { flex:1; padding:36px }
        .card { background:#fff; border-radius:16px; padding:18px; box-shadow:0 12px 30px rgba(2,6,23,0.06); margin-bottom:16px }
        .meta { display:flex; gap:12px; align-items:center }
        .badge { padding:6px 10px; border-radius:999px; background:#e6f0ff; color:#0b5; font-weight:700 }
        .btn { padding:8px 12px; border-radius:8px; border:1px solid #cbd5e1; background:#fff; cursor:pointer }
    </style>
</head>
<body>
<div class="page">
    <aside class="sidebar">
        <div class="logo">StudyTrack</div>
        <nav class="menu">
            <a href="/teacher" class="menu-item">Dashboard</a>
            <a href="/teacher/challenges" class="menu-item">Daftar tantangan</a>
            <a href="/logout" class="menu-item">Keluar</a>
        </nav>
    </aside>
    <main class="main">
        <h1>Submisi Siswa</h1>
        <p>Daftar jawaban siswa untuk tantangan ini.</p>

        <?php if (empty($submissions)): ?>
            <div class="card">Belum ada submisi untuk tantangan ini.</div>
        <?php else: ?>
            <?php foreach ($submissions as $s): ?>
                <div class="card">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <div>
                            <strong><?= htmlspecialchars($s['student_name']) ?></strong>
                            <div style="color:#64748b;font-size:0.95rem"><?= htmlspecialchars($s['email']) ?></div>
                        </div>
                        <div class="meta">
                            <div style="font-weight:700;color:#475569"><?= date('d M Y H:i', strtotime($s['created_at'])) ?></div>
                            <div class="badge"><?= $s['status'] === 'graded' ? 'Dinilai' : 'Menunggu' ?></div>
                        </div>
                    </div>
                    <div style="margin-top:12px;color:#475569"><?= nl2br(htmlspecialchars(substr($s['answer_text'],0,500))) ?></div>
                    <?php if (!empty($s['attachment'])): ?>
                        <div style="margin-top:8px"><a href="<?= htmlspecialchars($s['attachment']) ?>" target="_blank">Lihat lampiran</a></div>
                    <?php endif; ?>
                    <?php if (!empty($s['grade'])): ?>
                        <div style="margin-top:8px;font-weight:700">Nilai: <?= intval($s['grade']) ?></div>
                        <div style="margin-top:6px;color:#334155">Feedback: <?= nl2br(htmlspecialchars($s['feedback'])) ?></div>
                    <?php endif; ?>
                    <div style="margin-top:12px;display:flex;gap:8px">
                        <a class="btn" href="/teacher/submissions/<?= $s['id'] ?>/grade">Beri Nilai</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
