<?php
require '../app/config/db.php';

// Array untuk menyimpan history minggu-minggu sebelumnya
$history_data = [];

// Looping dari minggu pertama sampai minggu sebelum current_week
for ($w = 1; $w < $current_week; $w++) {
    // Total challenge di minggu ke-$w
    $t_query = $conn->query("SELECT COUNT(*) as count FROM challenges WHERE week_number = $w");
    $total = $t_query->fetch_assoc()['count'];

    // Total selesai di minggu ke-$w
    $c_query = $conn->query("
        SELECT COUNT(*) as count FROM user_challenges uc 
        JOIN challenges c ON uc.challenge_id = c.id 
        WHERE c.week_number = $w AND uc.user_id = $user_id AND uc.status = 'completed'
    ");
    $completed = $c_query->fetch_assoc()['count'];

    $pct = ($total > 0) ? round(($completed / $total) * 100) : 0;
    
    // Masukkan ke array
    $history_data[$w] = $pct;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>StudyTrack - History</title>
    <link rel="stylesheet" href="/css/home.css">
    <style>
        /* ... STYLE BAWAAN ANDA ... */
        .history-list { display: flex; flex-direction: column; gap: 30px; }
        .history-card { background: #ffffff; border: 3px solid #000; border-radius: 20px; padding: 25px 35px; display: flex; align-items: center; justify-content: space-between; box-shadow: 6px 6px 0px rgba(0, 0, 0, 0.05); }
        .progress-mini-outline { width: 90%; height: 40px; background: #eee; border: 3px solid #000; border-radius: 15px; overflow: hidden; }
        .progress-mini-fill { height: 100%; background: #76ff03; border-right: 3px solid #000; transition: 0.5s; }
        .arrow-icon { font-size: 2.5rem; cursor: pointer; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <h1 class="logo">StudyTrack</h1>
        <nav class="menu">
            <a href="challenge.php" class="menu-item">Challenge</a>
            <a href="progress.php" class="menu-item">Progress</a>
            <a href="history.php" class="menu-item active">History</a>
            <a href="profile.php" class="menu-item">Profile</a>
        </nav>
    </aside>

    <main class="main-content">
        <h2 class="section-title">History Progress</h2>
        <div class="history-list">
            
            <?php if(empty($history_data)): ?>
                <p style="color:white; font-size: 1.2rem;">Belum ada history karena ini masih minggu pertama.</p>
            <?php else: ?>
                <?php foreach($history_data as $minggu => $persentase): ?>
                <div class="history-card">
                    <div class="history-info">
                        <label>Progress Minggu <?= $minggu ?></label>
                        <div class="progress-mini-outline">
                            <div class="progress-mini-fill" style="width: <?= $persentase ?>%;"></div>
                        </div>
                    </div>
                    <div class="arrow-icon">❯</div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </main>
</div>
</body>
</html>