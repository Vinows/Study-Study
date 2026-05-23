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

    public function Submissions($challengeId) {
        $this->guardTeacher();
        require '../app/config/db.php';
        $cid = intval($challengeId);
        $result = $conn->query("SELECT s.*, u.name AS student_name, u.email FROM submissions s JOIN users u ON s.user_id = u.id WHERE s.challenge_id = $cid ORDER BY s.created_at DESC");
        $submissions = [];
        if ($result) while ($row = $result->fetch_assoc()) $submissions[] = $row;
        require_once '../app/views/teacher/submissions.php';
    }

    public function GradeSubmission($submissionId) {
        $this->guardTeacher();
        require '../app/config/db.php';
        $sid = intval($submissionId);
        $res = $conn->query("SELECT s.*, u.name AS student_name, u.email FROM submissions s JOIN users u ON s.user_id = u.id WHERE s.id = $sid");
        if (!$res || $res->num_rows === 0) {
            header('Location: /teacher/challenges');
            exit;
        }
        $submission = $res->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $grade = intval($_POST['grade'] ?? 0);
            $feedback = $conn->real_escape_string(trim($_POST['feedback'] ?? ''));
            $now = date('Y-m-d H:i:s');
            $conn->query("UPDATE submissions SET grade=$grade, feedback='$feedback', status='graded', graded_at='$now' WHERE id = $sid");

            // mark user_challenges as completed
            $uid = intval($submission['user_id']);
            $cid = intval($submission['challenge_id']);
            $conn->query("INSERT INTO user_challenges (user_id, challenge_id, status) VALUES ($uid, $cid, 'completed') ON DUPLICATE KEY UPDATE status='completed'");

            header('Location: /teacher/challenges/' . $submission['challenge_id'] . '/submissions');
            exit;
        }

        require_once '../app/views/teacher/grade_submission.php';
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

            // handle optional attachment
            $attachment_sql = 'NULL';
            if (!empty($_FILES['attachment']['name'])) {
                $tmp = $_FILES['attachment']['tmp_name'];
                $orig = basename($_FILES['attachment']['name']);
                $ext = pathinfo($orig, PATHINFO_EXTENSION);
                $allowed = ['pdf','doc','docx','ppt','pptx','jpg','jpeg','png'];
                if (in_array(strtolower($ext), $allowed) && $_FILES['attachment']['size'] <= 10 * 1024 * 1024) {
                    $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    $uploadDir = __DIR__ . '/../../public/uploads/challenges';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    if (move_uploaded_file($tmp, $uploadDir . '/' . $newName)) {
                        $attachment_sql = "'" . $conn->real_escape_string('/uploads/challenges/' . $newName) . "'";
                    }
                }
            }

            $due_date_sql = $due_date !== '' ? "'$due_date'" : 'NULL';
            $conn->query("INSERT INTO challenges (title, description, category, week_number, due_date, points, challenge_type, attachment) VALUES ('$title', '$description', '$category', $week_number, $due_date_sql, $points, '$challenge_type', $attachment_sql)");
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

            $attachment_sql = '';
            if (!empty($_FILES['attachment']['name'])) {
                $tmp = $_FILES['attachment']['tmp_name'];
                $orig = basename($_FILES['attachment']['name']);
                $ext = pathinfo($orig, PATHINFO_EXTENSION);
                $allowed = ['pdf','doc','docx','ppt','pptx','jpg','jpeg','png'];
                if (in_array(strtolower($ext), $allowed) && $_FILES['attachment']['size'] <= 10 * 1024 * 1024) {
                    $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    $uploadDir = __DIR__ . '/../../public/uploads/challenges';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    if (move_uploaded_file($tmp, $uploadDir . '/' . $newName)) {
                        $attachment_sql = ", attachment='" . $conn->real_escape_string('/uploads/challenges/' . $newName) . "'";
                    }
                }
            }

            $conn->query("UPDATE challenges SET title='$title', description='$description', category='$category', week_number=$week_number, due_date=$due_date_sql, points=$points, challenge_type='$challenge_type' $attachment_sql WHERE id = $challenge_id");
            header('Location: /teacher/challenges');
            exit;
        }

        require_once '../app/views/teacher/edit_challenge.php';
    }

    public function DeleteChallenge($id) {
        $this->guardTeacher();
        require '../app/config/db.php';
        $challenge_id = intval($id);

        // Fetch challenge to get attachment path (if any)
        $res = $conn->query("SELECT attachment FROM challenges WHERE id = $challenge_id");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $attachment = $row['attachment'] ?? null;
            if (!empty($attachment)) {
                // Convert web path to filesystem path
                $publicPath = __DIR__ . '/../../public';
                $filePath = $publicPath . $attachment;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        }

        $conn->query("DELETE FROM challenges WHERE id = $challenge_id");
        // If request is AJAX, return JSON; otherwise redirect
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'ok']);
            return;
        }

        header('Location: /teacher/challenges');
        exit;
    }
}
