<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$userId = isset($input['user_id']) ? (int)$input['user_id'] : 0;
$email = isset($input['email']) ? $input['email'] : '';
$currentPassword = isset($input['current_password']) ? $input['current_password'] : '';
$newPassword = isset($input['new_password']) ? $input['new_password'] : '';

if (!$userId || !$email || !$currentPassword || !$newPassword) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if (strlen($newPassword) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

try {
    // Verify current password
    $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ? AND email = ?');
    $stmt->execute([$userId, $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    if (!password_verify($currentPassword, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }
    
    // Update password
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $stmt->execute([$newPasswordHash, $userId]);
    
    echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to update password']);
}
?>
