<?php
require '../app/config/db.php';

// Hitung Total Challenge minggu ini
$total_query = $conn->query("SELECT COUNT(*) as count FROM challenges WHERE week_number = $current_week");
$total_challenges = $total_query->fetch_assoc()['count'];

// Hitung Challenge yang sudah 'completed' oleh user minggu ini
$completed_query = $conn->query("
    SELECT COUNT(*) as count FROM user_challenges uc 
    JOIN challenges c ON uc.challenge_id = c.id 
    WHERE c.week_number = $current_week AND uc.user_id = $user_id AND uc.status = 'completed'
");
$completed_challenges = $completed_query->fetch_assoc()['count'];

// Hitung persentase
$progress_percentage = 0;
if ($total_challenges > 0) {
    $progress_percentage = round(($completed_challenges / $total_challenges) * 100);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>StudyTrack - Progress</title>
    <link rel="stylesheet" href="/css/home.css">
    <style>
        /* ... STYLE BAWAAN ANDA ... */
        .progress-container { background: white; border: 2px solid #000; border-radius: 25px; padding: 40px; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); }
        .progress-title { font-weight: 800; font-size: 1.5rem; margin-bottom: 20px; display: block; }
        .progress-bar-outline { width: 100%; height: 50px; background: #eee; border: 2px solid #000; border-radius: 15px; overflow: hidden; margin-bottom: 25px; }
        .progress-bar-fill { height: 100%; background: #76ff03; border-right: 2px solid #000; transition: width 0.5s ease-in-out; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <h1 class="logo">StudyTrack</h1>
        <nav class="menu">
            <a href="challenge.php" class="menu-item">Challenge</a>
            <a href="progress.php" class="menu-item active">Progress</a>
            <a href="history.php" class="menu-item">History</a>
            <a href="profile.php" class="menu-item">Profile</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="progress-container">
            <span class="progress-title">Progress Minggu <?= $current_week ?></span>
            
            <div class="progress-bar-outline">
                <div class="progress-bar-fill" style="width: <?= $progress_percentage ?>%;"></div>
            </div>

            <p class="progress-text">Kamu telah menyelesaikan <?= $completed_challenges ?> dari <?= $total_challenges ?> challenge (<?= $progress_percentage ?>%).</p>
        </div>
    </main>
</div>
</body>
</html>