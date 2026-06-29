<?php
// backend/razorpay_config.php

require_once __DIR__ . '/load_env.php';

// Razorpay API Keys
$keyId     = $_ENV['RAZORPAY_KEY_ID']     ?? '';
$keySecret = $_ENV['RAZORPAY_KEY_SECRET'] ?? '';

// Fixed booking amount (in INR) - used as fallback
$bookingAmount = (int) ($_ENV['RAZORPAY_BOOKING_AMOUNT'] ?? 1500);

// ── SMTP Email Configuration ───────────────────────────────────────────────────
define('SMTP_HOST',       $_ENV['SMTP_HOST']       ?? '');
define('SMTP_PORT',  (int)($_ENV['SMTP_PORT']       ?? 587));
define('SMTP_SECURE',     $_ENV['SMTP_SECURE']     ?? 'tls');
define('SMTP_USER',       $_ENV['SMTP_USER']       ?? '');
define('SMTP_PASS',       $_ENV['SMTP_PASS']       ?? '');
define('SMTP_FROM_NAME',  $_ENV['SMTP_FROM_NAME']  ?? 'Nishchala Yoga');
define('SMTP_FROM_EMAIL', $_ENV['SMTP_FROM_EMAIL'] ?? '');
?>
