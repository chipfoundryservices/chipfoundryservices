<?php
// glossary.php - SEO-friendly browsable glossary
header('Content-Type: text/html; charset=UTF-8');

$host = 'localhost';
$user = 'root';
$pass = 'fOm7eS:DyRW0';
$db = 'chipfoundry';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed");
}

$domain = isset($_GET['domain']) ? $_GET['domain'] : '';
$letter = isset($_GET['letter']) ? strtoupper($_GET['letter']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Build query
$where = [];
$params = [];
$types = '';

if ($domain === 'semiconductor') {
    $where[] = "(keywords LIKE '%semiconductor%' OR keywords LIKE '%lithography%' OR keywords LIKE '%wafer%' OR keywords LIKE '%chip%' OR keywords LIKE '%fabrication%' OR keywords LIKE '%etching%' OR keywords LIKE '%deposition%' OR keywords LIKE '%metrology%' OR keywords LIKE '%packaging%')";
} elseif ($domain === 'ai') {
    $where[] = "(keywords LIKE '%ai%' OR keywords LIKE '%machine learning%' OR keywords LIKE '%neural%' OR keywords LIKE '%transformer%' OR keywords LIKE '%llm%' OR keywords LIKE '%training%' OR keywords LIKE '%model%' OR keywords LIKE '%diffusion%')";
}

if ($letter && strlen($letter) === 1) {
    $where[] = "keywords LIKE ?";
    $params[] = strtolower($letter) . '%';
    $types .= 's';
}

$whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$countSql = "SELECT COUNT(*) as total FROM qa_responses $whereClause";
$countStmt = $conn->prepare($countSql);
if ($types) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $perPage);

// Get entries
$sql = "SELECT keywords, response FROM qa_responses $whereClause ORDER BY keywords LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$allParams = array_merge($params, [$offset, $perPage]);
$allTypes = $types . 'ii';
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$result = $stmt->get_result();

$domainTitle = $domain ? ucfirst($domain) : 'All Topics';
$letterTitle = $letter ? " - Letter $letter" : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($domainTitle) ?> Glossary<?= $letterTitle ?> | AI Factory - Chip Foundry Services</title>
    <meta name="description" content="Browse <?= number_format($totalRows) ?> <?= htmlspecialchars(strtolower($domainTitle)) ?> terms and definitions. Free technical glossary for semiconductor manufacturing and AI.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://www.chipfoundryservices.com/glossary.php<?= $domain ? "?domain=$domain" : '' ?><?= $letter ? ($domain ? "&letter=$letter" : "?letter=$letter") : '' ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0a0a0a; color: #fff; min-height: 100vh; padding: 2rem; }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        h1 a { color: #10a37f; text-decoration: none; }
        .subtitle { color: #666; margin-bottom: 2rem; }
        .filters { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem; }
        .filter-group { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .filter { padding: 0.5rem 1rem; background: #1a1a1a; border: 1px solid #333; border-radius: 20px; color: #888; text-decoration: none; font-size: 0.875rem; transition: all 0.2s; }
        .filter:hover, .filter.active { border-color: #10a37f; color: #fff; }
        .letters { display: flex; gap: 0.25rem; flex-wrap: wrap; }
        .letter { padding: 0.5rem 0.75rem; background: #1a1a1a; border: 1px solid #333; border-radius: 8px; color: #666; text-decoration: none; font-size: 0.8rem; transition: all 0.2s; }
        .letter:hover, .letter.active { border-color: #10a37f; color: #fff; background: #10a37f22; }
        .entries { display: flex; flex-direction: column; gap: 1rem; }
        .entry { padding: 1.5rem; background: #151515; border-radius: 12px; border: 1px solid #222; }
        .entry h3 { color: #10a37f; font-size: 1.1rem; margin-bottom: 0.5rem; text-transform: capitalize; }
        .entry p { color: #d1d5db; line-height: 1.6; }
        .pagination { display: flex; gap: 0.5rem; justify-content: center; margin-top: 2rem; flex-wrap: wrap; }
        .page-link { padding: 0.5rem 1rem; background: #1a1a1a; border: 1px solid #333; border-radius: 8px; color: #888; text-decoration: none; }
        .page-link:hover, .page-link.active { border-color: #10a37f; color: #fff; }
        .stats { text-align: center; color: #666; margin-bottom: 2rem; }
        .back { display: inline-block; margin-bottom: 1rem; color: #10a37f; text-decoration: none; }
        .back:hover { text-decoration: underline; }
        footer { margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #222; text-align: center; color: #444; }
        footer a { color: #10a37f; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back">← Back to AI Factory Chat</a>
        <h1><a href="/">AI Factory</a> Glossary</h1>
        <p class="subtitle"><?= number_format($totalRows) ?> technical terms and definitions</p>
        
        <div class="filters">
            <div class="filter-group">
                <a href="/glossary.php" class="filter <?= !$domain ? 'active' : '' ?>">All Topics</a>
                <a href="/glossary.php?domain=semiconductor" class="filter <?= $domain === 'semiconductor' ? 'active' : '' ?>">Semiconductor</a>
                <a href="/glossary.php?domain=ai" class="filter <?= $domain === 'ai' ? 'active' : '' ?>">Generative AI</a>
            </div>
        </div>
        
        <div class="letters">
            <?php foreach (range('A', 'Z') as $l): ?>
                <a href="/glossary.php?<?= $domain ? "domain=$domain&" : '' ?>letter=<?= $l ?>" class="letter <?= $letter === $l ? 'active' : '' ?>"><?= $l ?></a>
            <?php endforeach; ?>
            <a href="/glossary.php<?= $domain ? "?domain=$domain" : '' ?>" class="letter <?= !$letter ? 'active' : '' ?>">All</a>
        </div>
        
        <div class="stats">
            Showing page <?= $page ?> of <?= $totalPages ?> (<?= number_format($totalRows) ?> entries)
        </div>
        
        <div class="entries">
            <?php while ($row = $result->fetch_assoc()): ?>
                <article class="entry">
                    <h3><?= htmlspecialchars($row['keywords']) ?></h3>
                    <p><?= $row['response'] ?></p>
                </article>
            <?php endwhile; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="/glossary.php?<?= $domain ? "domain=$domain&" : '' ?><?= $letter ? "letter=$letter&" : '' ?>page=<?= $page - 1 ?>" class="page-link">← Prev</a>
            <?php endif; ?>
            
            <?php 
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++): 
            ?>
                <a href="/glossary.php?<?= $domain ? "domain=$domain&" : '' ?><?= $letter ? "letter=$letter&" : '' ?>page=<?= $i ?>" class="page-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="/glossary.php?<?= $domain ? "domain=$domain&" : '' ?><?= $letter ? "letter=$letter&" : '' ?>page=<?= $page + 1 ?>" class="page-link">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <footer>
            <p>© 2025 <a href="/">Chip Foundry Services</a>. Free AI-powered knowledge base.</p>
            <p>10,000+ definitions covering semiconductor manufacturing and generative AI.</p>
        </footer>
    </div>
</body>
</html>
<?php $conn->close(); ?>
