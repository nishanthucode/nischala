<?php
require 'c:/xampp/htdocs/Nishchal/xhtml/backend/config.php';
try {
    pdo()->exec('ALTER TABLE enquiries ADD COLUMN phone VARCHAR(50) NULL AFTER email');
    echo 'Added phone';
} catch (Exception $e) {
    echo $e->getMessage();
}
