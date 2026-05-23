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
    "CREATE TABLE IF NOT EXISTS questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        challenge_id INT NOT NULL,
        question_text TEXT NOT NULL,
        answer_type ENUM('essay','multiple_choice') NOT NULL DEFAULT 'essay',
        options TEXT NULL,
        position INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (challenge_id) REFERENCES challenges(id) ON DELETE CASCADE
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
    "CREATE TABLE IF NOT EXISTS submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        challenge_id INT NOT NULL,
        answer_text TEXT NULL,
        attachment VARCHAR(255) NULL,
        grade INT NULL,
        feedback TEXT NULL,
        status ENUM('pending','graded') NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        graded_at DATETIME NULL,
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
    'answer_type' => "ALTER TABLE challenges ADD COLUMN answer_type ENUM('essay','multiple_choice') NOT NULL DEFAULT 'essay'",
    'options' => "ALTER TABLE challenges ADD COLUMN options TEXT NULL",
    'status' => "ALTER TABLE challenges ADD COLUMN status ENUM('active','inactive') NOT NULL DEFAULT 'active'",
    'attachment' => "ALTER TABLE challenges ADD COLUMN attachment VARCHAR(255) NULL",
];

foreach ($fieldsToAdd as $field => $sql) {
    $check = $conn->query("SHOW COLUMNS FROM challenges LIKE '$field'");
    if ($check && $check->num_rows === 0) {
        $conn->query($sql);
    }
}

// Ensure submissions table has attachment column (in case it was created earlier without it)
$checkSubAttach = $conn->query("SHOW COLUMNS FROM submissions LIKE 'attachment'");
if (!($checkSubAttach && $checkSubAttach->num_rows > 0)) {
    $conn->query("ALTER TABLE submissions ADD COLUMN attachment VARCHAR(255) NULL");
}

// Ensure submissions has grade, feedback, status, graded_at columns
$checkGrade = $conn->query("SHOW COLUMNS FROM submissions LIKE 'grade'");
if (!($checkGrade && $checkGrade->num_rows > 0)) {
    $conn->query("ALTER TABLE submissions ADD COLUMN grade INT NULL");
}
$checkFeedback = $conn->query("SHOW COLUMNS FROM submissions LIKE 'feedback'");
if (!($checkFeedback && $checkFeedback->num_rows > 0)) {
    $conn->query("ALTER TABLE submissions ADD COLUMN feedback TEXT NULL");
}
$checkStatus = $conn->query("SHOW COLUMNS FROM submissions LIKE 'status'");
if (!($checkStatus && $checkStatus->num_rows > 0)) {
    $conn->query("ALTER TABLE submissions ADD COLUMN status ENUM('pending','graded') NOT NULL DEFAULT 'pending'");
}
$checkGradedAt = $conn->query("SHOW COLUMNS FROM submissions LIKE 'graded_at'");
if (!($checkGradedAt && $checkGradedAt->num_rows > 0)) {
    $conn->query("ALTER TABLE submissions ADD COLUMN graded_at DATETIME NULL");
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
// Do not auto-seed challenges. The app should start empty until the teacher adds content.
$current_week = 1;
?>