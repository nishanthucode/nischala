<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;
$params = [];
$sql = 'SELECT b.*, c.name as class_name FROM bookings b JOIN classes c ON c.id = b.class_id';
if ($classId) {
    $sql .= ' WHERE b.class_id = :cid';
    $params['cid'] = $classId;
}
$sql .= ' ORDER BY b.id DESC';

$stmt = pdo()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows, JSON_UNESCAPED_SLASHES);
