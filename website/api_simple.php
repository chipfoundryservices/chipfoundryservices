<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache');
ini_set('max_execution_time', '5');

$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'DB failed']);
    exit;
}

$q = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';

if (empty($q)) {
    echo json_encode(['response' => 'Please ask a question.']);
    exit;
}

// Direct keyword search (much faster)
$stmt = $pdo->prepare("SELECT response FROM qa_responses WHERE LOWER(keywords) LIKE ? LIMIT 1");
$stmt->execute(['%' . $q . '%']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo json_encode(['response' => $row['response']]);
} else {
    echo json_encode(['response' => 'I don\'t have information about that yet. Try keywords like: llm, gpu, chip, transformer, rag, quantization']);
}
?>
