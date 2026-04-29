<?php
// db.php
$host = 'localhost';
$user = 'root'; // Sesuaikan dengan username database kamu
$pass = '';     // Sesuaikan dengan password database kamu
$db   = 'studytrack';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Simulasi Sistem Login (Anggap user yang sedang login adalah Lawrence dengan ID 1)
$user_id = 1; 

// Simulasi Minggu Berjalan (Ubah angka ini menjadi 2 jika ingin melihat simulasi minggu depannya)
$current_week = 1; 
?>