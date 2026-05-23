<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'studytrack';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error . "\n");
}

$conn->query('SET FOREIGN_KEY_CHECKS=0');
$tables = ['grades', 'submissions', 'user_challenges', 'challenges'];
foreach ($tables as $table) {
    $ping = $conn->query("SHOW TABLES LIKE '$table'");
    if ($ping && $ping->num_rows > 0) {
        $conn->query("TRUNCATE TABLE $table");
    }
}
$conn->query('SET FOREIGN_KEY_CHECKS=1');

$paths = [
    __DIR__ . '/../public/uploads/challenges',
    __DIR__ . '/../public/uploads/submissions',
];
foreach ($paths as $path) {
    if (!is_dir($path)) continue;
    $files = scandir($path);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $full = $path . DIRECTORY_SEPARATOR . $file;
        if (is_file($full)) unlink($full);
    }
}

echo "Reset selesai. Semua tantangan, submisi, dan tautan pengguna dihapus.\n";
