<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'studytrack';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

try {
    $conn->select_db($db);
} catch (\mysqli_sql_exception $e) {
    $createDb = "CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if (! $conn->query($createDb)) {
        die('Koneksi database gagal: ' . $conn->error);
    }
    try {
        $conn->select_db($db);
    } catch (\mysqli_sql_exception $e2) {
        die('Koneksi database gagal: ' . $e2->getMessage());
    }
}

$schema = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('student','teacher') NOT NULL DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS challenges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        category VARCHAR(100) NOT NULL,
        week_number INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS user_challenges (
        user_id INT NOT NULL,
        challenge_id INT NOT NULL,
        status ENUM('pending','completed') NOT NULL DEFAULT 'pending',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY(user_id, challenge_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (challenge_id) REFERENCES challenges(id) ON DELETE CASCADE
    )",
];

foreach ($schema as $sql) {
    $conn->query($sql);
}

$fieldsToAdd = [
    'due_date' => "ALTER TABLE challenges ADD COLUMN due_date DATETIME NULL",
    'points' => "ALTER TABLE challenges ADD COLUMN points INT NOT NULL DEFAULT 0",
    'challenge_type' => "ALTER TABLE challenges ADD COLUMN challenge_type VARCHAR(50) NOT NULL DEFAULT 'Tugas'",
    'status' => "ALTER TABLE challenges ADD COLUMN status ENUM('active','inactive') NOT NULL DEFAULT 'active'",
];

foreach ($fieldsToAdd as $field => $sql) {
    $check = $conn->query("SHOW COLUMNS FROM challenges LIKE '$field'");
    if ($check && $check->num_rows === 0) {
        $conn->query($sql);
    }
}

$defaultTeacher = $conn->query("SELECT id FROM users WHERE email = 'teacher@studytrack.local'")->num_rows;
if (! $defaultTeacher) {
    $password = password_hash('teacher123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (name, email, password, role) VALUES ('Teacher Default', 'teacher@studytrack.local', '$password', 'teacher')");
}

$defaultStudent = $conn->query("SELECT id FROM users WHERE email = 'student@studytrack.local'")->num_rows;
if (! $defaultStudent) {
    $password = password_hash('student123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (name, email, password, role) VALUES ('Student Default', 'student@studytrack.local', '$password', 'student')");
}

$challengeCount = $conn->query("SELECT COUNT(*) as cnt FROM challenges")->fetch_assoc()['cnt'];
if ($challengeCount == 0) {
    $conn->query("INSERT INTO challenges (title, description, category, week_number) VALUES
        ('Mulai Hari dengan Kosakata Baru', 'Pelajari 10 kata baru hari ini dengan kuis cepat.', 'Vocabulary', 1),
        ('Baca Paragraf Pendek', 'Baca paragraf bahasa target selama 5 menit dan catat 3 kosakata baru.', 'Reading', 1),
        ('Latihan Mendengarkan', 'Dengarkan dialog pendek dan tulis ringkasannya.', 'Listening', 1)");
}

$current_week = 1;
?>