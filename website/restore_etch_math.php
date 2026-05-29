<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read the original source file content
    $sourceFile = '/opt/bitnami/nginx/html/etch_profile_modeling.md';
    $originalContent = file_get_contents($sourceFile);
    
    if ($originalContent === false) {
        echo "Could not read source file\n";
        exit;
    }
    
    // Keep the original content with proper LaTeX formatting for grey background rendering
    // Only remove problematic formatting, keep $$ for math rendering
    $cleanContent = $originalContent;
    
    // Remove only the problematic \text{} commands but keep math delimiters
    $cleanContent = preg_replace('/\\\\text\{([^}]*)\}/', '$1', $cleanContent);
    
    // Update the database entry ID 10717
    $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = 10717");
    $updateStmt->execute([$cleanContent]);
    
    echo "Successfully restored etch profile modeling with proper math formatting!\n";
    echo "Content length: " . strlen($cleanContent) . " characters\n";
    echo "Kept LaTeX delimiters for proper grey background math rendering\n";
    
    // Show sample of math equations
    echo "\nSample math equations:\n";
    $lines = explode("\n", $cleanContent);
    foreach ($lines as $line) {
        if (strpos($line, '$$') !== false || strpos($line, '$') !== false) {
            echo $line . "\n";
            break;
        }
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>