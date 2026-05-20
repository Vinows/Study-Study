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
    <title>StudyTrack - Challenge</title>
    <link rel="stylesheet" href="/css/home.css">
    <style>
        .btn-complete {
            margin-top: 15px; padding: 10px 20px; background: #000; color: #fff;
            border: none; border-radius: 10px; cursor: pointer; font-weight: bold;
        }
        .btn-done {
            margin-top: 15px; padding: 10px 20px; background: #76ff03; color: #000;
            border: 2px solid #000; border-radius: 10px; font-weight: bold; cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h1 class="logo">StudyTrack</h1>
            <nav class="menu">
                <a href="/challenge" class="menu-item active">Challenge</a>
                <a href="/progress" class="menu-item">Progress</a>
                <a href="/history" class="menu-item">History</a>
                <a href="/profile" class="menu-item">Profile</a>
                <a href="/logout" class="menu-item">Keluar</a>
            </nav>
        </aside>
        
        <main class="main-content">
            <h2 class="section-title">Tantangan Minggu <?= $current_week ?></h2>
            <div class="challenge-list">
                
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="challenge-card">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <span class="category"><?= htmlspecialchars($row['category']) ?></span>
                    <br>
                    
                    <?php if($row['status'] == 'completed'): ?>
                        <button class="btn-done" disabled>Sudah Selesai ✓</button>
                    <?php else: ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="challenge_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="complete_challenge" class="btn-complete">Selesaikan</button>
                        </form>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>

            </div>
        </main>
    </div>
</body>
</html>