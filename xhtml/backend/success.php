<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background-color: #f9f9f9; }
        .container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { color: #28a745; }
        p { font-size: 16px; color: #555; }
        .btn { display: inline-block; padding: 10px 20px; background: #E67527; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
        .btn:hover { background: #99C83D; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Payment Successful!</h1>
        <p>Your booking has been confirmed.</p>
        <?php if(isset($_GET['payment_id'])): ?>
            <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($_GET['payment_id']); ?></p>
        <?php endif; ?>
        <p>A confirmation email has been sent to you.</p>
        <a href="../index.html" class="btn">Return to Home</a>
    </div>
</body>
</html>
