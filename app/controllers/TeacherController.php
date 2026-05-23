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
            $answer_type = $conn->real_escape_string(trim($_POST['answer_type'] ?? 'essay'));
            $options = [];
            if ($answer_type === 'multiple_choice' && isset($_POST['options']) && is_array($_POST['options'])) {
                foreach ($_POST['options'] as $opt) {
                    $opt = trim($opt);
                    if ($opt !== '') {
                        $options[] = $opt;
                    }
                }
            }
            $options_sql = !empty($options) ? "'" . $conn->real_escape_string(json_encode($options, JSON_UNESCAPED_UNICODE)) . "'" : 'NULL';

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
            $conn->query("INSERT INTO challenges (title, description, category, week_number, due_date, points, challenge_type, answer_type, options, attachment) VALUES ('$title', '$description', '$category', $week_number, $due_date_sql, $points, '$challenge_type', '$answer_type', $options_sql, $attachment_sql)");
            $challenge_id = $conn->insert_id;

            // Save questions if provided (questions[], q_answer_type[], q_options[index][][])
            if (isset($_POST['questions']) && is_array($_POST['questions'])) {
                foreach ($_POST['questions'] as $i => $qText) {
                    $qText = trim($qText);
                    if ($qText === '') continue;
                    $qType = isset($_POST['q_answer_type'][$i]) ? $conn->real_escape_string($_POST['q_answer_type'][$i]) : $answer_type;
                    $qOptionsSql = 'NULL';
                    if ($qType === 'multiple_choice' && isset($_POST['q_options'][$i]) && is_array($_POST['q_options'][$i])) {
                        $opts = [];
                        foreach ($_POST['q_options'][$i] as $opt) {
                            $opt = trim($opt);
                            if ($opt !== '') $opts[] = $opt;
                        }
                        if (!empty($opts)) $qOptionsSql = "'" . $conn->real_escape_string(json_encode($opts, JSON_UNESCAPED_UNICODE)) . "'";
                    }
                    $pos = intval($i);
                    $conn->query("INSERT INTO questions (challenge_id, question_text, answer_type, options, position) VALUES ($challenge_id, '" . $conn->real_escape_string($qText) . "', '$qType', $qOptionsSql, $pos)");
                }
            }
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

        // fetch existing questions for edit view
        $questions = [];
        $qres = $conn->query("SELECT * FROM questions WHERE challenge_id = $challenge_id ORDER BY position ASC, id ASC");
        if ($qres) while ($qr = $qres->fetch_assoc()) $questions[] = $qr;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $conn->real_escape_string(trim($_POST['title'] ?? ''));
            $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
            $category = $conn->real_escape_string(trim($_POST['category'] ?? 'Umum'));
            $week_number = intval($_POST['week_number'] ?? 1);
            $due_date = $conn->real_escape_string(trim($_POST['due_date'] ?? ''));
            $points = intval($_POST['points'] ?? 0);
            $challenge_type = $conn->real_escape_string(trim($_POST['challenge_type'] ?? 'Tugas'));
            $answer_type = $conn->real_escape_string(trim($_POST['answer_type'] ?? 'essay'));
            $options = [];
            if ($answer_type === 'multiple_choice' && isset($_POST['options']) && is_array($_POST['options'])) {
                foreach ($_POST['options'] as $opt) {
                    $opt = trim($opt);
                    if ($opt !== '') {
                        $options[] = $opt;
                    }
                }
            }
            $options_sql = !empty($options) ? "'" . $conn->real_escape_string(json_encode($options, JSON_UNESCAPED_UNICODE)) . "'" : 'NULL';
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

            $conn->query("UPDATE challenges SET title='$title', description='$description', category='$category', week_number=$week_number, due_date=$due_date_sql, points=$points, challenge_type='$challenge_type', answer_type='$answer_type', options=$options_sql $attachment_sql WHERE id = $challenge_id");

            // Replace existing questions: remove and re-insert
            $conn->query("DELETE FROM questions WHERE challenge_id = $challenge_id");
            if (isset($_POST['questions']) && is_array($_POST['questions'])) {
                foreach ($_POST['questions'] as $i => $qText) {
                    $qText = trim($qText);
                    if ($qText === '') continue;
                    $qType = isset($_POST['q_answer_type'][$i]) ? $conn->real_escape_string($_POST['q_answer_type'][$i]) : $answer_type;
                    $qOptionsSql = 'NULL';
                    if ($qType === 'multiple_choice' && isset($_POST['q_options'][$i]) && is_array($_POST['q_options'][$i])) {
                        $opts = [];
                        foreach ($_POST['q_options'][$i] as $opt) {
                            $opt = trim($opt);
                            if ($opt !== '') $opts[] = $opt;
                        }
                        if (!empty($opts)) $qOptionsSql = "'" . $conn->real_escape_string(json_encode($opts, JSON_UNESCAPED_UNICODE)) . "'";
                    }
                    $pos = intval($i);
                    $conn->query("INSERT INTO questions (challenge_id, question_text, answer_type, options, position) VALUES ($challenge_id, '" . $conn->real_escape_string($qText) . "', '$qType', $qOptionsSql, $pos)");
                }
            }

            header('Location: /teacher/challenges');
            exit;
        }

        require_once '../app/views/teacher/edit_challenge.php';
    }

    public function DeleteChallenge($id) {
        $this->guardTeacher();
        require '../app/config/db.php';
        $challenge_id = intval($id);
        // Remove attachments for submissions belonging to this challenge
        $publicPath = __DIR__ . '/../../public';
        $subRes = $conn->query("SELECT attachment FROM submissions WHERE challenge_id = $challenge_id");
        if ($subRes) {
            while ($s = $subRes->fetch_assoc()) {
                if (!empty($s['attachment'])) {
                    $filePath = $publicPath . $s['attachment'];
                    if (file_exists($filePath)) @unlink($filePath);
                }
            }
        }

        // Remove challenge attachment
        $res = $conn->query("SELECT attachment FROM challenges WHERE id = $challenge_id");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $attachment = $row['attachment'] ?? null;
            if (!empty($attachment)) {
                $filePath = $publicPath . $attachment;
                if (file_exists($filePath)) @unlink($filePath);
            }
        }

        // Delete dependent rows first to avoid FK issues
        $err = null;
        if (! $conn->query("DELETE FROM submissions WHERE challenge_id = $challenge_id")) {
            $err = $conn->error;
        }
        if (! $conn->query("DELETE FROM user_challenges WHERE challenge_id = $challenge_id")) {
            $err = $err ?: $conn->error;
        }

        if (is_null($err)) {
            if (! $conn->query("DELETE FROM challenges WHERE id = $challenge_id")) {
                $err = $conn->error;
            }
        }

        // Log the delete attempt for debugging
        $logEntry = date('c') . " | delete_challenge id=$challenge_id | err=" . ($err ?: 'none') . "\n";
        @file_put_contents(__DIR__ . '/../../storage/delete_challenge.log', $logEntry, FILE_APPEND | LOCK_EX);

        // If deletion failed, try stronger approaches then fall back to soft-hide
        if ($err) {
            $logPath = __DIR__ . '/../../storage/delete_challenge.log';
            file_put_contents($logPath, date('c') . " | initial_delete_failed id=$challenge_id | err=" . ($err ?: 'unknown') . "\n", FILE_APPEND | LOCK_EX);

            // Try disabling foreign key checks and re-attempt delete
            $conn->query('SET FOREIGN_KEY_CHECKS=0');
            $retryErr = null;
            if (! $conn->query("DELETE FROM submissions WHERE challenge_id = $challenge_id")) {
                $retryErr = $conn->error;
            }
            if (! $conn->query("DELETE FROM user_challenges WHERE challenge_id = $challenge_id")) {
                $retryErr = $retryErr ?: $conn->error;
            }
            if (! $conn->query("DELETE FROM challenges WHERE id = $challenge_id")) {
                $retryErr = $retryErr ?: $conn->error;
            }
            $conn->query('SET FOREIGN_KEY_CHECKS=1');

            if ($retryErr) {
                file_put_contents($logPath, date('c') . " | retry_delete_failed id=$challenge_id | err=" . ($retryErr ?: 'unknown') . "\n", FILE_APPEND | LOCK_EX);
                $safe = $conn->real_escape_string('inactive');
                $conn->query("UPDATE challenges SET status='$safe' WHERE id = $challenge_id");
            } else {
                file_put_contents($logPath, date('c') . " | retry_delete_ok id=$challenge_id\n", FILE_APPEND | LOCK_EX);
            }
        }

        // If request is AJAX, return JSON; otherwise redirect
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            if ($err) {
                echo json_encode(['status' => 'ok', 'note' => 'soft-hidden', 'challenge_id' => $challenge_id]);
            } else {
                echo json_encode(['status' => 'ok']);
            }
            return;
        }

        header('Location: /teacher/challenges');
        exit;
    }
}
