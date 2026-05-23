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
    WHERE c.week_number = $current_week AND (uc.status IS NULL OR uc.status != 'completed')";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>StudyTrack - Tantangan</title>
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/challengemurid.css">
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
                <?php if ($result && $result->num_rows === 0): ?>
                    <div class="challenge-card" style="text-align:center; padding: 40px;">
                        <h3>Tidak ada tantangan saat ini.</h3>
                        <p style="color:#475569; margin-top: 12px;">Guru belum menambahkan tantangan. Silakan kembali nanti atau hubungi guru Anda.</p>
                    </div>
                <?php else: ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <a href="/challenge/take" class="challenge-card" style="text-decoration:none;color:inherit;">
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
                                    <?= $row['status'] === 'completed' ? 'Selesai' : 'Klik untuk melihat soal' ?>
                                </div>
                            </div>
                            <p><?= htmlspecialchars($row['description']) ?></p>
                            <div class="meta-row">
                                <div class="meta-card">Minggu ke-<?= intval($row['week_number']) ?></div>
                                <div class="meta-card">Deadline: <?= $row['due_date'] ? date('d M Y', strtotime($row['due_date'])) : 'Belum ditentukan' ?></div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

</body>
</html>