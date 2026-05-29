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
        
        // Find the Complexity Analysis section
        $lines = explode("\n", $response);
        $found = false;
        $tableStart = -1;
        
        foreach ($lines as $i => $line) {
            if (strpos($line, 'Complexity Analysis') !== false) {
                $found = true;
                $tableStart = $i;
                break;
            }
        }
        
        if ($found) {
            echo "Found Complexity Analysis at line " . ($tableStart + 1) . "\n\n";
            
            // Show lines around the table
            for ($i = $tableStart; $i < $tableStart + 15 && $i < count($lines); $i++) {
                $line = $lines[$i];
                echo "Line " . ($i + 1) . ": [" . strlen($line) . " chars] ";
                
                // Show raw characters
                for ($j = 0; $j < strlen($line); $j++) {
                    $char = $line[$j];
                    if ($char === '|') echo '|';
                    elseif ($char === '-') echo '-';
                    elseif ($char === ' ') echo '·';
                    else echo $char;
                }
                echo "\n";
            }
        } else {
            echo "Complexity Analysis not found\n";
        }
    } else {
        echo "Entry ID 37 not found\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>