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

        // If the schema uses `student_id` (older installs), populate it as well to avoid missing-not-default errors
        $checkStudentCol = $conn->query("SHOW COLUMNS FROM submissions LIKE 'student_id'");
        if ($checkStudentCol && $checkStudentCol->num_rows > 0) {
            $conn->query("INSERT INTO submissions (user_id, student_id, challenge_id, answer_text, attachment) VALUES ($user_id, $user_id, $challenge_id, '$answer_sql', $attachment_sql)");
        } else {
            $conn->query("INSERT INTO submissions (user_id, challenge_id, answer_text, attachment) VALUES ($user_id, $challenge_id, '$answer_sql', $attachment_sql)");
        }

        // mark user_challenges with a valid status value (detect enum values to avoid truncation)
        // For UX: mark as 'completed' so submitted challenges disappear from the main list immediately
        $statusToSet = 'completed';
        $col = $conn->query("SHOW COLUMNS FROM user_challenges LIKE 'status'");
        if ($col && $col->num_rows > 0) {
            $c = $col->fetch_assoc();
            if (isset($c['Type'])) {
                // Parse enum(...) safely. Example: enum('pending','completed')
                if (preg_match("/^enum\\((.*)\\)$/", $c['Type'], $m)) {
                    $inside = $m[1]; // "'pending','completed'"
                    $inside = trim($inside);
                    $inside = trim($inside, "'\"");
                    $opts = explode("','", $inside);
                    $default = $c['Default'] ?? null;
                    if (!empty($opts)) {
                        if (!in_array($statusToSet, $opts)) {
                            $statusToSet = $default ? $default : $opts[0];
                        }
                    } elseif ($default) {
                        $statusToSet = $default;
                    }
                } elseif (!empty($c['Default'])) {
                    $statusToSet = $c['Default'];
                }
            }
        }
        $statusEsc = $conn->real_escape_string($statusToSet);
        $conn->query("INSERT INTO user_challenges (user_id, challenge_id, status) VALUES ($user_id, $challenge_id, '$statusEsc') ON DUPLICATE KEY UPDATE status='$statusEsc'");

        // If request was AJAX, return JSON so frontend can remove the card without a full redirect
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'challenge_id' => $challenge_id]);
            exit;
        }

        header('Location: /challenge');
        exit;
    }
}
