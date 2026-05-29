<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the new content from ID 10596
    $stmt = $pdo->prepare("SELECT response FROM qa_responses WHERE id = 10596");
    $stmt->execute();
    $newContentRow = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($newContentRow) {
        $newContent = $newContentRow['response'];
        
        // Update ID 3 with the new clean content
        $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = 3");
        $updateStmt->execute([$newContent]);
        
        echo "Successfully updated ID 3 with new clean LLM training content!\n";
        echo "Content length: " . strlen($newContent) . " characters\n";
        echo "Content lines: " . count(explode("\n", $newContent)) . " lines\n";
    } else {
        echo "Could not find new content in ID 10596\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>