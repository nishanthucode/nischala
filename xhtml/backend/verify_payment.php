<?php
require_once 'config.php';
require_once 'razorpay_config.php';

// If PHPMailer is available
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require '../vendor/autoload.php'; // Uncomment if using composer
// require 'PHPMailer/src/Exception.php'; // Uncomment if manual install
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid Request");
}

$razorpay_payment_id = $_POST['razorpay_payment_id'] ?? '';
$razorpay_order_id = $_POST['razorpay_order_id'] ?? '';
$razorpay_signature = $_POST['razorpay_signature'] ?? '';

if (empty($razorpay_payment_id) || empty($razorpay_order_id) || empty($razorpay_signature)) {
    header("Location: failed.php?msg=Missing parameters");
    exit;
}

$generated_signature = hash_hmac('sha256', $razorpay_order_id . '|' . $razorpay_payment_id, $keySecret);

if (hash_equals($generated_signature, $razorpay_signature)) {
    try {
        $pdo = pdo();
        $stmt = $pdo->prepare("UPDATE program_bookings SET payment_status = 'paid', razorpay_payment_id = ? WHERE razorpay_order_id = ?");
        $stmt->execute([$razorpay_payment_id, $razorpay_order_id]);

        $stmt = $pdo->prepare("SELECT * FROM program_bookings WHERE razorpay_order_id = ?");
        $stmt->execute([$razorpay_order_id]);
        $booking = $stmt->fetch();

        if ($booking) {
            $customer_name = $booking['customer_name'];
            $email = $booking['email'];
            $selected_program = $booking['selected_program'];
            $preferred_time = $booking['preferred_time'];
            $amount = $booking['amount'];

            // Insert into enquiries for admin dashboard visibility
            $enq_stmt = $pdo->prepare("INSERT INTO enquiries (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, ?)");
            $enq_subject = $selected_program . ($preferred_time ? ' | ' . $preferred_time : '');
            $enq_message = "Payment Successful.\n\nProgram: {$selected_program}\nTime: {$preferred_time}\nAmount Paid: ₹{$amount}\nRazorpay Order ID: {$razorpay_order_id}\nRazorpay Payment ID: {$razorpay_payment_id}";
            $enq_stmt->execute([$customer_name, $email, $booking['phone'], $enq_subject, $enq_message, 'Paid']);

            // Send Confirmation Email
            try {
                // Uncomment and configure below lines to enable email sending via PHPMailer
                /*
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.example.com'; 
                $mail->SMTPAuth   = true;
                $mail->Username   = 'your_email@example.com';
                $mail->Password   = 'your_email_password';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('no-reply@nishchal.com', 'Nishchal Yoga');
                $mail->addAddress($email, $customer_name);

                $mail->isHTML(false);
                $mail->Subject = 'Booking Confirmed - Payment Successful';
                $mail->Body    = "Hello {$customer_name},\n\nYour booking has been confirmed.\n\nProgram:\n{$selected_program}\n\nDate/Time:\n{$preferred_time}\n\nAmount Paid:\n₹{$amount}\n\nPayment ID:\n{$razorpay_payment_id}\n\nThank you for registering.";

                $mail->send();
                */
            } catch (Exception $e) {
                error_log("Message could not be sent. Mailer Error: " . $e->getMessage());
            }
        }

        header("Location: success.php?payment_id=" . urlencode($razorpay_payment_id));
        exit;
    } catch (PDOException $e) {
        header("Location: failed.php?msg=Database error");
        exit;
    }
} else {
    header("Location: failed.php?msg=Signature Verification Failed");
    exit;
}
?>
