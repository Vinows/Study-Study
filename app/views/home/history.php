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

$history_by_week = [];
// Get latest submission per challenge for this user
$subLatest = "SELECT sub.challenge_id, sub.grade, sub.feedback, sub.graded_at FROM submissions sub JOIN (SELECT challenge_id, MAX(id) AS max_id FROM submissions WHERE user_id = $user_id GROUP BY challenge_id) m ON sub.challenge_id = m.challenge_id AND sub.id = m.max_id";

$histQuery = "SELECT c.*, uc.status, sl.grade, sl.feedback, sl.graded_at FROM challenges c JOIN user_challenges uc ON c.id = uc.challenge_id AND uc.user_id = $user_id AND uc.status = 'completed' LEFT JOIN ($subLatest) sl ON sl.challenge_id = c.id ORDER BY c.week_number DESC, sl.graded_at DESC";
$res = $conn->query($histQuery);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $wk = intval($row['week_number']);
        if (!isset($history_by_week[$wk])) $history_by_week[$wk] = [];
        $history_by_week[$wk][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>StudyTrack - History</title>
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/history.css">
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <h1 class="logo">StudyTrack</h1>
        <nav class="menu">
            <a href="/challenge" class="menu-item">Challenge</a>
            <a href="/progress" class="menu-item">Progress</a>
            <a href="/history" class="menu-item active">History</a>
            <a href="/profile" class="menu-item">Profile</a>
            <a href="/logout" class="menu-item">Keluar</a>
        </nav>
        <div class="sidebar-mascot">
            <img src="/assets/Image1.png" alt="Mascot">
        </div>
    </aside>

    <main class="main-content">
        <h2 class="section-title">History</h2>
        <div class="history-list">
            
            <?php if(empty($history_by_week)): ?>
                <p style="color:white; font-size: 1.2rem;">Belum ada history yang sudah dinilai.</p>
            <?php else: ?>
                <?php foreach($history_by_week as $minggu => $items): ?>
                    <h3 style="color:#fff;margin-bottom:8px">Minggu <?= $minggu ?></h3>
                    <?php foreach($items as $it): ?>
                        <div class="history-card">
                            <div style="max-width:80%">
                                <div style="font-weight:800; font-size:1.05rem"><?= htmlspecialchars($it['title']) ?></div>
                                <div style="color:#64748b; margin-top:6px"><?= htmlspecialchars($it['description']) ?></div>
                                <?php if (!empty($it['grade']) || !empty($it['feedback'])): ?>
                                    <div style="margin-top:10px; background:#f8fafc; padding:12px; border-radius:8px; color:#334155">
                                        <?php if (!empty($it['grade'])): ?>
                                            <div><strong>Nilai:</strong> <?= intval($it['grade']) ?>%</div>
                                        <?php endif; ?>
                                        <?php if (!empty($it['feedback'])): ?>
                                            <div style="margin-top:6px"><strong>Feedback:</strong> <?= nl2br(htmlspecialchars($it['feedback'])) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div style="text-align:right">
                                <div style="font-weight:700">Selesai</div>
                                <div style="color:#64748b;margin-top:8px">Dinilai: <?= $it['graded_at'] ? date('d M Y', strtotime($it['graded_at'])) : '-' ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </main>
</div>
</body>
</html>