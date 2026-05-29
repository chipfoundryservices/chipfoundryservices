<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache');
ini_set('max_execution_time', '5');

$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'DB failed']);
    exit;
}

$q = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';
$q = preg_replace('/[?!.,;:]/', '', $q);
$q = trim($q);

if (empty($q)) {
    echo json_encode(['response' => 'Please ask a question.']);
    exit;
}

// Strip stop words
$stopWords = ['what','is','are','how','does','do','can','why','when','where','who','which','the','a','an','to','for','about','explain','tell','me','you','work','works','working','it','this','that','these','those','of','in','on','at','by','with'];
$words = explode(' ', $q);
$cleanWords = array_filter($words, function($w) use ($stopWords) {
    return !in_array(trim($w), $stopWords) && strlen(trim($w)) > 0;
});
$searchQuery = !empty($cleanWords) ? implode(' ', $cleanWords) : $q;

// Step 1: Search only id and keywords
// First try prefix match (uses index, fast)
$stmt = $pdo->prepare("SELECT id, keywords FROM qa_responses WHERE keywords LIKE ? LIMIT 50");
$stmt->execute([$searchQuery . '%']);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no prefix match, try contains match (slower but comprehensive)
if (empty($rows) && strlen($searchQuery) >= 3) {
    $stmt = $pdo->prepare("SELECT id, keywords FROM qa_responses WHERE keywords LIKE ? LIMIT 30");
    $stmt->execute(['%' . $searchQuery . '%']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (empty($rows)) {
    $stmt2 = $pdo->prepare("SELECT response FROM qa_responses WHERE id = 99999 LIMIT 1");
    $stmt2->execute();
    $fallback = $stmt2->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['response' => $fallback ? $fallback['response'] : 'No results found.']);
    exit;
}

// Step 2: Score matches in PHP
$bestId = null;
$bestScore = 0;

foreach ($rows as $row) {
    $keywords = strtolower($row['keywords']);
    $score = 0;
    
    $kwArray = explode(',', $keywords);
    foreach ($kwArray as $kw) {
        $kw = trim($kw);
        if ($kw === $searchQuery || $kw === $q) { $score = 1000; break; }
    }
    
    if ($score < 1000) {
        foreach ($kwArray as $kw) {
            $kw = trim($kw);
            if (strpos($kw, $searchQuery) === 0 && ($kw === $searchQuery || (strlen($kw) > strlen($searchQuery) && $kw[strlen($searchQuery)] === ' '))) {
                $score = max($score, 50);
            }
        }
    }
    
    if ($score < 50 && strlen($searchQuery) >= 4) {
        foreach ($kwArray as $kw) {
            if (strpos(trim($kw), $searchQuery) !== false) {
                $score = max($score, 20);
            }
        }
    }
    
    if ($score > 0 && $score < 1000) {
        $score += (10 - min(10, count($kwArray)));
    }
    
    if ($score > $bestScore) {
        $bestScore = $score;
        $bestId = $row['id'];
    }
}

// Step 3: Fetch only the winning response by primary key (instant)
if ($bestId) {
    $stmt3 = $pdo->prepare("SELECT response FROM qa_responses WHERE id = ? LIMIT 1");
    $stmt3->execute([$bestId]);
    $result = $stmt3->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        echo json_encode(['response' => $result['response']]);
        exit;
    }
}

$stmt2 = $pdo->prepare("SELECT response FROM qa_responses WHERE id = 99999 LIMIT 1");
$stmt2->execute();
$fallback = $stmt2->fetch(PDO::FETCH_ASSOC);
echo json_encode(['response' => $fallback ? $fallback['response'] : 'No results found.']);
?>
