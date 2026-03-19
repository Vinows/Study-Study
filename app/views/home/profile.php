<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack - Profile</title>
    <link rel="stylesheet" href="/css/home.css">
    
    <style>
        /* --- LAYOUT UTAMA --- */
        .main-content {
            padding: 40px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 25px;
            /* Background biru otomatis mengikuti style.css bawaan Anda */
        }

        /* --- KOTAK PROFILE (PUTIH CERAH) --- */
        .profile-card {
            background: #ffffff; /* <-- Ini yang membuat kotaknya putih cerah */
            border-radius: 20px;
            padding: 35px;
            width: 100%;
            max-width: 900px;
            /* Shadow tipis agar kotak sedikit menonjol (opsional) */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); 
        }

        .section-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #000;
            margin-bottom: 25px;
        }

        /* --- BAGIAN HEADER PROFILE --- */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 25px;
        }

        .avatar-circle {
            width: 90px;
            height: 90px;
            background-color: #555; /* Warna lingkaran foto (bisa disesuaikan) */
            border-radius: 50%;
        }

        .user-info h2 {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 5px;
            color: #000;
        }

        .user-info p {
            font-size: 1rem;
            color: #555;
        }

        .profile-actions {
            display: flex;
            gap: 15px;
        }

        .btn-outline {
            background: #ffffff;
            border: 1px solid #aaa;
            padding: 8px 15px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            color: #000;
            transition: 0.2s;
        }

        .btn-outline:hover {
            background: #f0f0f0;
        }

        /* --- BAGIAN NOTIFIKASI EMAIL --- */
        .notif-section {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .notif-section:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .notif-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .notif-group-title {
            font-weight: 800;
            margin-bottom: 15px;
            display: block;
            font-size: 1.05rem;
            color: #000;
        }

        .notif-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-size: 1rem;
            color: #222;
        }

        /* --- TOGGLE SWITCH (SAKLAR) --- */
        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }

        .switch input { opacity: 0; width: 0; height: 0; }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #000; /* Hitam saat ON */
            transition: .3s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px; width: 18px;
            left: 3px; bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        /* Abu-abu saat OFF */
        input:not(:checked) + .slider {
            background-color: #ccc;
        }
        
        input:not(:checked) + .slider:before {
            transform: translateX(0);
        }

        input:checked + .slider:before {
            transform: translateX(22px);
        }

        /* --- BAGIAN DROPDOWN (PER KELAS) --- */
        .dropdown-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .custom-select {
            background: #f8f8f8;
            border: 1px solid #ccc;
            padding: 8px 15px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            outline: none;
            color: #000;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <h1 class="logo">StudyTrack</h1>
        <nav class="menu">
            <a href="/challenge" class="menu-item">Challenge</a>
            <a href="/progress" class="menu-item">Progress</a>
            <a href="/history" class="menu-item">History</a>
            <a href="/profile" class="menu-item active">Profile</a>
        </nav>
        <div class="sidebar-mascot">
            <img src="/assets/Image1.png" alt="Mascot">
        </div>
    </aside>

    <main class="main-content">
        
        <div class="profile-card">
            <h2 class="section-title">Profile</h2>
            
            <div class="profile-header">
                <div class="avatar-circle"></div>
                <div class="user-info">
                    <h2>Lawrence Epstein</h2>
                    <p>lawrence.epstein@gmail.com</p>
                </div>
            </div>

            <div class="profile-actions">
                <button class="btn-outline">Edit Profile</button>
                <button class="btn-outline">Kelola Akun</button>
            </div>
        </div>

        <div class="profile-card">
            <h2 class="section-title">Notifikasi Email</h2>
            
            <div class="notif-section notif-grid">
                <div>
                    <span class="notif-group-title">Aktivitas</span>
                    <div class="notif-item">Komentar <label class="switch"><input type="checkbox" checked><span class="slider"></span></label></div>
                    <div class="notif-item">Mention <label class="switch"><input type="checkbox" checked><span class="slider"></span></label></div>
                    <div class="notif-item">Nilai & feedback <label class="switch"><input type="checkbox" checked><span class="slider"></span></label></div>
                </div>
                <div>
                    <span class="notif-group-title">&nbsp;</span>
                    <div class="notif-item">Update kelas <label class="switch"><input type="checkbox" checked><span class="slider"></span></label></div>
                    <div class="notif-item">Tugas baru <label class="switch"><input type="checkbox" checked><span class="slider"></span></label></div>
                </div>
            </div>

            <div class="notif-section notif-grid">
                <div>
                    <span class="notif-group-title">Kelas & Tugas</span>
                    <div class="notif-item">Update kelas <label class="switch"><input type="checkbox" checked><span class="slider"></span></label></div>
                    <div class="notif-item">Tugas baru <label class="switch"><input type="checkbox" checked><span class="slider"></span></label></div>
                </div>
                <div>
                    <span class="notif-group-title">&nbsp;</span>
                    <div class="notif-item">Nilai & feedback <label class="switch"><input type="checkbox" checked><span class="slider"></span></label></div>
                    <div class="notif-item">Deadline reminder <label class="switch"><input type="checkbox" checked><span class="slider"></span></label></div>
                </div>
            </div>
        </div>

        <div class="profile-card dropdown-wrapper">
            <h2 class="section-title" style="margin-bottom: 0;">Notifikasi per Kelas</h2>
            <select class="custom-select">
                <option>Atur per Kelas</option>
                <option>Matematika</option>
                <option>Bahasa Inggris</option>
            </select>
        </div>

    </main>
</div>

</body>
</html>