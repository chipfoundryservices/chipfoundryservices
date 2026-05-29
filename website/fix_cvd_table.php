<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the current content from ID 1737
    $stmt = $pdo->prepare("SELECT response FROM qa_responses WHERE id = 1737");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $content = $row['response'];
        
        // Find the CVD Process Parameters section
        $lines = explode("\n", $content);
        $updatedLines = [];
        $inCvdTable = false;
        $changesCount = 0;
        
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];
            
            // Check if we're in the CVD Process Parameters section
            if (strpos($line, 'CVD Process Parameters') !== false) {
                $inCvdTable = true;
                $updatedLines[] = $line;
                continue;
            }
            
            // If we're in the CVD table and find a line with just dashes in the first column
            if ($inCvdTable && preg_match('/^\|\s*-+\s*\|/', $line)) {
                echo "Found problematic CVD table line: " . trim($line) . "\n";
                
                // Skip this line (don't add it to updatedLines)
                $changesCount++;
                continue;
            }
            
            // Stop looking when we reach the next section
            if ($inCvdTable && (strpos($line, '##') !== false || strpos($line, '###') !== false || strpos($line, '####') !== false)) {
                $inCvdTable = false;
            }
            
            $updatedLines[] = $line;
        }
        
        if ($changesCount > 0) {
            $finalContent = implode("\n", $updatedLines);
            
            // Update the database
            $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = 1737");
            $updateStmt->execute([$finalContent]);
            
            echo "\nSuccessfully removed $changesCount problematic CVD table lines!\n";
            echo "Original lines: " . count($lines) . "\n";
            echo "New lines: " . count($updatedLines) . "\n";
        } else {
            echo "No problematic CVD table lines found.\n";
            
            // Let's search for the CVD section to see what's there
            echo "\nSearching for CVD Process Parameters section:\n";
            for ($i = 0; $i < count($lines); $i++) {
                if (strpos($lines[$i], 'CVD Process Parameters') !== false) {
                    echo "Found CVD section at line " . ($i + 1) . "\n";
                    for ($j = $i; $j < min($i + 10, count($lines)); $j++) {
                        echo "Line " . ($j + 1) . ": " . $lines[$j] . "\n";
                    }
                    break;
                }
            }
        }
        
    } else {
        echo "Entry ID 1737 not found\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>