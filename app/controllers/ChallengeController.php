<?php
namespace App\Controllers;

class ChallengeController {
    public function Challenge() {
        // Memanggil view challenge yang ada di app/views/challenge/index.php
        // Jika file anda bernama home.php, ubah nama file di bawah ini
        require_once '../app/views/home/challenge.php';
        
    }
    public function Take() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        require '../app/config/db.php';
        $user_id = $_SESSION['user_id'];
        $query = "SELECT c.*, uc.status FROM challenges c LEFT JOIN user_challenges uc ON c.id = uc.challenge_id AND uc.user_id = $user_id WHERE c.week_number = $current_week AND (uc.status IS NULL OR uc.status != 'completed') ORDER BY c.id ASC";
        $res = $conn->query($query);
        $challenges = [];
        if ($res) while ($r = $res->fetch_assoc()) $challenges[] = $r;
        // load questions for each challenge
        foreach ($challenges as &$ch) {
            $cid = intval($ch['id']);
            $qres = $conn->query("SELECT * FROM questions WHERE challenge_id = $cid ORDER BY position ASC, id ASC");
            $ch['questions'] = [];
            if ($qres) while ($qr = $qres->fetch_assoc()) {
                if (!empty($qr['options'])) {
                    $decoded = json_decode($qr['options'], true);
                    $qr['options'] = is_array($decoded) ? $decoded : [];
                } else {
                    $qr['options'] = [];
                }
                $ch['questions'][] = $qr;
            }
        }
        require_once '../app/views/home/take.php';
    }
    
}