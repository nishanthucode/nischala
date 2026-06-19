<?php
header('Content-Type: application/json');
require_once 'config.php';
require_once 'razorpay_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$firstName = $_POST['dzFirstName'] ?? '';
$lastName = $_POST['dzLastName'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$program = $_POST['dzProgram'] ?? '';
$time = $_POST['dzTime'] ?? '';

if (empty($firstName) || empty($lastName) || empty($phone) || empty($email) || empty($program) || empty($time)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
    exit;
}

try {
    $pdo = pdo();

    // Basic duplicate booking check
    $stmt = $pdo->prepare("SELECT id FROM program_bookings WHERE email = ? AND selected_program = ? AND payment_status = 'paid'");
    $stmt->execute([$email, $program]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already booked this program.']);
        exit;
    }

    $amountInPaise = $bookingAmount * 100;
    $receiptId = 'receipt_' . time() . '_' . rand(100, 999);

    // Create Razorpay order
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'amount' => $amountInPaise,
        'currency' => 'INR',
        'receipt' => $receiptId
    ]));
    curl_setopt($ch, CURLOPT_USERPWD, $keyId . ':' . $keySecret);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpStatus === 200) {
        $orderData = json_decode($response, true);
        $orderId = $orderData['id'];

        $customerName = trim($firstName . ' ' . $lastName);
        $stmt = $pdo->prepare("INSERT INTO program_bookings (customer_name, email, phone, selected_program, preferred_time, razorpay_order_id, amount, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$customerName, $email, $phone, $program, $time, $orderId, $bookingAmount]);

        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'key' => $keyId,
            'customer_name' => $customerName,
            'email' => $email,
            'phone' => $phone
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create Razorpay order', 'error' => $response]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
