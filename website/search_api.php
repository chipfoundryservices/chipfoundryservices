<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($q)) {
    echo json_encode(['error' => 'No search query provided']);
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=chipfoundry;charset=utf8mb4",
        'root',
        'fOm7eS:DyRW0'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $searchTerm = '%' . $q . '%';
    
    $stmt = $pdo->prepare("
        SELECT id, keywords, response 
        FROM qa_responses 
        WHERE keywords LIKE :search OR response LIKE :search
        LIMIT 1
    ");
    
    $stmt->execute([':search' => $searchTerm]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'response' => $result['response'],
            'keywords' => $result['keywords'],
            'id' => $result['id']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'response' => 'No results found for "' . htmlspecialchars($q) . '"'
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
