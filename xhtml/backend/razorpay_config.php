<?php
// backend/razorpay_config.php

// Razorpay API Keys


//live keys
$keyId = '';
$keySecret = '';


// Fixed booking amount (in INR) - used as fallback
$bookingAmount = 1500;

// ── SMTP Email Configuration (Gmail - for testing) ───────────────────────────
// To use Gmail:
// 1. Enable 2-Step Verification on your Google account
// 2. Go to myaccount.google.com > Security > App Passwords
// 3. Generate an App Password for "Mail" and paste it below (remove spaces)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);                          // Gmail uses 587 + TLS
define('SMTP_SECURE', 'tls');                        // Must be 'tls' for Gmail
define('SMTP_USER', 'nishanthlab024@gmail.com');        // ← Your Gmail address
define('SMTP_PASS', 'ptkvqmyhplhnrobz');           // ← 16-char App Password (no spaces)
define('SMTP_FROM_NAME', 'Nishchala Yoga');
define('SMTP_FROM_EMAIL', 'nishanthlab024@gmail.com');       // ← Same as SMTP_USER
?>