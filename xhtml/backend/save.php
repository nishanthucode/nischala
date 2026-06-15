<?php

require_once __DIR__ . '/functions.php';

$module = $_POST['module'] ?? '';
$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;

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

