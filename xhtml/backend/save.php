<?php

require_once __DIR__ . '/functions.php';

$module = $_POST['module'] ?? '';
$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;

// Check if POST data was dropped (happens when upload exceeds post_max_size)
if (empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post' && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
    header('Location: ../admin/index.html?error=File+is+too+large+(exceeds+server+post_max_size+limit).');
    exit;
}

$redirectMap = [
    'blogs' => '../admin/all-blogs.html',
    'classes' => '../admin/all-class.html',
    'gallery' => '../admin/all-gallery.html',
    'events' => '../admin/event-management.html',
];

$target = $_POST['redirect'] ?? $redirectMap[$module] ?? '../admin/index.html';

// Return JSON for AJAX (XMLHttpRequest) OR if the frontend explicitly asks for JSON via ajax_mode
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isFetch = !empty($_POST['ajax_mode']);

try {
    backend_save($module, $_POST, $_FILES, $id);
    if ($isAjax || $isFetch) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 1, 'msg' => 'Success! Your enquiry has been submitted.']);
        exit;
    }
    header('Location: ' . $target . '?success=1');
    exit;
} catch (Throwable $throwable) {
    if ($isAjax || $isFetch) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 0, 'msg' => $throwable->getMessage()]);
        exit;
    }
    header('Location: ' . $target . '?error=' . urlencode($throwable->getMessage()));
    exit;
}

