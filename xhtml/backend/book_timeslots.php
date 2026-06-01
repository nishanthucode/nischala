<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$classId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$date = $_GET['date'] ?? null;

if ($classId <= 0 || !$date) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'class_id and date required']);
    exit;
}

$stmt = pdo()->prepare('SELECT * FROM classes WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $classId]);
$class = $stmt->fetch();
if (!$class) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Class not found']);
    exit;
}

$maxPerSlot = (int)($class['slot_capacity'] ?? $class['max_students'] ?? 0);

// Use class-specific timeslots if available, fallback to defaults
$timeslotsStr = trim((string)($class['timeslots'] ?? '06:00,08:00,10:00,12:00,14:00,16:00,18:00'));
$timeslots = array_filter(array_map('trim', explode(',', $timeslotsStr)));

// Get tiers
$tiersStr = trim((string)($class['tiers'] ?? 'General,Premium,VIP'));
$tiers = array_filter(array_map('trim', explode(',', $tiersStr)));

$result = [];
foreach ($timeslots as $slot) {
    if ($slot === '') continue;
    $stmt = pdo()->prepare('SELECT SUM(quantity) as booked FROM bookings WHERE class_id = :cid AND booking_date = :bdate AND time_slot = :ts AND payment_status != "cancelled"');
    $stmt->execute(['cid' => $classId, 'bdate' => $date, 'ts' => $slot]);
    $row = $stmt->fetch();
    $booked = (int)($row['booked'] ?? 0);
    $available = $maxPerSlot <= 0 ? true : ($booked < $maxPerSlot);
    $result[] = ['time' => $slot, 'available' => $available, 'booked' => $booked, 'capacity' => $maxPerSlot];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['success' => true, 'timeslots' => $result, 'tiers' => $tiers], JSON_UNESCAPED_SLASHES);

