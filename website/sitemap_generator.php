<?php
// Dynamic sitemap generator for chipfoundryservices.com
// Generates sitemap.xml from qa_responses table
header('Content-Type: application/xml; charset=utf-8');

$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
    exit;
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Main pages
$mainPages = [
    ['/', '1.0', 'daily'],
    ['/chat/', '0.9', 'daily'],
    ['/app/', '0.8', 'weekly'],
    ['/glossary.php', '0.8', 'weekly'],
    ['/blog.php', '0.8', 'weekly'],
];

foreach ($mainPages as $page) {
    echo "  <url>\n";
    echo "    <loc>https://www.chipfoundryservices.com" . $page[0] . "</loc>\n";
    echo "    <priority>" . $page[1] . "</priority>\n";
    echo "    <changefreq>" . $page[2] . "</changefreq>\n";
    echo "  </url>\n";
}

// Q&A topic pages — top entries by response length (best content)
$stmt = $pdo->query("
    SELECT id, keywords, LENGTH(response) as len 
    FROM qa_responses 
    WHERE LENGTH(response) >= 2000 AND id < 100000
    ORDER BY LENGTH(response) DESC 
    LIMIT 2000
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $firstKeyword = trim(explode(',', $row['keywords'])[0]);
    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($firstKeyword));
    $slug = trim($slug, '-');
    if (empty($slug) || strlen($slug) < 3) continue;
    
    echo "  <url>\n";
    echo "    <loc>https://www.chipfoundryservices.com/topic/" . htmlspecialchars($slug) . "</loc>\n";
    echo "    <priority>0.7</priority>\n";
    echo "    <changefreq>monthly</changefreq>\n";
    echo "  </url>\n";
}

echo '</urlset>' . "\n";
?>
