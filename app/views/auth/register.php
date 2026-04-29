<?php
session_start();
require '../app/config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Kata sandi tidak cocok!";
    } else {
        // Cek apakah email sudah terdaftar
        $cek_email = $conn->query("SELECT id FROM users WHERE email = '$email'");
        if ($cek_email->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Enkripsi password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Simpan ke database
            $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";
            if ($conn->query($query)) {
                // Jika sukses, arahkan ke login
                header("Location: login.php?registered=true");
                exit;
            } else {
                $error = "Terjadi kesalahan sistem.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Study Track</title>
    <link rel="stylesheet" href="/css/register.css">
</head>
<body>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">DAFTAR</h1>
            <p class="hero-subtitle">Buat akun gratis dan mulai belajar sekarang.</p>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="/register" class="auth-form mx-auto max-w-md">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" required minlength="6" class="form-control">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Kata Sandi</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary w-full">Daftar Gratis</button>
            </form>
            
            <div class="auth-links">
                <p>Sudah punya akun? <a href="/login" class="link">Masuk sekarang</a></p>
            </div>
        </div>
        <div class="hero-image">
            <div class="duo-mascot"><img src="/assets/Image1.png" alt="Duo Mascot"></div>
        </div>
    </div>
</section>

</body>
</html>
