<?php
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Find entries with "how to make a chip" keywords
    $stmt = $pdo->query("SELECT id, keywords, LENGTH(response) as response_length FROM qa_responses WHERE keywords LIKE '%how to make a chip%' OR keywords LIKE '%chip%' AND keywords LIKE '%make%'");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($rows) . " entries:\n\n";
    foreach ($rows as $row) {
        echo "ID: " . $row['id'] . "\n";
        echo "Keywords: " . $row['keywords'] . "\n";
        echo "Response length: " . $row['response_length'] . " characters\n";
        echo "---\n";
    }
    
    // Get the specific entry that should contain the full content
    $stmt = $pdo->prepare("SELECT response FROM qa_responses WHERE keywords LIKE '%how to make a chip%' ORDER BY LENGTH(response) DESC LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $lines = explode("\n", $row['response']);
        echo "\nLongest entry has " . count($lines) . " lines\n";
        echo "First 10 lines:\n";
        for ($i = 0; $i < min(10, count($lines)); $i++) {
            echo ($i + 1) . ": " . substr($lines[$i], 0, 80) . "\n";
        }
        
        echo "\nLast 10 lines:\n";
        for ($i = max(0, count($lines) - 10); $i < count($lines); $i++) {
            echo ($i + 1) . ": " . substr($lines[$i], 0, 80) . "\n";
        }
    }
    
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>