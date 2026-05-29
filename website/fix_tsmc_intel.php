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
        
        // Remove all LaTeX formatting
        // Remove $$ delimiters
        $cleanContent = str_replace('$$', '', $content);
        
        // Remove single $ delimiters
        $cleanContent = str_replace('$', '', $cleanContent);
        
        // Remove \text{} commands and keep the content
        $cleanContent = preg_replace('/\\\\text\{([^}]*)\}/', '$1', $cleanContent);
        
        // Remove \frac{}{} and replace with simple division
        $cleanContent = preg_replace('/\\\\frac\{([^}]*)\}\{([^}]*)\}/', '($1)/($2)', $cleanContent);
        
        // Remove other LaTeX commands
        $cleanContent = preg_replace('/\\\\[a-zA-Z]+\{([^}]*)\}/', '$1', $cleanContent);
        
        // Remove backslashes
        $cleanContent = str_replace('\\', '', $cleanContent);
        
        // Remove extra curly braces
        $cleanContent = preg_replace('/\{([^}]*)\}/', '$1', $cleanContent);
        
        // Clean up the revenue ratio example
        $cleanContent = str_replace('Revenue Ratio = (TSMC Revenue)/(Intel Foundry Revenue) = (101B)/(120M) approx 842:1', 
                                   'Revenue Ratio = TSMC Revenue / Intel Foundry Revenue = 101B / 120M ≈ 842:1', $cleanContent);
        
        $cleanContent = str_replace('TSMC Revenue approx 1000 times Intel Foundry Revenue', 
                                   'TSMC Revenue ≈ 1000 times Intel Foundry Revenue', $cleanContent);
        
        // Clean up multiple spaces and newlines
        $cleanContent = preg_replace('/\n\s*\n\s*\n/', "\n\n", $cleanContent);
        $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
        $cleanContent = str_replace(' \n', "\n", $cleanContent);
        
        echo "Original length: " . strlen($content) . " characters\n";
        echo "Cleaned length: " . strlen($cleanContent) . " characters\n";
        
        // Update the database
        $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = 1738");
        $updateStmt->execute([$cleanContent]);
        
        echo "Successfully cleaned TSMC vs Intel content!\n";
        echo "Removed all LaTeX formatting and dollar signs\n";
        
        // Show sample of cleaned content
        echo "\nSample of cleaned content:\n";
        echo substr($cleanContent, 0, 400) . "...\n";
        
    } else {
        echo "Entry ID 1738 not found\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>