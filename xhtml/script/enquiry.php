<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/functions.php';

header('Content-Type: application/json');

try {
    $firstName = trim($_POST['dzFirstName'] ?? '');
    $lastName = trim($_POST['dzLastName'] ?? '');
    $phone = trim($_POST['dzPhone'] ?? '');
    $email = trim($_POST['dzEmail'] ?? '');
    $program = trim($_POST['dzProgram'] ?? '');
    $time = trim($_POST['dzTime'] ?? '');

    if ($firstName === '' || $email === '') {
        throw new Exception('First Name and Email are required.');
    }

    $name = $firstName . ' ' . $lastName;
    $subject = 'Enquiry for ' . ($program ?: 'Yoga Program');
    
    $message = "Program: $program\n";
    $message .= "Preferred Batch/Session: $time\n";
    if ($phone) {
        $message .= "Phone: $phone\n";
    }

    $stmt = pdo()->prepare("INSERT INTO enquiries (name, email, phone, subject, message, status) VALUES (:name, :email, :phone, :subject, :message, 'New')");
    $stmt->execute([
        'name' => trim($name),
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message
    ]);

    echo json_encode([
        'status' => 1,
        'msg' => 'Thank you! We have received your enquiry and will get back to you soon.'
    ]);
    exit;
} catch (Exception $e) {
    echo json_encode([
        'status' => 0,
        'msg' => 'Something went wrong: ' . $e->getMessage()
    ]);
    exit;
}
