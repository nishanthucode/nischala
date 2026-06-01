<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$module = $_GET['module'] ?? 'blogs';
$rows = backend_fetch_all($module);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
