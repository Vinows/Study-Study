<?php
$host = 'localhost';
$user = 'root'; // Sesuaikan username database
$pass = '';     // Sesuaikan password database
$db   = 'studytrack';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Minggu berjalan (tetap dipertahankan untuk halaman challenge & progress)
$current_week = 1; 
?>