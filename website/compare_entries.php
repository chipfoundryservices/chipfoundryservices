<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get both entries
    $stmt = $pdo->prepare("SELECT id, keywords, LENGTH(response) as length FROM qa_responses WHERE id IN (5, 1737)");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rows as $row) {
        echo "ID: " . $row['id'] . "\n";
        echo "Keywords: " . $row['keywords'] . "\n";
        echo "Length: " . $row['length'] . " characters\n";
        
        // Calculate score for "how to make a chip"
        $question = "how to make a chip";
        $searchWords = ['how', 'to', 'make', 'a', 'chip'];
        $searchWords = array_map(function($word) {
            return preg_replace('/[^\w]/', '', $word);
        }, $searchWords);
        $skipWords = ['what', 'how', 'why', 'when', 'where', 'who', 'which', 'the', 'and', 'for', 'are', 'does', 'can', 'you', 'explain', 'tell', 'about', 'describe', 'is', 'a', 'an', 'to', 'of', 'in', 'on', 'at', 'by'];
        
        $keywords = strtolower($row['keywords']);
        $score = 0;
        $keywordArray = explode(',', $keywords);
        
        echo "Keyword array: " . implode(' | ', $keywordArray) . "\n";
        
        foreach ($searchWords as $word) {
            $word = trim($word);
            if (strlen($word) < 3) continue;
            if (in_array($word, $skipWords)) continue;
            
            echo "Checking word: '$word'\n";
            
            foreach ($keywordArray as $keyword) {
                $keyword = trim($keyword);
                if ($keyword === $word) {
                    $score += 10;
                    echo "  Exact match with '$keyword': +10\n";
                } elseif (strpos($keyword, $word) !== false) {
                    $score += 5;
                    echo "  Partial match with '$keyword': +5\n";
                }
            }
            
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/', $keywords)) {
                $score += 3;
                echo "  Word boundary match: +3\n";
            }
        }
        
        // Multi-word phrase bonus
        $cleanQuestion = preg_replace('/\b(' . implode('|', $skipWords) . ')\b/', '', $question);
        $cleanQuestion = trim(preg_replace('/\s+/', ' ', $cleanQuestion));
        
        echo "Clean question: '$cleanQuestion'\n";
        
        if (strlen($cleanQuestion) > 5 && strpos($keywords, $cleanQuestion) !== false) {
            $score += 15;
            echo "  Phrase match bonus: +15\n";
        }
        
        echo "Total score: $score\n";
        echo "---\n\n";
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>