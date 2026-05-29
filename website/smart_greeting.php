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

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Get user stats
if ($action === 'get_stats') {
    try {
        // Total users
        $stmt = $pdo->query('SELECT COUNT(*) as total FROM users');
        $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Users today
        $stmt = $pdo->query('SELECT COUNT(*) as today FROM users WHERE DATE(created_at) = CURDATE()');
        $usersToday = $stmt->fetch(PDO::FETCH_ASSOC)['today'];
        
        // Users this week
        $stmt = $pdo->query('SELECT COUNT(*) as week FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)');
        $usersWeek = $stmt->fetch(PDO::FETCH_ASSOC)['week'];
        
        // Active users (logged in last 24 hours)
        $stmt = $pdo->query('SELECT COUNT(*) as active FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 24 HOUR)');
        $activeUsers = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
        
        // Progress to 100
        $progress = ($totalUsers / 100) * 100;
        $remaining = 100 - $totalUsers;
        
        // Milestone status
        $milestone = '';
        $milestoneMessage = '';
        
        if ($totalUsers >= 100) {
            $milestone = 'complete';
            $milestoneMessage = '🎉 Milestone Complete! Ready for TinyLlama AI!';
        } elseif ($totalUsers >= 75) {
            $milestone = 'almost';
            $milestoneMessage = '🔥 Almost there! ' . $remaining . ' users to go!';
        } elseif ($totalUsers >= 50) {
            $milestone = 'halfway';
            $milestoneMessage = '🚀 Halfway there! Keep growing!';
        } elseif ($totalUsers >= 25) {
            $milestone = 'quarter';
            $milestoneMessage = '💪 Quarter milestone reached!';
        } else {
            $milestone = 'starting';
            $milestoneMessage = '🌱 Building your community...';
        }
        
        echo json_encode([
            'success' => true,
            'total_users' => $totalUsers,
            'users_today' => $usersToday,
            'users_week' => $usersWeek,
            'active_users' => $activeUsers,
            'progress' => round($progress, 1),
            'remaining' => $remaining,
            'milestone' => $milestone,
            'milestone_message' => $milestoneMessage
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Query failed']);
    }
}

// Get personalized greeting
elseif ($action === 'get_greeting') {
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
        
        // Get total users
        $stmt = $pdo->query('SELECT COUNT(*) as total FROM users');
        $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Determine greeting type
        $isFirstTime = empty($user['last_login']);
        $createdToday = date('Y-m-d', strtotime($user['created_at'])) === date('Y-m-d');
        $userName = explode('@', $user['email'])[0];
        
        $greeting = '';
        $message = '';
        $badge = '';
        
        if ($userId === 1) {
            $greeting = "Welcome back, Founder! 👑";
            $message = "You're User #1 - the pioneer of ChipFoundryServices!";
            $badge = "🏆 Founder";
        } elseif ($userId <= 10) {
            $greeting = "Welcome back, Early Adopter! 🌟";
            $message = "You're one of the first 10 users - thank you for believing in us!";
            $badge = "⭐ Early Adopter";
        } elseif ($isFirstTime) {
            $greeting = "Welcome to ChipFoundryServices, " . $userName . "! 🎉";
            $message = "You're user #" . $userId . " of 100. Let's explore together!";
            $badge = "🆕 New Member";
        } elseif ($createdToday) {
            $greeting = "Welcome back, " . $userName . "! 👋";
            $message = "Great to see you again today!";
            $badge = "✨ Active Today";
        } else {
            $greeting = "Hello, " . $userName . "! 👋";
            $message = "Welcome back to ChipFoundryServices!";
            $badge = "💎 Member";
        }
        
        // Add milestone context
        if ($totalUsers >= 100) {
            $message .= " We've reached 100 users - AI upgrade unlocked! 🚀";
        } else {
            $remaining = 100 - $totalUsers;
            $message .= " (" . $remaining . " more users until AI upgrade!)";
        }
        
        echo json_encode([
            'success' => true,
            'greeting' => $greeting,
            'message' => $message,
            'badge' => $badge,
            'user_id' => $userId,
            'is_first_time' => $isFirstTime,
            'total_users' => $totalUsers
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Query failed']);
    }
}

else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
