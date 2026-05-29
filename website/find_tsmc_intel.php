<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Find TSMC vs Intel entries
    $stmt = $pdo->query("SELECT id, keywords, LENGTH(response) as length FROM qa_responses WHERE keywords LIKE '%tsmc%' AND keywords LIKE '%intel%'");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($rows) . " TSMC vs Intel entries:\n\n";
    foreach ($rows as $row) {
        echo "ID: " . $row['id'] . "\n";
        echo "Keywords: " . $row['keywords'] . "\n";
        echo "Length: " . $row['length'] . " characters\n";
        echo "---\n";
    }
    
    // Test which one matches "tsmc vs intel"
    $question = "tsmc vs intel";
    $searchWords = preg_split('/\s+/', $question);
    $searchWords = array_map(function($word) {
        return preg_replace('/[^\w]/', '', $word);
    }, $searchWords);
    $skipWords = ['what', 'how', 'why', 'when', 'where', 'who', 'which', 'the', 'and', 'for', 'are', 'does', 'can', 'you', 'explain', 'tell', 'about', 'describe', 'is', 'a', 'an', 'to', 'of', 'in', 'on', 'at', 'by'];
    
    $bestMatch = null;
    $bestScore = 0;
    $bestId = null;
    
    foreach ($rows as $row) {
        $keywords = strtolower($row['keywords']);
        $score = 0;
        $keywordArray = explode(',', $keywords);
        
        foreach ($searchWords as $word) {
            $word = trim($word);
            if (strlen($word) < 3) continue;
            if (in_array($word, $skipWords)) continue;
            
            foreach ($keywordArray as $keyword) {
                $keyword = trim($keyword);
                if ($keyword === $word) {
                    $score += 10;
                } elseif (strpos($keyword, $word) !== false) {
                    $score += 5;
                }
            }
            
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/', $keywords)) {
                $score += 3;
            }
        }
        
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestId = $row['id'];
        }
    }
    
    echo "\nBest match for 'tsmc vs intel': ID $bestId with score $bestScore\n";
    
    // Show sample of current content
    if ($bestId) {
        $stmt = $pdo->prepare("SELECT response FROM qa_responses WHERE id = ?");
        $stmt->execute([$bestId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo "\nCurrent content sample:\n";
            echo substr($row['response'], 0, 300) . "...\n";
        }
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>