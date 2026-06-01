<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

// Expect JSON or form POST
$data = $_POST ?: json_decode(file_get_contents('php://input'), true) ?: [];

$required = ['class_id', 'customer_name', 'customer_email', 'booking_date', 'time_slot'];
foreach ($required as $r) {
    if (empty($data[$r])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $r . ' is required']);
        exit;
    }
}

$classId = (int) $data['class_id'];
$name = trim((string) $data['customer_name']);
$email = trim((string) $data['customer_email']);
$phone = trim((string) ($data['customer_phone'] ?? ''));
$tier = trim((string) ($data['program_tier'] ?? ''));
$date = trim((string) $data['booking_date']);
$timeSlot = trim((string) $data['time_slot']);
$quantity = max(1, (int) ($data['quantity'] ?? 1));
$personalOrGroup = in_array($data['personal_or_group'] ?? 'personal', ['personal', 'group'], true) ? $data['personal_or_group'] : 'personal';

// Simple validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email']);
    exit;
}

// Fetch class to check capacity
$stmt = pdo()->prepare('SELECT * FROM classes WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $classId]);
$class = $stmt->fetch();
if (!$class) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Class not found']);
    exit;
}

$maxPerSlot = (int) ($class['slot_capacity'] ?? $class['max_students'] ?? 0);

// Count existing bookings for same class/date/time_slot
$stmt = pdo()->prepare('SELECT SUM(quantity) as booked FROM bookings WHERE class_id = :cid AND booking_date = :bdate AND time_slot = :ts AND payment_status != "cancelled"');
$stmt->execute(['cid' => $classId, 'bdate' => $date, 'ts' => $timeSlot]);
$row = $stmt->fetch();
$booked = (int) ($row['booked'] ?? 0);

if ($maxPerSlot > 0 && ($booked + $quantity) > $maxPerSlot) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Selected time slot is full']);
    exit;
}

// Insert booking
$insert = pdo()->prepare('INSERT INTO bookings (class_id, customer_name, customer_email, customer_phone, program_tier, booking_date, time_slot, quantity, personal_or_group, payment_status) VALUES (:class_id, :name, :email, :phone, :tier, :date, :time_slot, :quantity, :porg, :status)');
$insert->execute([
    'class_id' => $classId,
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'tier' => $tier,
    'date' => $date,
    'time_slot' => $timeSlot,
    'quantity' => $quantity,
    'porg' => $personalOrGroup,
    'status' => 'confirmed', // for prototyping mark confirmed; integrate payment later
]);

$bookingId = (int) pdo()->lastInsertId();

// Send notifications (best-effort)
$subject = sprintf('Booking Confirmed: %s on %s', $class['name'], $date);
$body = "Hello " . htmlspecialchars($name) . ",\n\nYour booking for " . $class['name'] . " on " . $date . " at " . $timeSlot . " has been received. Booking ID: " . $bookingId . "\n\nThank you.";

try {
    send_email($email, $subject, $body);
} catch (Throwable $e) {
    // ignore notification failures
}

try {
    send_sms($phone, substr($body, 0, 300));
} catch (Throwable $e) {
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['success' => true, 'booking_id' => $bookingId, 'message' => 'Booking confirmed']);
