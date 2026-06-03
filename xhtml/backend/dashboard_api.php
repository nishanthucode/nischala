<?php
header('Content-Type: application/json');
require_once __DIR__ . '/functions.php';

$pdo = pdo();
$data = [];

try {
    // 1. Total Members
    $stmt = $pdo->query("SELECT COUNT(DISTINCT customer_email) as total FROM bookings");
    $data['total_members'] = (int) $stmt->fetchColumn();

    // 2. New Registrations
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings");
    $data['new_registrations'] = (int) $stmt->fetchColumn();

    // 3. Active Classes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM classes");
    $data['active_classes'] = (int) $stmt->fetchColumn();

    // 4. Revenue
    $stmt = $pdo->query("SELECT SUM(CAST(c.price AS DECIMAL) * b.quantity) as total FROM bookings b JOIN classes c ON b.class_id = c.id");
    $data['revenue'] = (float) $stmt->fetchColumn();

    // 5. Top Instructors
    $stmt = $pdo->query("SELECT instructor as name, COUNT(*) as classes_count FROM classes WHERE instructor IS NOT NULL AND instructor != '' GROUP BY instructor ORDER BY classes_count DESC LIMIT 5");
    $data['top_instructors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Recent Registrations
    $stmt = $pdo->query("
        SELECT b.id, b.customer_name as name, c.instructor, b.booking_date, b.payment_status as status, c.name as class_name, c.price, b.quantity
        FROM bookings b
        JOIN classes c ON b.class_id = c.id
        ORDER BY b.id DESC
        LIMIT 5
    ");
    $data['recent_registrations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
