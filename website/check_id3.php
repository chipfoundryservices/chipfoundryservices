<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check what ID 3 contains
    $stmt = $pdo->prepare("SELECT keywords, response FROM qa_responses WHERE id = 3");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "ID 3 Keywords: " . $row['keywords'] . "\n";
        echo "ID 3 Response length: " . strlen($row['response']) . " characters\n";
        echo "ID 3 Response preview: " . substr($row['response'], 0, 200) . "...\n\n";
    }
    
    // Check ID 10596
    $stmt = $pdo->prepare("SELECT keywords, response FROM qa_responses WHERE id = 10596");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "ID 10596 Keywords: " . $row['keywords'] . "\n";
        echo "ID 10596 Response length: " . strlen($row['response']) . " characters\n";
        echo "ID 10596 Response preview: " . substr($row['response'], 0, 200) . "...\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>