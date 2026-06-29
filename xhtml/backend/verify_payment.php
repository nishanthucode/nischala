<?php
require_once 'config.php';
require_once 'razorpay_config.php';
require_once 'functions.php';

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
        $subject = '✅ Booking Confirmed – Nishchala Classical Yoga';

        $first_name = explode(' ', trim($customer_name))[0];
        $amount_fmt = '₹' . number_format((float) $amount, 2);

        $body = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:30px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);max-width:600px;width:100%;">

          <!-- Header -->
          <tr>
            <td style="background:linear-gradient(135deg,#2d6a4f 0%,#40916c 100%);padding:40px 40px 30px;text-align:center;">
              <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:700;letter-spacing:0.5px;">Nishchala Classical Yoga</h1>
              <p style="margin:8px 0 0;color:#d8f3dc;font-size:14px;letter-spacing:1px;text-transform:uppercase;">Booking Confirmation</p>
            </td>
          </tr>

          <!-- Success Badge -->
          <tr>
            <td style="background:#d8f3dc;padding:16px 40px;text-align:center;">
              <span style="display:inline-block;background:#2d6a4f;color:#ffffff;font-size:13px;font-weight:600;padding:6px 20px;border-radius:20px;letter-spacing:0.5px;">✔ Payment Successful</span>
            </td>
          </tr>

          <!-- Greeting -->
          <tr>
            <td style="padding:36px 40px 10px;">
              <p style="margin:0;font-size:17px;color:#1b1b1b;font-weight:600;">Dear {$first_name},</p>
              <p style="margin:12px 0 0;font-size:15px;color:#444444;line-height:1.7;">
                Thank you for registering with <strong>Nishchala Classical Yoga</strong>. We are delighted to confirm your booking. Please find your booking details below.
              </p>
            </td>
          </tr>

          <!-- Booking Details -->
          <tr>
            <td style="padding:24px 40px;">
              <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
                <tr style="background:#f9f9f9;">
                  <td colspan="2" style="padding:14px 20px;font-size:13px;font-weight:700;color:#2d6a4f;text-transform:uppercase;letter-spacing:0.8px;border-bottom:1px solid #e0e0e0;">
                    Booking Details
                  </td>
                </tr>
                <tr>
                  <td style="padding:14px 20px;font-size:14px;color:#666;font-weight:600;width:40%;border-bottom:1px solid #f0f0f0;">Program</td>
                  <td style="padding:14px 20px;font-size:14px;color:#1b1b1b;border-bottom:1px solid #f0f0f0;">{$selected_program}</td>
                </tr>
                <tr style="background:#fafafa;">
                  <td style="padding:14px 20px;font-size:14px;color:#666;font-weight:600;border-bottom:1px solid #f0f0f0;">Date / Time</td>
                  <td style="padding:14px 20px;font-size:14px;color:#1b1b1b;border-bottom:1px solid #f0f0f0;">{$preferred_time}</td>
                </tr>
                <tr>
                  <td style="padding:14px 20px;font-size:14px;color:#666;font-weight:600;border-bottom:1px solid #f0f0f0;">Amount Paid</td>
                  <td style="padding:14px 20px;font-size:15px;color:#2d6a4f;font-weight:700;border-bottom:1px solid #f0f0f0;">{$amount_fmt}</td>
                </tr>
                <tr style="background:#fafafa;">
                  <td style="padding:14px 20px;font-size:14px;color:#666;font-weight:600;">Payment Reference</td>
                  <td style="padding:14px 20px;font-size:13px;color:#555;font-family:monospace;">{$razorpay_payment_id}</td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Note -->
          <tr>
            <td style="padding:0 40px 30px;">
              <p style="margin:0;font-size:14px;color:#555;line-height:1.7;background:#fffde7;border-left:4px solid #f9a825;padding:14px 18px;border-radius:4px;">
                📌 Please save this email as your booking reference. Our team will reach out to you with further details.
              </p>
            </td>
          </tr>

          <!-- Divider -->
          <tr>
            <td style="padding:0 40px;"><hr style="border:none;border-top:1px solid #eeeeee;margin:0;"></td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding:24px 40px;text-align:center;">
              <p style="margin:0;font-size:13px;color:#999;">With gratitude,</p>
              <p style="margin:4px 0 0;font-size:14px;font-weight:700;color:#2d6a4f;">Nishchala Classical Yoga</p>
              <p style="margin:12px 0 0;font-size:12px;color:#bbb;">This is an automated confirmation email. Please do not reply directly to this message.</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        send_email($email, $subject, $body, true);
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