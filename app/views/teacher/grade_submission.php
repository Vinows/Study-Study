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
    <style>
        body{font-family:Inter, sans-serif;background:#f8fafc;margin:0}
        .wrap{max-width:900px;margin:40px auto;padding:20px}
        .card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 10px 30px rgba(2,6,23,0.06)}
        label{font-weight:700}
        textarea{width:100%;min-height:120px;padding:12px;border-radius:8px;border:1px solid #e5e7eb}
        input[type=number]{padding:10px;border-radius:8px;border:1px solid #e5e7eb}
        .actions{display:flex;gap:10px;justify-content:flex-end;margin-top:12px}
        .btn-primary{background:#2563eb;color:#fff;padding:10px 16px;border-radius:8px;border:none}
        .btn-secondary{background:#fff;border:1px solid #cbd5e1;padding:10px 14px;border-radius:8px}
    </style>
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
