<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../app/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

if (($_SESSION['role'] ?? 'student') === 'teacher') {
    header('Location: /teacher');
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['complete_challenge'])) {
    $cid = intval($_POST['challenge_id']);
    $conn->query("INSERT INTO user_challenges (user_id, challenge_id, status) 
                VALUES ($user_id, $cid, 'completed') 
                ON DUPLICATE KEY UPDATE status='completed'");
}

$query = "SELECT c.*, uc.status 
        FROM challenges c 
        LEFT JOIN user_challenges uc ON c.id = uc.challenge_id AND uc.user_id = $user_id 
        WHERE c.week_number = $current_week";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>StudyTrack - Tantangan</title>
    <link rel="stylesheet" href="/css/home.css">
    <style>
        body { margin: 0; font-family: Inter, sans-serif; background: #eef2ff; color: #0f172a; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #111827; padding: 32px; color: #f8fafc; }
        .logo { font-size: 2rem; font-weight: 900; margin-bottom: 2rem; }
        .menu { display: flex; flex-direction: column; gap: 16px; }
        .menu-item { display: block; color: #cbd5e1; padding: 14px 16px; border-radius: 16px; text-decoration: none; font-weight: 700; }
        .menu-item.active, .menu-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .main-content { flex: 1; padding: 36px 42px; }
        .hero-card { background: #fff; border-radius: 34px; padding: 28px 34px; box-shadow: 0 24px 60px rgba(15,23,42,0.08); display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
        .hero-card h1 { margin: 0 0 10px; font-size: 2rem; }
        .hero-card p { margin: 0; color: #475569; }
        .overview-pill { background: #e0f2fe; color: #0c4a6e; padding: 14px 20px; border-radius: 999px; font-weight: 700; }
        .challenge-list { display: grid; gap: 24px; }
        .challenge-card { background: #fff; border-radius: 32px; padding: 28px; box-shadow: 0 20px 45px rgba(15,23,42,0.08); }
        .challenge-card h3 { margin: 0 0 10px; font-size: 1.5rem; }
        .challenge-card p { margin: 0 0 18px; color: #475569; line-height: 1.8; }
        .tag-row { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 18px; }
        .tag-pill { background: #eef2ff; color: #1d4ed8; padding: 10px 14px; border-radius: 999px; font-weight: 700; }
        .meta-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 20px; }
        .meta-card { background: #f8fafc; padding: 16px 18px; border-radius: 20px; color: #334155; font-weight: 600; }
        .status-badge { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 999px; font-weight: 700; }
        .status-pending { background: #fef9c3; color: #92400e; }
        .status-done { background: #dcfce7; color: #166534; }
        .btn-complete, .btn-done { border: none; border-radius: 18px; padding: 14px 24px; font-weight: 700; }
        .btn-complete { background: #1d4ed8; color: #fff; cursor: pointer; }
        .btn-done { background: #10b981; color: #fff; cursor: default; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">StudyTrack</div>
            <nav class="menu">
                <a href="/challenge" class="menu-item active">Challenge</a>
                <a href="/progress" class="menu-item">Progress</a>
                <a href="/history" class="menu-item">History</a>
                <a href="/profile" class="menu-item">Profile</a>
                <a href="/logout" class="menu-item">Keluar</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="hero-card">
                <div>
                    <h1>Tantangan Mingguan</h1>
                    <p>Kerjakan tantangan terbaru dan tingkatkan progres belajarmu.</p>
                </div>
                <div class="overview-pill">Minggu <?= $current_week ?></div>
            </div>

            <div class="challenge-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="challenge-card">
                        <div class="meta-row" style="margin-bottom: 18px; align-items: center;">
                            <div>
                                <h3><?= htmlspecialchars($row['title']) ?></h3>
                                <div class="tag-row">
                                    <span class="tag-pill"><?= htmlspecialchars($row['category']) ?></span>
                                    <span class="tag-pill"><?= htmlspecialchars($row['challenge_type']) ?></span>
                                    <span class="tag-pill"><?= intval($row['points']) ?> Poin</span>
                                </div>
                            </div>
                            <div class="status-badge <?= $row['status'] === 'completed' ? 'status-done' : 'status-pending' ?>">
                                <?= $row['status'] === 'completed' ? 'Selesai' : 'In Progress' ?>
                            </div>
                        </div>
                        <p><?= htmlspecialchars($row['description']) ?></p>
                        <div class="meta-row">
                            <div class="meta-card">Minggu ke-<?= intval($row['week_number']) ?></div>
                            <div class="meta-card">Tenggat: <?= $row['due_date'] ? date('d M Y', strtotime($row['due_date'])) : 'Belum ditentukan' ?></div>
                        </div>
                        <?php if ($row['status'] === 'completed'): ?>
                            <button class="btn-done" disabled>Sudah Selesai ✓</button>
                        <?php else: ?>
                            <?php if ($row['challenge_type'] === 'Tugas'): ?>
                                <form action="/challenge/submit" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="challenge_id" value="<?= $row['id'] ?>">
                                    <div style="margin:12px 0;">
                                        <textarea name="answer_text" placeholder="Tulis jawaban singkat atau instruksi pengumpulan..." rows="3" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e5e7eb"></textarea>
                                    </div>
                                    <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">
                                        <input type="file" name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png">
                                    </div>
                                    <button type="submit" name="submit_answer" class="btn-complete">Kirim Jawaban</button>
                                </form>
                            <?php else: ?>
                                <form method="POST">
                                    <input type="hidden" name="challenge_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="complete_challenge" class="btn-complete">Selesaikan</button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>
</body>
</html>