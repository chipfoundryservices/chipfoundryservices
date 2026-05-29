<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the current content from ID 1737 (which now has the full content)
    $stmt = $pdo->prepare("SELECT response FROM qa_responses WHERE id = 1737");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $content = $row['response'];
        $originalLength = strlen($content);
        
        // Find and remove extra dash lines in tables
        // Pattern: |---|---|---| (table separator rows)
        $patterns = [
            '/\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\n/m',  // 3 column dash rows
            '/\|\s*-+\s*\|\s*-+\s*\|\n/m',            // 2 column dash rows
            '/\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\n/m', // 4 column dash rows
            '/\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\n/m', // 5 column dash rows
        ];
        
        $updatedContent = $content;
        $changesCount = 0;
        
        foreach ($patterns as $pattern) {
            $before = $updatedContent;
            $updatedContent = preg_replace($pattern, '', $updatedContent);
            if ($before !== $updatedContent) {
                $changesCount++;
                echo "Removed dash lines with pattern: " . $pattern . "\n";
            }
        }
        
        // Also remove any standalone lines that are just dashes and pipes
        $lines = explode("\n", $updatedContent);
        $cleanedLines = [];
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            // Skip lines that are only dashes, pipes, and spaces
            if (preg_match('/^[\|\-\s]+$/', $trimmed) && strlen($trimmed) > 5) {
                echo "Removing dash line: " . $trimmed . "\n";
                $changesCount++;
                continue;
            }
            $cleanedLines[] = $line;
        }
        
        $finalContent = implode("\n", $cleanedLines);
        
        if ($changesCount > 0) {
            // Update the database
            $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = 1737");
            $updateStmt->execute([$finalContent]);
            
            echo "\nSuccessfully removed $changesCount dash line patterns!\n";
            echo "Original length: $originalLength characters\n";
            echo "New length: " . strlen($finalContent) . " characters\n";
            echo "Lines removed: " . (count(explode("\n", $content)) - count(explode("\n", $finalContent))) . "\n";
        } else {
            echo "No extra dash lines found to remove.\n";
        }
        
    } else {
        echo "Entry ID 1737 not found\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>