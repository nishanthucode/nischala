<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background-color: #f9f9f9; }
        .container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { color: #dc3545; }
        p { font-size: 16px; color: #555; }
        .btn { display: inline-block; padding: 10px 20px; background: #E67527; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
        .btn:hover { background: #99C83D; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Payment Failed</h1>
        <p>Unfortunately, your payment could not be processed.</p>
        <?php if(isset($_GET['msg'])): ?>
            <p><strong>Reason:</strong> <?php echo htmlspecialchars($_GET['msg']); ?></p>
        <?php endif; ?>
        <a href="../index.html#register-interest" class="btn">Try Again</a>
    </div>
</body>
</html>
