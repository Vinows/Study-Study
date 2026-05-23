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

$total_query = $conn->query("SELECT COUNT(*) as count FROM challenges WHERE week_number = $current_week");
$total_challenges = $total_query->fetch_assoc()['count'];

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

// Fetch student submissions with challenge info
$sub_q = $conn->query("SELECT s.*, c.title AS challenge_title FROM submissions s JOIN challenges c ON s.challenge_id = c.id WHERE s.user_id = $user_id ORDER BY s.created_at DESC");
$submissions = [];
if ($sub_q) while ($r = $sub_q->fetch_assoc()) $submissions[] = $r;
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
            <a href="/challenge" class="menu-item">Challenge</a>
            <a href="/progress" class="menu-item active">Progress</a>
            <a href="/history" class="menu-item">History</a>
            <a href="/profile" class="menu-item">Profile</a>
            <a href="/logout" class="menu-item">Keluar</a>
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
        
        <div style="margin-top:28px">
            <h2 style="margin:0 0 12px">Per-Challenge Progress</h2>
            <?php
            // Fetch challenges for the week with user's status and latest submission grade
            $subLatest = "SELECT sub.challenge_id, sub.grade FROM submissions sub JOIN (SELECT challenge_id, MAX(id) AS max_id FROM submissions WHERE user_id = $user_id GROUP BY challenge_id) m ON sub.challenge_id = m.challenge_id AND sub.id = m.max_id";
            $chQuery = "SELECT c.*, COALESCE(uc.status, 'pending') AS uc_status, sl.grade AS submission_grade FROM challenges c LEFT JOIN user_challenges uc ON c.id = uc.challenge_id AND uc.user_id = $user_id LEFT JOIN ($subLatest) sl ON sl.challenge_id = c.id WHERE c.week_number = $current_week ORDER BY c.id ASC";
            $chRes = $conn->query($chQuery);
            if (!$chRes || $chRes->num_rows === 0) {
                echo '<div class="card" style="padding:18px;border-radius:12px;background:#fff">Belum ada tantangan untuk minggu ini.</div>';
            } else {
                echo '<div class="challenge-progress-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px">';
                while ($ch = $chRes->fetch_assoc()) {
                    $completed = ($ch['uc_status'] === 'completed');
                    $gradeVal = isset($ch['submission_grade']) ? intval($ch['submission_grade']) : null;
                    $barPct = 0;
                    if ($gradeVal !== null) {
                        $barPct = max(0, min(100, $gradeVal));
                    } elseif ($completed) {
                        $barPct = 100;
                    }
                    $barColor = $barPct >= 100 ? '#10b981' : ($barPct > 0 ? '#f59e0b' : '#e2e8f0');
                    echo '<div class="card" style="background:#fff;padding:16px;border-radius:12px;box-shadow:0 8px 24px rgba(2,6,23,0.06)">';
                    echo '<div style="font-weight:800;margin-bottom:8px">' . htmlspecialchars($ch['title']) . '</div>';
                    echo '<div style="font-size:0.9rem;color:#64748b;margin-bottom:10px">' . htmlspecialchars(substr($ch['description'],0,120)) . '</div>';
                    echo '<div style="background:#f1f5f9;border-radius:12px;padding:8px">';
                    echo '<div style="height:12px;background:#e6eef8;border-radius:8px;overflow:hidden;">';
                    echo '<div style="width:' . $barPct . '%;height:100%;background:' . $barColor . ';transition:width:300ms"></div>';
                    echo '</div>';
                    echo '<div style="display:flex;justify-content:space-between;margin-top:8px;font-weight:700;color:#334155">';
                    echo '<div>' . ($barPct >= 100 ? 'Selesai' : ($barPct > 0 ? 'Dalam Penilaian' : 'Belum Selesai')) . '</div>';
                    echo '<div>' . ($gradeVal !== null ? ($gradeVal . '%') : ($barPct >= 100 ? '100%' : '0%')) . '</div>';
                    echo '</div></div></div>';
                }
                echo '</div>';
            }
            ?>
        </div>
    </main>
</div>
</body>
</html>