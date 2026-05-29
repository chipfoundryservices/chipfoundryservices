<?php
// SEO-friendly topic page for chipfoundryservices.com
// Renders a single Q&A entry as a full HTML page for search engines
$host = 'localhost';
$db = 'chipfoundry';
$user = 'root';
$pass = 'fOm7eS:DyRW0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo 'Database error';
    exit;
}

// Get the slug from URL
$requestUri = $_SERVER['REQUEST_URI'];
$slug = '';
if (preg_match('#/topic/([a-z0-9-]+)#', $requestUri, $matches)) {
    $slug = $matches[1];
}

if (empty($slug)) {
    http_response_code(404);
    echo 'Topic not found';
    exit;
}

// Convert slug back to search term
$searchTerm = str_replace('-', ' ', $slug);

// Search for matching entry
$sqlPattern = '%' . $searchTerm . '%';
$stmt = $pdo->prepare("
    SELECT id, keywords, response 
    FROM qa_responses 
    WHERE keywords LIKE ? 
     
    LIMIT 1
");
$stmt->execute([$sqlPattern]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    // Try broader match
    $stmt = $pdo->prepare("
        SELECT id, keywords, response 
        FROM qa_responses 
        WHERE LOWER(keywords) LIKE ? 
         
        LIMIT 1
    ");
    $stmt->execute(['%' . $searchTerm . '%']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$row) {
    http_response_code(404);
    echo 'Topic not found';
    exit;
}

// Format the response as HTML
$response = $row['response'];
$keywords = $row['keywords'];

// Extract title from first bold text
$title = $searchTerm;
if (preg_match('/\*\*(.+?)\*\*/', $response, $m)) {
    $title = strip_tags($m[1]);
}

// Convert markdown to HTML
$html = htmlspecialchars($response, ENT_QUOTES, 'UTF-8');
$html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
$html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
$html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html);
$html = str_replace("\n\n", '</p><p>', $html);
$html = str_replace("\n", '<br>', $html);
$html = '<p>' . $html . '</p>';

// Clean description for meta tag
$description = strip_tags(substr($response, 0, 300));
$description = preg_replace('/\s+/', ' ', $description);
$description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

$keywordsMeta = htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8');
$titleClean = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titleClean ?> — ChipFoundryServices</title>
    <meta name="description" content="<?= $description ?>">
    <meta name="keywords" content="<?= $keywordsMeta ?>">
    <meta property="og:title" content="<?= $titleClean ?> — ChipFoundryServices">
    <meta property="og:description" content="<?= $description ?>">
    <meta property="og:url" content="https://www.chipfoundryservices.com/topic/<?= htmlspecialchars($slug) ?>">
    <meta property="og:type" content="article">
    <link rel="canonical" href="https://www.chipfoundryservices.com/topic/<?= htmlspecialchars($slug) ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0d0d0d; color: #d1d5db;
            line-height: 1.7; padding: 0;
        }
        .nav {
            background: #111; border-bottom: 1px solid #2d2d2d;
            padding: 12px 20px; display: flex; align-items: center;
            justify-content: space-between; gap: 16px;
        }
        .nav a { color: #8e8ea0; text-decoration: none; font-size: 14px; }
        .nav a:hover { color: #ececec; }
        .nav-brand { color: #10a37f; font-weight: 700; font-size: 16px; text-decoration: none; }
        .container {
            max-width: 800px; margin: 0 auto; padding: 40px 24px 80px;
        }
        h1 { color: #ececec; font-size: 28px; margin-bottom: 24px; line-height: 1.3; }
        .content { font-size: 16px; }
        .content p { margin-bottom: 16px; }
        .content strong { color: #ececec; }
        .content code {
            background: #1a1a2e; color: #e2e8f0; padding: 2px 6px;
            border-radius: 4px; font-family: 'SF Mono', monospace; font-size: 14px;
        }
        .content table {
            width: 100%; border-collapse: collapse; margin: 16px 0;
            font-size: 14px;
        }
        .content th, .content td {
            border: 1px solid #333; padding: 8px 12px; text-align: left;
        }
        .content th { background: #1a1a2e; color: #ececec; }
        .meta { color: #666; font-size: 13px; margin-bottom: 24px; }
        .cta {
            margin-top: 40px; padding: 24px; background: #111;
            border: 1px solid #2d2d2d; border-radius: 12px; text-align: center;
        }
        .cta h3 { color: #ececec; margin-bottom: 8px; }
        .cta p { color: #8e8ea0; font-size: 14px; margin-bottom: 16px; }
        .cta a {
            display: inline-block; background: #10a37f; color: #fff;
            padding: 10px 24px; border-radius: 8px; text-decoration: none;
            font-weight: 600; margin: 0 8px;
        }
        .cta a:hover { background: #0e8c6b; }
        .cta a.secondary { background: #333; }
        .cta a.secondary:hover { background: #444; }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="/" class="nav-brand">ChipFoundryServices</a>
        <div style="display:flex;gap:16px;">
            <a href="/">🔍 Search</a>
            <a href="/chat/">💬 CFSGPT</a>
            <a href="/app/">📱 App</a>
        </div>
    </nav>
    <div class="container">
        <h1><?= $titleClean ?></h1>
        <div class="meta">Keywords: <?= $keywordsMeta ?></div>
        <div class="content"><?= $html ?></div>
        <div class="cta">
            <h3>Want to learn more?</h3>
            <p>Search 13,225+ semiconductor and AI topics or chat with our AI assistant.</p>
            <a href="/">Search Topics</a>
            <a href="/chat/" class="secondary">Chat with CFSGPT</a>
        </div>
    </div>
</body>
</html>
