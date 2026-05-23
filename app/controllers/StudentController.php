<?php
namespace App\Controllers;

class StudentController {
    protected function guardStudent() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public function SubmitChallenge() {
        $this->guardStudent();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /challenge');
            exit;
        }
        require '../app/config/db.php';
        $user_id = intval($_SESSION['user_id']);
        $challenge_id = intval($_POST['challenge_id'] ?? 0);
        $answer_text = $conn->real_escape_string(trim($_POST['answer_text'] ?? ''));

        $attachment_path = null;
        if (!empty($_FILES['attachment']['name'])) {
            $tmp = $_FILES['attachment']['tmp_name'];
            $orig = basename($_FILES['attachment']['name']);
            $ext = pathinfo($orig, PATHINFO_EXTENSION);
            $allowed = ['pdf','doc','docx','ppt','pptx','jpg','jpeg','png'];
            if (in_array(strtolower($ext), $allowed) && $_FILES['attachment']['size'] <= 10 * 1024 * 1024) {
                $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $uploadDir = __DIR__ . '/../../public/uploads/submissions';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                if (move_uploaded_file($tmp, $uploadDir . '/' . $newName)) {
                    $attachment_path = '/uploads/submissions/' . $newName;
                }
            }
        }

        $attachment_sql = $attachment_path ? "'" . $conn->real_escape_string($attachment_path) . "'" : 'NULL';
        $answer_sql = $conn->real_escape_string($answer_text);

        $conn->query("INSERT INTO submissions (user_id, challenge_id, answer_text, attachment) VALUES ($user_id, $challenge_id, '$answer_sql', $attachment_sql)");

        // mark user_challenges as pending (insert or update)
        $conn->query("INSERT INTO user_challenges (user_id, challenge_id, status) VALUES ($user_id, $challenge_id, 'pending') ON DUPLICATE KEY UPDATE status='pending'");

        header('Location: /challenge');
        exit;
    }
}
