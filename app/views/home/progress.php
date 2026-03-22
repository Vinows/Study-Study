<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack - Progress</title>
    <link rel="stylesheet" href="/css/home.css">
    <style>
        /* Styling khusus untuk Progress Bar Pajangan */
        .progress-container {
            background: white;
            border: 2px solid #000;
            border-radius: 25px;
            padding: 40px;
            width: 100%;
            max-width: 1200px;
            box-shadow: 8px 8px 0px rgba(0,0,0,0.1);
        }

        .progress-title {
            font-weight: 800;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: block;
        }

        /* Outline Progress Bar */
        .progress-bar-outline {
            width: 100%;
            height: 50px;
            background: #eee;
            border: 2px solid #000;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 25px;
        }

        /* Isi Progress Bar (Warna Hijau) */
        .progress-bar-fill {
            width: 85%; /* Atur lebar ini untuk "pajangan" */
            height: 100%;
            background: #76ff03; /* Hijau neon sesuai gambar */
            border-right: 2px solid #000;
        }

        .progress-text {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 30px;
        }

        .progress-link {
            color: #00a2ff;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px solid #00a2ff;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <h1 class="logo">StudyTrack</h1>
        <nav class="menu">
            <a href="/challenge" class="menu-item">Challenge</a>
            <a href="/progress" class="menu-item active">Progress</a>
            <a href="/history" class="menu-item">History</a>
            <a href="/profile" class="menu-item">Profile</a>
        </nav>
        <div class="sidebar-mascot">
            <img src="/assets/Image1.png" alt="Mascot">
        </div>
    </aside>

    <main class="main-content">
        <div class="progress-container">
            <span class="progress-title">Progress</span>
            
            <div class="progress-bar-outline">
                <div class="progress-bar-fill"></div>
            </div>

            <p class="progress-text">Ini adalah progres bar untuk minggu...</p>
            
            <a href="#" class="progress-link">Lihat progres mu di minggu ini</a>
        </div>
    </main>
</div>

</body>
</html>