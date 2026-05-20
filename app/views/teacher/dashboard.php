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
    <style>
        body { background: #f7f8fb; color: #1f2937; }
        .teacher-dashboard { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #111827; color: #f9fafb; padding: 32px; }
        .sidebar .logo { font-size: 1.8rem; font-weight: 800; margin-bottom: 2rem; }
        .menu { display: flex; flex-direction: column; gap: 14px; }
        .menu-item { color: #d1d5db; text-decoration: none; font-weight: 600; }
        .menu-item:hover { color: #fff; }
        .main-content { padding: 40px; width: 100%; }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; }
        .card { border-radius: 24px; padding: 28px; background: #fff; box-shadow: 0 20px 55px rgba(15, 23, 42, 0.08); }
        .card-title { font-size: 1.1rem; margin-bottom: 12px; font-weight: 700; }
        .card-value { font-size: 2.6rem; font-weight: 800; color: #111827; }
    </style>
</head>
<body>
    <div class="teacher-dashboard">
        <aside class="sidebar">
            <div class="logo">StudyTrack Guru</div>
            <nav class="menu">
                <a href="/teacher" class="menu-item">Dashboard</a>
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
