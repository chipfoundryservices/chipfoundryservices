<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the transformer entry
    $stmt = $pdo->prepare("SELECT response FROM qa_responses WHERE id = 37");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $response = $row['response'];
        
        // Remove the dash line from the table
        $oldTable = "| Operation | Time Complexity | Space Complexity |\n|-----------|-----------------|------------------|";
        $newTable = "| Operation | Time Complexity | Space Complexity |";
        
        $updatedResponse = str_replace($oldTable, $newTable, $response);
        
        if ($updatedResponse !== $response) {
            // Update the database
            $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = 37");
            $updateStmt->execute([$updatedResponse]);
            
            echo "Successfully removed dash line from Transformer architecture table!\n";
            echo "Updated entry ID 37\n";
        } else {
            echo "No changes needed - dash line not found in expected format\n";
        }
    } else {
        echo "Entry ID 37 not found\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>