<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyTrack - Profile</title>
    <link rel="stylesheet" href="/css/home.css">
    
    <style>
        /* --- KHUSUS HALAMAN PROFILE --- */
        .profile-container {
            background: #ffffff;
            border: 3px solid #000;
            border-radius: 25px;
            padding: 40px;
            width: 100%;
            max-width: 800px;
            box-shadow: 10px 10px 0px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .profile-title {
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 30px;
            display: block;
            color: #000;
        }

        .profile-content {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        /* Lingkaran Avatar */
        .profile-avatar {
            width: 100px;
            height: 100px;
            background-color: #f5e6d3; /* Warna krem sesuai gambar */
            border: 3px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .profile-avatar img{
            width: 170px;
        }

        /* Informasi Teks */
        .profile-info h2 {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            color: #000;
        }

        .profile-info p {
            font-size: 1.1rem;
            color: #555;
            margin-top: 5px;
        }

        /* Tombol Log Out */
        .logout-container {
            text-align: right;
            margin-top: 20px;
        }

        .logout-link {
            color: #00a2ff;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            border-bottom: 2px solid #00a2ff;
            transition: all 0.2s ease;
        }

        .logout-link:hover {
            color: #ff4b2b;
            border-bottom-color: #ff4b2b;
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
        <div class="profile-container">
            <span class="profile-title">Profile</span>
            
            <div class="profile-content">
                <div class="profile-avatar"> <img src="/assets/Image2.jpeg" alt="">
                    </div>

                <div class="profile-info">
                    <h2>Lawrence Epstein</h2>
                    <p>lawrence.epstein@gmail.com</p>
                </div>
            </div>

            <div class="logout-container">
                <a href="/logout" class="logout-link">Log Out?</a>
            </div>
        </div>
    </main>
</div>

</body>
</html>