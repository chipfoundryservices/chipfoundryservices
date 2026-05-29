<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all entries from the database
    $stmt = $pdo->query("SELECT id, keywords, response FROM qa_responses WHERE keywords != 'default'");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Processing " . count($rows) . " database entries...\n\n";
    
    $totalChanges = 0;
    $entriesModified = 0;
    
    foreach ($rows as $row) {
        $id = $row['id'];
        $content = $row['response'];
        $originalLength = strlen($content);
        
        // Remove various patterns of dash lines in tables
        $patterns = [
            // Standard table separator patterns
            '/\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\n/m', // 6 columns
            '/\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\n/m',           // 5 columns
            '/\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\n/m',                     // 4 columns
            '/\|\s*-+\s*\|\s*-+\s*\|\s*-+\s*\|\n/m',                               // 3 columns
            '/\|\s*-+\s*\|\s*-+\s*\|\n/m',                                         // 2 columns
        ];
        
        $updatedContent = $content;
        $entryChanges = 0;
        
        // Apply regex patterns
        foreach ($patterns as $pattern) {
            $before = $updatedContent;
            $updatedContent = preg_replace($pattern, '', $updatedContent);
            if ($before !== $updatedContent) {
                $entryChanges++;
            }
        }
        
        // Also remove lines that start with |--- or |------ (problematic table rows)
        $lines = explode("\n", $updatedContent);
        $cleanedLines = [];
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            
            // Skip lines that are table separators or have dashes in first column
            if (preg_match('/^\|\s*-+/', $trimmed) && strlen($trimmed) > 5) {
                $entryChanges++;
                continue;
            }
            
            // Skip lines that are only dashes, pipes, and spaces
            if (preg_match('/^[\|\-\s]+$/', $trimmed) && strlen($trimmed) > 5) {
                $entryChanges++;
                continue;
            }
            
            $cleanedLines[] = $line;
        }
        
        $finalContent = implode("\n", $cleanedLines);
        
        if ($entryChanges > 0) {
            // Update the database entry
            $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = ?");
            $updateStmt->execute([$finalContent, $id]);
            
            $newLength = strlen($finalContent);
            $linesRemoved = count(explode("\n", $content)) - count(explode("\n", $finalContent));
            
            echo "ID $id: Removed $entryChanges dash patterns, $linesRemoved lines\n";
            echo "  Keywords: " . substr($row['keywords'], 0, 60) . "...\n";
            echo "  Size: $originalLength → $newLength chars\n";
            
            $totalChanges += $entryChanges;
            $entriesModified++;
        }
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "Total entries processed: " . count($rows) . "\n";
    echo "Entries modified: $entriesModified\n";
    echo "Total dash patterns removed: $totalChanges\n";
    echo "Database cleanup completed!\n";
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>