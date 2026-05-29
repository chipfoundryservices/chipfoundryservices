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

// Create tables if not exist
$pdo->exec("CREATE TABLE IF NOT EXISTS group_rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id VARCHAR(100) UNIQUE NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(room_id)
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS group_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id VARCHAR(100) NOT NULL,
    user_id INT NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(room_id),
    INDEX(created_at)
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS group_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id VARCHAR(100) NOT NULL,
    user_id INT NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_participant (room_id, user_id),
    INDEX(room_id)
)");

$action = isset($_GET['action']) ? $_GET['action'] : '';
$input = json_decode(file_get_contents('php://input'), true);

// Create room
if ($action === 'create_room') {
    $roomId = $input['room_id'] ?? '';
    $userId = $input['user_id'] ?? 0;
    
    if (!$roomId || !$userId) {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare('INSERT INTO group_rooms (room_id, created_by) VALUES (?, ?)');
        $stmt->execute([$roomId, $userId]);
        echo json_encode(['success' => true, 'room_id' => $roomId]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Room creation failed']);
    }
}

// Join room
elseif ($action === 'join_room') {
    $roomId = $input['room_id'] ?? '';
    $userId = $input['user_id'] ?? 0;
    $userEmail = $input['user_email'] ?? '';
    
    if (!$roomId || !$userId || !$userEmail) {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare('INSERT INTO group_participants (room_id, user_id, user_email) VALUES (?, ?, ?) 
                              ON DUPLICATE KEY UPDATE last_seen = CURRENT_TIMESTAMP');
        $stmt->execute([$roomId, $userId, $userEmail]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Join failed']);
    }
}

// Send message
elseif ($action === 'send_message') {
    $roomId = $input['room_id'] ?? '';
    $userId = $input['user_id'] ?? 0;
    $userEmail = $input['user_email'] ?? '';
    $message = $input['message'] ?? '';
    
    if (!$roomId || !$userId || !$message) {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare('INSERT INTO group_messages (room_id, user_id, user_email, message) VALUES (?, ?, ?, ?)');
        $stmt->execute([$roomId, $userId, $userEmail, $message]);
        
        // Update last seen
        $stmt = $pdo->prepare('UPDATE group_participants SET last_seen = CURRENT_TIMESTAMP WHERE room_id = ? AND user_id = ?');
        $stmt->execute([$roomId, $userId]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Send failed']);
    }
}

// Get messages
elseif ($action === 'get_messages') {
    $roomId = $_GET['room_id'] ?? '';
    $since = $_GET['since'] ?? 0;
    
    if (!$roomId) {
        echo json_encode(['success' => false, 'error' => 'Missing room_id']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare('SELECT id, user_id, user_email, message, created_at 
                              FROM group_messages 
                              WHERE room_id = ? AND id > ? 
                              ORDER BY id ASC 
                              LIMIT 100');
        $stmt->execute([$roomId, $since]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'messages' => $messages]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Fetch failed']);
    }
}

// Get participants
elseif ($action === 'get_participants') {
    $roomId = $_GET['room_id'] ?? '';
    
    if (!$roomId) {
        echo json_encode(['success' => false, 'error' => 'Missing room_id']);
        exit;
    }
    
    try {
        // Get participants active in last 5 minutes
        $stmt = $pdo->prepare('SELECT user_id, user_email, last_seen 
                              FROM group_participants 
                              WHERE room_id = ? AND last_seen > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                              ORDER BY last_seen DESC');
        $stmt->execute([$roomId]);
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'participants' => $participants]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Fetch failed']);
    }
}

else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
