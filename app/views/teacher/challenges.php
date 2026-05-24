<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../app/config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header('Location: /login');
    exit;
}

$teacher_name = $_SESSION['user_name'] ?? 'Guru';
$result = $conn->query("SELECT * FROM challenges WHERE status IS NULL OR status = 'active' ORDER BY id DESC");
$challenges = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $challenges[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack - Tantangan Guru</title>
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/challengeguru.css">
</head>
<body>
<div class="teacher-dashboard">
    <aside class="sidebar">
        <div class="logo">StudyTrack</div>
        <nav class="menu">
            <a href="/teacher" class="menu-item">Dashboard</a>
            <a href="/teacher/challenges" class="menu-item active">Daftar tantangan</a>
            <a href="/logout" class="menu-item">Keluar</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h1 class="page-title">Daftar tantangan</h1>
                <p class="page-subtitle">Buat dan kelola tantangan siswa dari sini.</p>
            </div>
            <a href="/teacher/challenges/create" class="btn-primary">+ Buat Tantangan</a>
        </div>

        <div class="card-grid">
            <?php if (empty($challenges)): ?>
                <div class="challenge-card">
                    <p>Tidak ada tantangan saat ini. Klik Buat Tantangan untuk menambah.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($challenges as $challenge): ?>
                <div class="challenge-card">
                    <div class="challenge-top">
                        <div>
                            <h2 class="challenge-title"><?= htmlspecialchars($challenge['title']) ?></h2>
                            <div class="badge"><?= htmlspecialchars($challenge['challenge_type']) ?></div>
                        </div>
                        <span class="badge"><?= htmlspecialchars($challenge['status'] === 'active' ? 'In Progress' : 'Inactive') ?></span>
                    </div>
                    <p class="challenge-description"><?= htmlspecialchars($challenge['description']) ?></p>
                    <div class="tag-list">
                        <span class="tag-pill"><?= htmlspecialchars($challenge['category']) ?></span>
                        <span class="tag-pill"><?= htmlspecialchars($challenge['answer_type'] === 'multiple_choice' ? 'Pilihan Ganda' : 'Essay') ?></span>
                        <span class="tag-pill"><?= htmlspecialchars($challenge['points']) ?> Poin</span>

                    </div>
                    <div class="meta-list">
                        <div class="meta-item">Minggu <?= htmlspecialchars($challenge['week_number']) ?></div>
                        <div class="meta-item">Tenggat: <?= $challenge['due_date'] ? date('d M Y', strtotime($challenge['due_date'])) : 'Belum ditentukan' ?></div>
                    </div>
                    <div class="action-bar">
                        <a href="/teacher/challenges/<?= $challenge['id'] ?>/submissions" class="action-button">Lihat Submisi</a>
                        <a href="/teacher/challenges/<?= $challenge['id'] ?>/edit" class="action-button">Edit</a>
                        <button type="button" class="action-button delete js-delete" data-id="<?= $challenge['id'] ?>" data-title="<?= htmlspecialchars($challenge['title'], ENT_QUOTES) ?>">Hapus</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>

<!-- Delete confirmation modal -->
<div class="modal-backdrop" id="deleteModal">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="delTitle">
        <div class="icon">⚠️</div>
        <h3 id="delTitle">Hapus Tantangan</h3>
        <p id="delMessage">Yakin ingin menghapus tantangan ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-actions">
            <button class="btn-cancel" id="cancelDelete">Batal</button>
            <button class="btn-danger" id="confirmDelete">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const modal = document.getElementById('deleteModal');
    const delMsg = document.getElementById('delMessage');
    const confirmBtn = document.getElementById('confirmDelete');
    const cancelBtn = document.getElementById('cancelDelete');
    let deleteUrl = null;
    let deleteId = null;

    document.querySelectorAll('.js-delete').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title') || 'tantangan';
            delMsg.textContent = `Yakin ingin menghapus "${title}"? Tindakan ini tidak dapat dibatalkan.`;
            deleteUrl = `/teacher/challenges/${id}/delete`;
            deleteId = id;
            modal.style.display = 'flex';
        });
    });

    cancelBtn.addEventListener('click', () => { modal.style.display = 'none'; deleteUrl = null; });

    confirmBtn.addEventListener('click', () => {
        if (!deleteUrl) return;
        confirmBtn.disabled = true;
        fetch(deleteUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(r => {
                if (!r.ok) return r.text().then(t => { throw new Error(t || 'Gagal menghapus'); });
                return r.json();
            })
            .then(data => {
                if (data && data.status === 'ok') {
                    // remove the corresponding card from DOM
                    try {
                        const btn = document.querySelector('.js-delete[data-id="' + deleteId + '"]');
                        const card = btn ? btn.closest('.challenge-card') : null;
                        if (card) {
                            card.style.transition = 'opacity 240ms, transform 240ms';
                            card.style.opacity = 0;
                            card.style.transform = 'scale(0.98)';
                            setTimeout(() => card.remove(), 260);
                        }
                    } catch (e) {
                        // fallback to reload if DOM removal fails
                        window.location.reload();
                    }
                    modal.style.display = 'none';
                    deleteUrl = null; deleteId = null;
                } else {
                    alert(data && data.message ? data.message : 'Gagal menghapus');
                    modal.style.display='none';
                }
            })
            .catch(e => { alert('Gagal menghapus: ' + (e.message || e)); modal.style.display='none'; })
            .finally(()=> { confirmBtn.disabled = false; });
    });
});
</script>
</body>
</html>
