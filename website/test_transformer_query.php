<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Find transformer related entries
    $stmt = $pdo->query("SELECT id, keywords, response FROM qa_responses WHERE keywords LIKE '%transformer%' OR response LIKE '%Complexity Analysis%'");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($rows) . " entries:\n\n";
    foreach ($rows as $row) {
        echo "ID: " . $row['id'] . "\n";
        echo "Keywords: " . $row['keywords'] . "\n";
        
        // Check if response contains the problematic table
        if (strpos($row['response'], 'Complexity Analysis') !== false) {
            echo "FOUND COMPLEXITY ANALYSIS TABLE!\n";
            // Look for the table section
            $lines = explode("\n", $row['response']);
            $inTable = false;
            $tableLines = [];
            
            foreach ($lines as $lineNum => $line) {
                if (strpos($line, 'Complexity Analysis') !== false) {
                    $inTable = true;
                    $tableLines[] = "Line " . ($lineNum + 1) . ": " . $line;
                    continue;
                }
                
                if ($inTable) {
                    $tableLines[] = "Line " . ($lineNum + 1) . ": " . $line;
                    
                    // Stop after we've captured the table
                    if (trim($line) === '' && count($tableLines) > 10) {
                        break;
                    }
                }
            }
            
            echo "Table section:\n";
            foreach (array_slice($tableLines, 0, 15) as $tableLine) {
                echo $tableLine . "\n";
            }
        }
        echo "---\n\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>