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
    $totalDollarsRemoved = 0;
    $totalBracketsRemoved = 0;
    
    foreach ($rows as $row) {
        $id = $row['id'];
        $content = $row['response'];
        $originalLength = strlen($content);
        
        // Count problematic characters before cleaning
        $dollarCount = substr_count($content, '$');
        $leftBracketCount = substr_count($content, '{');
        $rightBracketCount = substr_count($content, '}');
        $backslashCount = substr_count($content, '\\');
        
        if ($dollarCount > 0 || $leftBracketCount > 0 || $rightBracketCount > 0 || $backslashCount > 0) {
            // AGGRESSIVE CLEANING - Remove ALL problematic characters
            $cleanContent = $content;
            
            // Remove ALL dollar signs
            $cleanContent = str_replace('$', '', $cleanContent);
            
            // Remove ALL left brackets
            $cleanContent = str_replace('{', '', $cleanContent);
            
            // Remove ALL right brackets
            $cleanContent = str_replace('}', '', $cleanContent);
            
            // Remove ALL backslashes
            $cleanContent = str_replace('\\', '', $cleanContent);
            
            // Remove LaTeX commands
            $cleanContent = preg_replace('/\\\\[a-zA-Z]+/', '', $cleanContent);
            
            // Replace mathematical symbols with clean text
            $cleanContent = str_replace(['×', '≈', '→', '↑', '⁻¹', '₀', '₁', '₂', '₃', '₄', '₅', '₆', '₇', '₈', '₉'], 
                                       ['x', 'approx', '->', 'up', '^-1', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], $cleanContent);
            
            // Clean up multiple spaces and newlines
            $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
            $cleanContent = str_replace(' \n', "\n", $cleanContent);
            
            // Update the database entry
            $updateStmt = $pdo->prepare("UPDATE qa_responses SET response = ? WHERE id = ?");
            $updateStmt->execute([$cleanContent, $id]);
            
            $newLength = strlen($cleanContent);
            
            echo "ID $id: Cleaned entry\n";
            echo "  Keywords: " . substr($row['keywords'], 0, 60) . "...\n";
            echo "  Removed: $dollarCount dollars, $leftBracketCount left brackets, $rightBracketCount right brackets, $backslashCount backslashes\n";
            echo "  Size: $originalLength → $newLength chars\n\n";
            
            $totalDollarsRemoved += $dollarCount;
            $totalBracketsRemoved += ($leftBracketCount + $rightBracketCount);
            $entriesModified++;
        }
    }
    
    echo "=== FINAL SUMMARY ===\n";
    echo "Total entries processed: " . count($rows) . "\n";
    echo "Entries modified: $entriesModified\n";
    echo "Total dollar signs removed: $totalDollarsRemoved\n";
    echo "Total brackets removed: $totalBracketsRemoved\n";
    echo "Database cleanup completed!\n";
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>