<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get current content from ID 1738
    $stmt = $pdo->prepare("SELECT response FROM qa_responses WHERE id = 1738");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $content = $row['response'];
        
        // Count problematic characters before cleaning
        $dollarCount = substr_count($content, '$');
        $leftBracketCount = substr_count($content, '{');
        
        echo "Before cleaning:\n";
        echo "Dollar signs: $dollarCount\n";
        echo "Left brackets: $leftBracketCount\n\n";
        
        // AGGRESSIVE CLEANING - Remove ALL problematic characters
        $cleanContent = $content;
        
        // Remove ALL dollar signs - every single one
        $cleanContent = str_replace('$', '', $cleanContent);
        
        // Remove ALL left brackets - every single one
        $cleanContent = str_replace('{', '', $cleanContent);
        
        // Remove ALL right brackets too
        $cleanContent = str_replace('}', '', $cleanContent);
        
        // Remove ALL backslashes
        $cleanContent = str_replace('\\', '', $cleanContent);
        
        // Remove any remaining LaTeX-style commands
        $cleanContent = preg_replace('/\\\\[a-zA-Z]+/', '', $cleanContent);
        
        // Clean up any mathematical notation that might have brackets
        $cleanContent = str_replace('(', '(', $cleanContent);
        $cleanContent = str_replace(')', ')', $cleanContent);
        
        // Replace any remaining mathematical symbols with clean text
        $cleanContent = str_replace('₀', '0', $cleanContent);
        $cleanContent = str_replace('₁', '1', $cleanContent);
        $cleanContent = str_replace('₂', '2', $cleanContent);
        $cleanContent = str_replace('₃', '3', $cleanContent);
        $cleanContent = str_replace('₄', '4', $cleanContent);
        $cleanContent = str_replace('₅', '5', $cleanContent);
        $cleanContent = str_replace('₆', '6', $cleanContent);
        $cleanContent = str_replace('₇', '7', $cleanContent);
        $cleanContent = str_replace('₈', '8', $cleanContent);
        $cleanContent = str_replace('₉', '9', $cleanContent);
        
        // Count after cleaning
        $dollarCountAfter = substr_count($cleanContent, '$');
        $leftBracketCountAfter = substr_count($cleanContent, '{');
        
        echo "After cleaning:\n";
        echo "Dollar signs: $dollarCountAfter\n";
        echo "Left brackets: $leftBracketCountAfter\n\n";
        
        // Update the database
        $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = 1738");
        $updateStmt->execute([$cleanContent]);
        
        echo "Successfully removed ALL dollar signs and left brackets!\n";
        echo "Original length: " . strlen($content) . " characters\n";
        echo "Cleaned length: " . strlen($cleanContent) . " characters\n";
        
        // Show sample to verify cleaning
        echo "\nSample of cleaned content:\n";
        $lines = explode("\n", $cleanContent);
        for ($i = 0; $i < min(15, count($lines)); $i++) {
            if (trim($lines[$i]) !== '') {
                echo $lines[$i] . "\n";
            }
        }
        
    } else {
        echo "Entry ID 1738 not found\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>