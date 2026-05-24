<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../app/config/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>StudyTrack - Kerjakan Semua Tantangan</title>
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/take.css">
</head>
<body>
<div class="container">
    <h1>Kerjakan Tantangan Berikut Ini!</h1>
    <form action="/challenge/submit-multiple" method="POST" enctype="multipart/form-data">
        <?php if (empty($challenges)): ?>
            <div class="card">Tidak ada tantangan untuk minggu ini.</div>
        <?php else: ?>
            <?php foreach($challenges as $idx => $c): ?>
                <div class="card">
                    <div class="q-title"><?= htmlspecialchars($c['title']) ?></div>
                    <div class="q-desc" style="color:#475569;margin-bottom:12px"><?= htmlspecialchars($c['description']) ?></div>
                    <?php if (!empty($c['questions'])): ?>
                        <?php foreach ($c['questions'] as $q): ?>
                            <div style="margin-bottom:12px; padding:10px; border-radius:8px; background:#f8fafc">
                                <div style="font-weight:700"><?= htmlspecialchars($q['question_text']) ?></div>
                                <?php if ($q['answer_type'] === 'multiple_choice'): ?>
                                    <?php $opts = $q['options'] ?? []; ?>
                                    <?php if (!empty($opts)): ?>
                                        <?php foreach($opts as $opt): ?>
                                            <label class="mcq-option">
                                                <input type="radio" name="answer_q_<?= $q['id'] ?>" value="<?= htmlspecialchars($opt, ENT_QUOTES) ?>">
                                                <span><?= htmlspecialchars($opt) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div style="color:#b91c1c">Tidak ada opsi untuk pertanyaan ini.</div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div>
                                        <textarea name="answer_q_<?= $q['id'] ?>" rows="3" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e5e7eb"></textarea>
                                    </div>
                                    <div style="margin-top:8px"><input type="file" name="attachment_q_<?= $q['id'] ?>"></div>
                                <?php endif; ?>
                                <input type="hidden" name="question_ids[]" value="<?= intval($q['id']) ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <input type="hidden" name="challenge_ids[]" value="<?= $c['id'] ?>">
                        <?php if ($c['answer_type'] === 'multiple_choice'): ?>
                            <?php $opts = json_decode($c['options'] ?? '[]', true); ?>
                            <?php if (!empty($opts)): ?>
                                <?php foreach($opts as $opt): ?>
                                    <label class="mcq-option">
                                        <input type="radio" name="answer_<?= $c['id'] ?>" value="<?= htmlspecialchars($opt, ENT_QUOTES) ?>">
                                        <span><?= htmlspecialchars($opt) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="color:#b91c1c">Tidak ada opsi untuk pertanyaan ini.</div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div>
                                <textarea name="answer_<?= $c['id'] ?>" rows="4" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e5e7eb"></textarea>
                            </div>
                            <div style="margin-top:8px"><input type="file" name="attachment_<?= $c['id'] ?>"></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="submit-row">
            <a href="/challenge" style="margin-right:12px;color:#64748b;">Batal</a>
            <button type="submit" class="btn-primary">Kirim Semua Jawaban</button>
        </div>
    </form>
</div>
</body>
</html>
