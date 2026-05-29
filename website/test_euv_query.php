<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Find EUV related entries
    $stmt = $pdo->query("SELECT id, keywords FROM qa_responses WHERE keywords LIKE '%euv%' OR keywords LIKE '%ultraviolet%' OR keywords LIKE '%lithography%'");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "EUV related entries:\n";
    foreach ($rows as $row) {
        echo "ID: " . $row['id'] . " - Keywords: " . $row['keywords'] . "\n";
    }
    
    // Test the scoring for "What is EUV?"
    $question = "what is euv?";
    $searchWords = preg_split('/\s+/', $question);
    $skipWords = ['what', 'how', 'why', 'when', 'where', 'who', 'which', 'the', 'and', 'for', 'are', 'does', 'can', 'you', 'explain', 'tell', 'about', 'describe', 'is', 'a', 'an', 'to', 'of', 'in', 'on', 'at', 'by'];
    
    echo "\nTesting scoring for: '$question'\n";
    echo "Search words: " . implode(', ', $searchWords) . "\n";
    
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
        
        echo "ID " . $row['id'] . " score: $score\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>