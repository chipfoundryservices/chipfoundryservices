<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Simulate the API logic for "How to make a chip"
    $question = "how to make a chip";
    $searchWords = preg_split('/\s+/', $question);
    $searchWords = array_map(function($word) {
        return preg_replace('/[^\w]/', '', $word);
    }, $searchWords);
    $skipWords = ['what', 'how', 'why', 'when', 'where', 'who', 'which', 'the', 'and', 'for', 'are', 'does', 'can', 'you', 'explain', 'tell', 'about', 'describe', 'is', 'a', 'an', 'to', 'of', 'in', 'on', 'at', 'by'];
    
    // Get all responses
    $stmt = $pdo->query("SELECT keywords, response FROM qa_responses");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $bestMatch = null;
    $bestScore = 0;
    $bestId = null;
    
    foreach ($rows as $row) {
        $keywords = strtolower($row['keywords']);
        
        if ($row['keywords'] === 'default') {
            continue;
        }
        
        $score = 0;
        $keywordArray = explode(',', $keywords);
        
        // Calculate match score
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
        
        // Multi-word phrase bonus
        $cleanQuestion = preg_replace('/\b(' . implode('|', $skipWords) . ')\b/', '', $question);
        $cleanQuestion = trim(preg_replace('/\s+/', ' ', $cleanQuestion));
        
        if (strlen($cleanQuestion) > 5 && strpos($keywords, $cleanQuestion) !== false) {
            $score += 15;
        }
        
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestMatch = $row['response'];
            
            // Find the ID for debugging
            $idStmt = $pdo->prepare("SELECT id FROM qa_responses WHERE keywords = ? AND response = ?");
            $idStmt->execute([$row['keywords'], $row['response']]);
            $idRow = $idStmt->fetch(PDO::FETCH_ASSOC);
            if ($idRow) {
                $bestId = $idRow['id'];
            }
        }
    }
    
    echo "Best match ID: " . $bestId . "\n";
    echo "Best score: " . $bestScore . "\n";
    echo "Response length: " . strlen($bestMatch) . " characters\n";
    echo "Response lines: " . count(explode("\n", $bestMatch)) . " lines\n";
    
    // Test JSON encoding
    $jsonResponse = json_encode(['response' => $bestMatch], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo "JSON length: " . strlen($jsonResponse) . " characters\n";
    
    // Check if JSON encoding failed
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON Error: " . json_last_error_msg() . "\n";
    } else {
        echo "JSON encoding successful\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>