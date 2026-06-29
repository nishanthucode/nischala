<?php
session_start();
require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Credentials loaded from .env
    $admin_email    = $_ENV['ADMIN_EMAIL']    ?? '';
    $admin_password = $_ENV['ADMIN_PASSWORD'] ?? '';

    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: ../admin/index.html");
        exit;
    } else {
        header("Location: ../admin/page-login.html?error=Invalid+credentials");
        exit;
    }
}
