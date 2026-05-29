<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=chipfoundry;charset=utf8mb4",
        'root',
        'fOm7eS:DyRW0'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

if ($q === '') {
    // Return stats when no query
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM qa_responses");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode([
        'success' => true,
        'message' => 'Ready to search. Database has ' . $result['total'] . ' entries.'
    ]);
    exit;
}

// Search in keywords and response
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
        'response' => 'No results found for "' . htmlspecialchars($q) . '". Try searching for: transformer, LLM, semiconductor, chip, or any AI/semiconductor term.'
    ]);
}
?>
