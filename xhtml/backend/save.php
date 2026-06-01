<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$module = $_POST['module'] ?? '';
$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;

$redirectMap = [
    'blogs' => '../admin/all-blogs.html',
    'classes' => '../admin/all-courses.html',
    'gallery' => '../admin/all-gallery.html',
    'events' => '../admin/event-management.html',
];

$target = $_POST['redirect'] ?? $redirectMap[$module] ?? '../admin/index.html';

try {
    backend_save($module, $_POST, $_FILES, $id);
    header('Location: ' . $target . '?success=1');
    exit;
} catch (Throwable $throwable) {
    header('Location: ' . $target . '?error=' . urlencode($throwable->getMessage()));
    exit;
}
