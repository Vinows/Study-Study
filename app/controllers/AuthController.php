<?php

namespace App\Controllers;

class AuthController {
    
    public function login() {
        // Show login form
        require __DIR__ . '/../views/auth/login.php';
    }
    
    public function register() {
        // Show register form
        require __DIR__ . '/../views/auth/register.php';
    }
    
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $email = trim($email ?? '');
            $password = $_POST['password'] ?? '';
            
            // TODO: Real DB validation with User::findByEmail()
            // Stub for testing
            if ($email === 'test@example.com' && $password === 'password') {
                $_SESSION['user_id'] = 1;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = 'Test User'; // for demo
                header('Location: /home');
                exit;
            } else {
                $error = "Email atau password salah!";
                require __DIR__ . '/../views/auth/login.php';
                return;
            }
        }
    }
    
    public function processRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $name = trim($name ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $email = trim($email ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if ($password !== $confirm_password) {
                $error = "Konfirmasi password tidak cocok!";
                require __DIR__ . '/../views/auth/register.php';
                return;
            }
            
            if (strlen($password) < 6) {
                $error = "Password minimal 6 karakter!";
                require __DIR__ . '/../views/auth/register.php';
                return;
            }
            
            // TODO: Real DB insert with User::create()
            // Stub for testing - allow any email
            $_SESSION['user_id'] = time(); // fake ID
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            header('Location: /home');
            exit;
        }
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header('Location: /');
        exit;
    }
}
