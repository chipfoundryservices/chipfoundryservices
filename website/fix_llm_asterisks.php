<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the current content from ID 3
    $stmt = $pdo->prepare("SELECT response FROM qa_responses WHERE id = 3");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $content = $row['response'];
        $originalLength = strlen($content);
        
        // Remove all ** characters (markdown bold formatting)
        $cleanedContent = str_replace('**', '', $content);
        
        $newLength = strlen($cleanedContent);
        $removedCount = ($originalLength - $newLength) / 2; // Each ** pair counts as 2 characters
        
        // Update the database
        $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = 3");
        $updateStmt->execute([$cleanedContent]);
        
        echo "Successfully removed all ** characters from LLM training content!\n";
        echo "Original length: $originalLength characters\n";
        echo "New length: $newLength characters\n";
        echo "Removed approximately $removedCount ** pairs\n";
        
        // Show a sample of the cleaned content
        echo "\nSample of cleaned content:\n";
        $lines = explode("\n", $cleanedContent);
        for ($i = 10; $i < min(25, count($lines)); $i++) {
            if (trim($lines[$i]) !== '') {
                echo $lines[$i] . "\n";
            }
        }
        
    } else {
        echo "Entry ID 3 not found\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>