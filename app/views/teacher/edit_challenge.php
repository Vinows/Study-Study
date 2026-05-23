<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    header('Location: /login');
    exit;
}
if (!isset($challenge) || !is_array($challenge)) {
    header('Location: /teacher/challenges');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack - Edit Tantangan</title>
    <link rel="stylesheet" href="/css/home.css">
    <style>
        body { background: #eff6ff; margin: 0; font-family: Inter, sans-serif; color: #0f172a; }
        .page-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: #0f172a; color: #f8fafc; padding: 32px; }
        .sidebar .logo { font-size: 2rem; font-weight: 900; margin-bottom: 2rem; }
        .menu { display: flex; flex-direction: column; gap: 18px; }
        .menu-item { color: #cbd5e1; text-decoration: none; font-weight: 700; padding: 14px 16px; border-radius: 16px; }
        .menu-item.active, .menu-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .main { flex: 1; padding: 36px; }
        .back-link { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 18px; color: #2563eb; text-decoration: none; font-weight: 700; }
        .page-title { font-size: 2.4rem; margin: 0 0 8px; }
        .page-subtitle { color: #475569; margin: 0 0 24px; }
        .form-card { background: #fff; border-radius: 32px; padding: 34px; box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08); }
        .field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .field-group { display: flex; flex-direction: column; gap: 10px; margin-bottom: 18px; }
        .field-group.full { grid-column: span 2; }
        .field-group label { font-weight: 700; }
        .field-group input,
        .field-group textarea,
        .field-group select { width: 100%; padding: 14px 16px; border: 1px solid #cbd5e1; border-radius: 16px; font-size: 1rem; }
        .choice-cards { display: flex; gap: 12px; }
        .choice-card { display: flex; align-items: center; gap: 12px; padding: 16px; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; cursor: pointer; flex: 1; }
        .choice-card input { display: none; }
        .choice-card .choice-title { font-weight: 800; }
        .choice-card .choice-desc { color: #6b7280; font-size: 0.95rem; }
        .choice-card input:checked + .choice-content { border-color: #2563eb; }
        .choice-card.selected { border-color: #2563eb; background: #eff6ff; }
        .form-actions { display: flex; justify-content: flex-end; gap: 16px; margin-top: 18px; }
        .btn-secondary { border: 1px solid #cbd5e1; background: #fff; color: #334155; padding: 14px 24px; border-radius: 16px; text-decoration: none; font-weight: 700; }
        .btn-primary { border: none; background: #2563eb; color: #fff; padding: 14px 24px; border-radius: 16px; font-weight: 700; cursor: pointer; }
    </style>
</head>
<body>
<div class="page-layout">
    <aside class="sidebar">
        <div class="logo">StudyTrack</div>
        <nav class="menu">
            <a href="/teacher" class="menu-item">Dashboard</a>
            <a href="/teacher/challenges" class="menu-item active">Daftar tantangan</a>
            <a href="/logout" class="menu-item">Keluar</a>
        </nav>
    </aside>
    <main class="main">
        <a href="/teacher/challenges" class="back-link">← Kembali ke daftar tantangan</a>
        <h1 class="page-title">Edit Tantangan</h1>
        <p class="page-subtitle">Perbarui detail tantangan agar siswa mendapatkan instruksi yang tepat.</p>

        <div class="form-card">
            <form action="/teacher/challenges/<?= $challenge['id'] ?>/edit" method="POST" enctype="multipart/form-data">
                <div class="field-grid">
                    <div class="field-group full">
                        <label for="title">Judul Tantangan</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($challenge['title']) ?>" required>
                    </div>
                    <div class="field-group full">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" rows="5"><?= htmlspecialchars($challenge['description']) ?></textarea>
                    </div>
                    <div class="field-group">
                        <label for="category">Mata Pelajaran</label>
                        <select id="category" name="category" required>
                            <option value="Pelajaran" <?= $challenge['category'] === 'Pelajaran' ? 'selected' : '' ?>>Pelajaran</option>
                            <option value="Bahasa Inggris" <?= $challenge['category'] === 'Bahasa Inggris' ? 'selected' : '' ?>>Bahasa Inggris</option>
                            <option value="Matematika" <?= $challenge['category'] === 'Matematika' ? 'selected' : '' ?>>Matematika</option>
                            <option value="Sains" <?= $challenge['category'] === 'Sains' ? 'selected' : '' ?>>Sains</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="challenge_type">Tipe Tantangan</label>
                        <div class="choice-cards">
                            <label class="choice-card">
                                <input type="radio" name="challenge_type" value="Tugas" <?= $challenge['challenge_type'] === 'Tugas' ? 'checked' : '' ?>>
                                <div class="choice-content">
                                    <div class="choice-title">Tugas</div>
                                    <div class="choice-desc">Siswa mengerjakan tugas dan mengumpulkan hasil</div>
                                </div>
                            </label>
                            <label class="choice-card">
                                <input type="radio" name="challenge_type" value="Kuis" <?= $challenge['challenge_type'] === 'Kuis' ? 'checked' : '' ?>>
                                <div class="choice-content">
                                    <div class="choice-title">Kuis</div>
                                    <div class="choice-desc">Siswa menjawab pertanyaan dalam bentuk kuis</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="field-group full">
                        <label for="attachment">Lampiran (opsional)</label>
                        <div style="display:flex;align-items:center;gap:12px;flex-direction:column;align-items:flex-start;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <input type="file" id="attachment" name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png">
                                <small style="color:#64748b;">Maks. 10MB (PDF, DOC, DOCX, PPT, PPTX, JPG, PNG)</small>
                            </div>
                            <?php if (!empty($challenge['attachment'])): ?>
                                <div><a href="<?= htmlspecialchars($challenge['attachment']) ?>" target="_blank">Lihat lampiran saat ini</a></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="field-group">
                        <label for="week_number">Tingkat/Kelas</label>
                        <input type="number" id="week_number" name="week_number" min="1" value="<?= htmlspecialchars($challenge['week_number']) ?>" required>
                    </div>
                    <div class="field-group">
                        <label for="points">Poin</label>
                        <input type="number" id="points" name="points" min="0" value="<?= htmlspecialchars($challenge['points']) ?>" required>
                    </div>
                    <div class="field-group full">
                        <label for="due_date">Waktu Tenggat</label>
                        <input type="datetime-local" id="due_date" name="due_date" value="<?= $challenge['due_date'] ? date('Y-m-d\TH:i', strtotime($challenge['due_date'])) : '' ?>">
                    </div>
                    
                    <div style="grid-column: span 2">
                        <hr style="margin:20px 0; border:none; border-top:1px solid #eef2ff">
                        <h3 style="margin-top:0">Pertanyaan</h3>
                        <div id="questions-container"></div>
                        <div style="margin-top:12px">
                            <button type="button" id="add-question" class="btn-secondary">+ Tambah Pertanyaan</button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="/teacher/challenges" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </main>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Questions dynamic list for edit
    const qContainer = document.getElementById('questions-container');
    let qIndex = 0;
    function renderQuestion(index, data = {}) {
        const wrapper = document.createElement('div');
        wrapper.className = 'field-group full';
        wrapper.style.border = '1px solid #e6eefc';
        wrapper.style.padding = '12px';
        wrapper.style.borderRadius = '12px';
        wrapper.style.marginBottom = '12px';
        wrapper.dataset.index = index;
        const qt = data.question_text ? data.question_text : '';
        const at = data.answer_type ? data.answer_type : 'essay';
        const opts = Array.isArray(data.options) ? data.options : [];
        wrapper.innerHTML = `
            <label style="font-weight:700">Pertanyaan</label>
            <textarea name="questions[]" rows="3" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e5e7eb">${qt}</textarea>
            <div style="display:flex;gap:12px;margin-top:8px;align-items:center">
                <label style="font-weight:700">Tipe Jawaban</label>
                <label style="display:flex;align-items:center;gap:8px"><input type="radio" name="q_answer_type[${index}]" value="essay" ${at === 'essay' ? 'checked' : ''}> Essay</label>
                <label style="display:flex;align-items:center;gap:8px"><input type="radio" name="q_answer_type[${index}]" value="multiple_choice" ${at === 'multiple_choice' ? 'checked' : ''}> Pilihan Ganda</label>
                <button type="button" class="btn-secondary btn-remove-q" style="margin-left:auto">Hapus</button>
            </div>
            <div class="q-mcq-options" style="margin-top:8px; display:${at === 'multiple_choice' ? 'block' : 'none'}">
                <label>Opsi</label>
                <div class="opts-list" style="display:grid;gap:8px">
                </div>
                <div style="margin-top:8px"><button type="button" class="btn-secondary btn-add-opt">+ Tambah Opsi</button></div>
            </div>
        `;
        qContainer.appendChild(wrapper);

        const optsList = wrapper.querySelector('.opts-list');
        function addOpt(val = '') {
            const opt = document.createElement('div');
            opt.style.display = 'flex'; opt.style.gap = '8px'; opt.style.alignItems = 'center';
            opt.innerHTML = `<input type="text" name="q_options[${index}][]" value="${val}" placeholder="Opsi"> <button type="button" class="btn-secondary btn-remove-opt">-</button>`;
            optsList.appendChild(opt);
            opt.querySelector('.btn-remove-opt').addEventListener('click', () => opt.remove());
        }
        if (opts.length) for (const o of opts) addOpt(o);
        wrapper.querySelector('.btn-add-opt').addEventListener('click', () => addOpt(''));
        wrapper.querySelectorAll(`input[name=\"q_answer_type[${index}]\"]`).forEach(r => r.addEventListener('change', function(){
            wrapper.querySelector('.q-mcq-options').style.display = this.value === 'multiple_choice' ? 'block' : 'none';
        }));
        wrapper.querySelector('.btn-remove-q').addEventListener('click', () => wrapper.remove());
    }
    document.getElementById('add-question').addEventListener('click', function(){ renderQuestion(qIndex++); });
    // populate existing questions
    const existing = <?= isset($questions) ? json_encode($questions, JSON_UNESCAPED_UNICODE) : '[]' ?>;
    if (Array.isArray(existing) && existing.length) {
        for (const q of existing) {
            // normalize options
            if (q.options) {
                try { q.options = JSON.parse(q.options); } catch(e) { q.options = []; }
            } else q.options = [];
            renderQuestion(qIndex++, q);
        }
    } else {
        renderQuestion(qIndex++);
    }
});
</script>
</body>
</html>
