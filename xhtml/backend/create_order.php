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

if (empty($firstName) || empty($phone) || empty($program) || empty($time)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
    exit;
}

try {
    $pdo = pdo();



    // Map prices based on program and time (from class.html)
    $pricing = [
        'Yogasanas' => [
            '12:30 PM - 1:15 PM' => 3000,
            'Sep 14 - 15 (Weekend) | 12:30 PM - 2:30 PM' => 1500,
            'Oct 14 - 19 | 12:30 PM - 1:15 PM' => 3000
        ],
        'Anandam' => [
            'Morning: 6:00 AM - 7:15 AM' => 999,
            'Evening: 7:30 PM - 8:45 PM' => 999
        ],
        'Shanmukhi Mudra' => [
            'Aug 15 Morning | 7:30 AM - 9:30 AM' => 800,
            'Aug 15 Evening | 4:00 PM - 6:00 PM' => 800
        ],
        'Jala Neti' => [
            'Aug 15 | 12:30 PM - 1:15 PM' => 2000
        ],
        'Angamardana' => [
            'Aug 19 - 23 | 7:00 PM - 9:15 PM' => 2800,
            'Sep 14 - 15 (Weekend) | 6:00 PM - 8:00 PM' => 1200,
            'Oct 14 - 18 | 7:00 PM - 9:15 PM' => 2800
        ],
        'Sunayana' => [
            'Sep 3 - 5 & Oct 22 - 24 | 6:00 - 8:00 AM' => 1800,
            'Sep 3 - 5 & Oct 22 - 24 | 3:30 - 5:30 PM' => 1800,
            'Sep 3 - 5 & Oct 22 - 24 | 7:00 - 9:00 PM' => 1800
        ],
        'Bhuta Shuddhi' => [
            'Sep 14 | 9:30 AM - 11:00 AM' => 3000
        ],
        'Surya Kriya' => [
            'Aug 20 - 23 | 4:30 PM - 6:30 PM' => 2500,
            'Sep 14 - 15 (Weekend) | 6:00 AM - 9:00 AM' => 2000,
            'Oct 15 - 18 | 4:30 PM - 6:30 PM' => 2500
        ]
    ];

    if (isset($pricing[$program][$time])) {
        $bookingAmount = $pricing[$program][$time];
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
