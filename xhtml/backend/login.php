<?php
session_start();
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Hardcoded credentials for single admin setup
    $admin_email = '';
    $admin_password = '';

    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: ../admin/index.html");
        exit;
    } else {
        header("Location: ../admin/page-login.html?error=Invalid+credentials");
        exit;
    }
}
