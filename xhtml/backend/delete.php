<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$module = $_GET['module'] ?? '';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$redirectMap = [
    'blogs' => '../admin/all-blogs.html',
    'classes' => '../admin/all-class.html',
    'gallery' => '../admin/all-gallery.html',
    'events' => '../admin/event-management.html',
    'enquiries' => '../admin/all-enquiries.html',
];

$target = $_GET['redirect'] ?? $redirectMap[$module] ?? '../admin/index.html';

if ($module !== '' && $id > 0) {
    backend_delete($module, $id);
}

header('Location: ' . $target . '?deleted=1');
exit;
