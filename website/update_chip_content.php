<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the full content from ID 5
    $stmt = $pdo->prepare("SELECT response FROM qa_responses WHERE id = 5");
    $stmt->execute();
    $fullContentRow = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($fullContentRow) {
        $fullContent = $fullContentRow['response'];
        
        // Update ID 1737 with the full content
        $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = 1737");
        $updateStmt->execute([$fullContent]);
        
        echo "Successfully updated entry ID 1737 with full content!\n";
        echo "Content length: " . strlen($fullContent) . " characters\n";
        echo "Content lines: " . count(explode("\n", $fullContent)) . " lines\n";
    } else {
        echo "Could not find entry ID 5\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>