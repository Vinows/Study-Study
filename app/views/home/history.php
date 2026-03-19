<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack - History</title>
    <link rel="stylesheet" href="/css/home.css">
    
    <style>
        /* --- KHUSUS HALAMAN HISTORY --- */
        .main-content {
            padding: 40px;
            overflow-y: auto;
        }

        .history-list {
            display: flex;
            flex-direction: column;
            gap: 30px;
            width: 100%;
            max-width: 1150px;
        }

        /* --- CARD STYLE --- */
        .history-card {
            background: #ffffff;
            border: 3px solid #000;
            border-radius: 20px;
            padding: 25px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 6px 6px 0px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .history-card:hover {
            transform: scale(1.01);
        }

        .history-info {
            flex: 1;
        }

        .history-info label {
            display: block;
            font-weight: 800;
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: #000;
        }

        /* --- PROGRESS BAR MINI --- */
        .progress-mini-outline {
            width: 90%;
            height: 40px;
            background: #eee;
            border: 3px solid #000;
            border-radius: 15px;
            overflow: hidden;
        }

        .progress-mini-fill {
            height: 100%;
            background: #76ff03; /* Hijau Terang */
            border-right: 3px solid #000;
        }

        /* --- ICON PANAH --- */
        .arrow-icon {
            font-size: 2.5rem;
            font-weight: 300;
            color: #333;
            cursor: pointer;
            padding-left: 20px;
            line-height: 1;
        }

        /* Animasi sederhana untuk panah */
        .history-card:hover .arrow-icon {
            transform: translateX(5px);
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
            <a href="/history" class="menu-item active">History</a>
            <a href="/profile" class="menu-item">Profile</a>
        </nav>
        <div class="sidebar-mascot">
            <img src="/assets/Image1.png" alt="Mascot">
        </div>
    </aside>

    <main class="main-content">
        <div class="history-list">
            
            <div class="history-card">
                <div class="history-info">
                    <label>Progress Minggu 1</label>
                    <div class="progress-mini-outline">
                        <div class="progress-mini-fill" style="width: 100%;"></div>
                    </div>
                </div>
                <div class="arrow-icon">❯</div>
            </div>

            <div class="history-card">
                <div class="history-info">
                    <label>Progress Minggu 2</label>
                    <div class="progress-mini-outline">
                        <div class="progress-mini-fill" style="width: 85%;"></div>
                    </div>
                </div>
                <div class="arrow-icon">❯</div>
            </div>

            <div class="history-card">
                <div class="history-info">
                    <label>Progress Minggu 3</label>
                    <div class="progress-mini-outline">
                        <div class="progress-mini-fill" style="width: 65%;"></div>
                    </div>
                </div>
                <div class="arrow-icon">❯</div>
            </div>

            <div class="history-card">
                <div class="history-info">
                    <label>Progress Minggu 4</label>
                    <div class="progress-mini-outline">
                        <div class="progress-mini-fill" style="width: 45%;"></div>
                    </div>
                </div>
                <div class="arrow-icon">❯</div>
            </div>

            <div class="history-card">
                <div class="history-info">
                    <label>Progress Minggu 5</label>
                    <div class="progress-mini-outline">
                        <div class="progress-mini-fill" style="width: 25%;"></div>
                    </div>
                </div>
                <div class="arrow-icon">❯</div>