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
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'Missing user_id']);
    exit;
}

try {
    // Get user info
    $stmt = $pdo->prepare('SELECT email, created_at, last_login FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }
    
    // Get chat count (from chat_sessions)
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM chat_sessions WHERE session_id LIKE ?');
    $stmt->execute(["%user_{$userId}%"]);
    $chatCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get message count (from group_messages)
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM group_messages WHERE user_id = ?');
    $stmt->execute([$userId]);
    $messageCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get group count (from group_participants)
    $stmt = $pdo->prepare('SELECT COUNT(DISTINCT room_id) as count FROM group_participants WHERE user_id = ?');
    $stmt->execute([$userId]);
    $groupCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'email' => $user['email'],
        'created_at' => $user['created_at'],
        'last_login' => $user['last_login'],
        'chat_count' => $chatCount,
        'message_count' => $messageCount,
        'group_count' => $groupCount
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Query failed']);
}
?>
