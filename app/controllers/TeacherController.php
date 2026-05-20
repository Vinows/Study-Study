<?php
namespace App\Controllers;

class TeacherController {
    protected function guardTeacher() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
            header('Location: /login');
            exit;
        }
    }

    public function Teacher() {
        $this->guardTeacher();
        require_once '../app/views/teacher/dashboard.php';
    }

    public function Challenges() {
        $this->guardTeacher();
        require_once '../app/views/teacher/challenges.php';
    }

    public function CreateChallenge() {
        $this->guardTeacher();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require '../app/config/db.php';
            $title = $conn->real_escape_string(trim($_POST['title'] ?? ''));
            $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
            $category = $conn->real_escape_string(trim($_POST['category'] ?? 'Umum'));
            $week_number = intval($_POST['week_number'] ?? 1);
            $due_date = $conn->real_escape_string(trim($_POST['due_date'] ?? ''));
            $points = intval($_POST['points'] ?? 0);
            $challenge_type = $conn->real_escape_string(trim($_POST['challenge_type'] ?? 'Tugas'));

            $due_date_sql = $due_date !== '' ? "'$due_date'" : 'NULL';
            $conn->query("INSERT INTO challenges (title, description, category, week_number, due_date, points, challenge_type) VALUES ('$title', '$description', '$category', $week_number, $due_date_sql, $points, '$challenge_type')");
            header('Location: /teacher/challenges');
            exit;
        }
        require_once '../app/views/teacher/create_challenge.php';
    }

    public function EditChallenge($id) {
        $this->guardTeacher();
        require '../app/config/db.php';
        $challenge_id = intval($id);
        $challenge = null;
        $result = $conn->query("SELECT * FROM challenges WHERE id = $challenge_id");
        if ($result && $result->num_rows > 0) {
            $challenge = $result->fetch_assoc();
        }
        if (!$challenge) {
            header('Location: /teacher/challenges');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $conn->real_escape_string(trim($_POST['title'] ?? ''));
            $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
            $category = $conn->real_escape_string(trim($_POST['category'] ?? 'Umum'));
            $week_number = intval($_POST['week_number'] ?? 1);
            $due_date = $conn->real_escape_string(trim($_POST['due_date'] ?? ''));
            $points = intval($_POST['points'] ?? 0);
            $challenge_type = $conn->real_escape_string(trim($_POST['challenge_type'] ?? 'Tugas'));
            $due_date_sql = $due_date !== '' ? "'$due_date'" : 'NULL';

            $conn->query("UPDATE challenges SET title='$title', description='$description', category='$category', week_number=$week_number, due_date=$due_date_sql, points=$points, challenge_type='$challenge_type' WHERE id = $challenge_id");
            header('Location: /teacher/challenges');
            exit;
        }

        require_once '../app/views/teacher/edit_challenge.php';
    }

    public function DeleteChallenge($id) {
        $this->guardTeacher();
        require '../app/config/db.php';
        $challenge_id = intval($id);
        $conn->query("DELETE FROM challenges WHERE id = $challenge_id");
        header('Location: /teacher/challenges');
        exit;
    }
}
