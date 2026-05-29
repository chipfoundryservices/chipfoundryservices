<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$dbname = 'chipfoundry';
$username = 'root';
$password = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Create tables if not exist
$pdo->exec("CREATE TABLE IF NOT EXISTS chat_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) UNIQUE,
    ip_address VARCHAR(50),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100),
    sender ENUM('user', 'support') DEFAULT 'user',
    message TEXT,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(session_id)
)");

$action = $_GET['action'] ?? ($_POST['action'] ?? null);
$data = json_decode(file_get_contents('php://input'), true);
if ($data) {
    $action = $data['action'] ?? $action;
}

switch ($action) {
    case 'send':
        $session_id = $data['session_id'] ?? $_POST['session_id'];
        $message = $data['message'] ?? $_POST['message'];
        $sender = $data['sender'] ?? 'user';
        
        // Create/update session
        $stmt = $pdo->prepare("INSERT INTO chat_sessions (session_id, ip_address, user_agent) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE last_activity = CURRENT_TIMESTAMP");
        $stmt->execute([$session_id, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
        
        // Insert message
        $stmt = $pdo->prepare("INSERT INTO chat_messages (session_id, sender, message) VALUES (?, ?, ?)");
        $stmt->execute([$session_id, $sender, $message]);
        
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;
        
    case 'get':
        $session_id = $_GET['session_id'] ?? $data['session_id'];
        $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE session_id = ? ORDER BY created_at ASC");
        $stmt->execute([$session_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['messages' => $messages]);
        break;
        
    case 'sessions':
        // Get all active sessions for admin
        $stmt = $pdo->query("SELECT cs.*, 
            (SELECT COUNT(*) FROM chat_messages cm WHERE cm.session_id = cs.session_id AND cm.sender = 'user' AND cm.is_read = 0) as unread,
            (SELECT message FROM chat_messages cm WHERE cm.session_id = cs.session_id ORDER BY created_at DESC LIMIT 1) as last_message
            FROM chat_sessions cs 
            ORDER BY cs.last_activity DESC");
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['sessions' => $sessions]);
        break;
        
    case 'mark_read':
        $session_id = $data['session_id'] ?? $_GET['session_id'];
        $stmt = $pdo->prepare("UPDATE chat_messages SET is_read = 1 WHERE session_id = ? AND sender = 'user'");
        $stmt->execute([$session_id]);
        echo json_encode(['success' => true]);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>
