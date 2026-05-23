<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header('Location: /login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nilai Submisi - StudyTrack</title>
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/grade.css">
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h2>Nilai Submisi</h2>
            <div style="margin-top:12px">
                <strong>Siswa:</strong> <?= htmlspecialchars($submission['student_name']) ?> <br>
                <strong>Dikirim pada:</strong> <?= date('d M Y H:i', strtotime($submission['created_at'])) ?>
            </div>
            <div style="margin-top:12px">
                <h3>Jawaban</h3>
                <div style="padding:12px;background:#f1f5f9;border-radius:8px"><?= nl2br(htmlspecialchars($submission['answer_text'])) ?></div>
                <?php if (!empty($submission['attachment'])): ?>
                    <div style="margin-top:8px"><a href="<?= htmlspecialchars($submission['attachment']) ?>" target="_blank">Lihat lampiran</a></div>
                <?php endif; ?>
            </div>

            <form method="POST">
                <div style="margin-top:12px">
                    <label for="grade">Nilai (0-100)</label>
                    <input type="number" id="grade" name="grade" min="0" max="100" value="<?= htmlspecialchars($submission['grade'] ?? '') ?>">
                </div>
                <div style="margin-top:12px">
                    <label for="feedback">Feedback</label>
                    <textarea id="feedback" name="feedback"><?= htmlspecialchars($submission['feedback'] ?? '') ?></textarea>
                </div>
                <div class="actions">
                    <a class="btn-secondary" href="/teacher/challenges/<?= $submission['challenge_id'] ?>/submissions">Kembali</a>
                    <button class="btn-primary" type="submit">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
