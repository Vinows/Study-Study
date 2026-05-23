<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../app/config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header('Location: /login');
    exit;
}

$user_id = $_SESSION['user_id'];
$teacher_name = $_SESSION['user_name'] ?? 'Pengajar';

$student_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'")->fetch_assoc()['count'];
$challenge_count = $conn->query("SELECT COUNT(*) as count FROM challenges")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack - Dashboard Guru</title>
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/dash.css">
</head>
<body>
    <div class="teacher-dashboard">
        <aside class="sidebar">
            <div class="logo">StudyTrack</div>
            <nav class="menu">
                <a href="/teacher" class="menu-item">Dashboard</a>
                <a href="/teacher/challenges" class="menu-item">Daftar tantangan</a>
                <a href="/logout" class="menu-item">Keluar</a>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Halo, <?= htmlspecialchars($teacher_name) ?></h1>
            <p>Ini adalah halaman khusus guru. Di sini Anda dapat lihat ringkasan siswa dan tantangan.</p>

            <div class="card-grid">
                <div class="card">
                    <div class="card-title">Jumlah Siswa</div>
                    <div class="card-value"><?= number_format($student_count) ?></div>
                </div>
                <div class="card">
                    <div class="card-title">Jumlah Tantangan</div>
                    <div class="card-value"><?= number_format($challenge_count) ?></div>
                </div>
                <div class="card">
                    <div class="card-title">Jenis Akun</div>
                    <div class="card-value">Guru</div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
