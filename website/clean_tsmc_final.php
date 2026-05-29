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
        
        // Remove all problematic characters
        $cleanContent = $content;
        
        // Remove all dollar signs
        $cleanContent = str_replace('$', '', $cleanContent);
        
        // Remove all left brackets
        $cleanContent = str_replace('{', '', $cleanContent);
        
        // Remove all right brackets that are left over
        $cleanContent = str_replace('}', '', $cleanContent);
        
        // Remove backslashes
        $cleanContent = str_replace('\\', '', $cleanContent);
        
        // Clean up specific problematic patterns
        $cleanContent = str_replace('###', '##', $cleanContent);
        $cleanContent = str_replace('####', '##', $cleanContent);
        
        // Fix mathematical expressions to be clean
        $cleanContent = str_replace('×', 'x', $cleanContent);
        $cleanContent = str_replace('≈', 'approx', $cleanContent);
        $cleanContent = str_replace('→', '->', $cleanContent);
        $cleanContent = str_replace('↑', 'up', $cleanContent);
        $cleanContent = str_replace('⁻¹', '^-1', $cleanContent);
        
        // Clean up subscripts and superscripts that might be problematic
        $cleanContent = preg_replace('/_([a-zA-Z0-9]+)/', '_\\1', $cleanContent);
        $cleanContent = preg_replace('/\^([a-zA-Z0-9]+)/', '^\\1', $cleanContent);
        
        // Remove any remaining LaTeX-like commands
        $cleanContent = preg_replace('/\\\\[a-zA-Z]+/', '', $cleanContent);
        
        echo "Original length: " . strlen($content) . " characters\\n";
        echo "Cleaned length: " . strlen($cleanContent) . " characters\\n";
        
        // Update the database
        $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = 1738");
        $updateStmt->execute([$cleanContent]);
        
        echo "Successfully cleaned all problematic characters from TSMC vs Intel!\\n";
        echo "Removed: dollar signs, brackets, backslashes, and LaTeX symbols\\n";
        
        // Show sample of cleaned content
        echo "\\nSample of cleaned content:\\n";
        $lines = explode("\\n", $cleanContent);
        for ($i = 0; $i < min(10, count($lines)); $i++) {
            echo $lines[$i] . "\\n";
        }
        
    } else {
        echo "Entry ID 1738 not found\\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>