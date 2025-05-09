<?php
require_once __DIR__ . '/core/Database.php';

try {
    $pdo = Database::getInstance()->getConnection();
    
    // Generate bcrypt hash for 'admin123'
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Update admin user password
    $stmt = $pdo->prepare('UPDATE users SET password = :password WHERE username = :username');
    $stmt->execute([
        'password' => $hash,
        'username' => 'admin'
    ]);
    
    echo "Password updated successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
