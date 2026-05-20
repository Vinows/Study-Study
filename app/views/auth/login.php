<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../app/config/db.php';

if (isset($_SESSION['user_id'])) {
    if (($_SESSION['role'] ?? 'student') === 'teacher') {
        header("Location: /teacher");
    } else {
        header("Location: /profile");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    // Cari user berdasarkan email
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];

            if ($user['role'] === 'teacher') {
                header("Location: /teacher");
            } else {
                header("Location: /profile");
            }
            exit;
        } else {
            $error = "Kata sandi salah!";
        }
    } else {
        $error = "Email tidak terdaftar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Study Track</title>
    <link rel="stylesheet" href="/css/register.css">
</head>
<body>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">MASUK</h1>
            <p class="hero-subtitle">Belajar bahasa secara gratis. Selalu.</p>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" style="grid-area: subtitle; margin-top: 30px; color: #ff6b6b;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="/login" class="auth-form mx-auto max-w-md">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary w-full">Masuk</button>
            </form>
            
            <div class="auth-links">
                <p>Belum punya akun? <a href="/register" class="link">Daftar sekarang</a></p>
            </div>
        </div>
        <div class="hero-image">
            <div class="duo-mascot"><img src="/assets/Image1.png" alt="Duo Mascot"></div>
        </div>
    </div>
</section>

</body>
</html>