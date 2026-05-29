<?php
/**
 * topic.php — ChipFoundryServices Knowledge Base Article Renderer
 * Serves /topics/{slug} from markdown files + /topic/{slug} from DB (legacy)
 * PHP 8.2 | Dark cosmos theme | TOC | Full SEO
 */

$uri    = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$slug   = '';
$source = '';

if (preg_match('#^/topics/([a-z0-9][a-z0-9\-]{1,100})$#', $uri, $m)) {
    $slug = $m[1]; $source = 'markdown';
} elseif (preg_match('#^/topic/([a-z0-9][a-z0-9\-]{1,100})$#', $uri, $m)) {
    $slug = $m[1]; $source = 'db';
} else {
    http_response_code(404); echo '<h1>404</h1>'; exit;
}

$mdContent = '';
$title     = ucwords(str_replace('-', ' ', $slug));
$keywords  = '';

// ── Markdown file source ───────────────────────────────────────────────────────
if ($source === 'markdown') {
    $mdFile = __DIR__ . '/topics/' . $slug . '.md';
    if (!file_exists($mdFile)) { $source = 'db'; }
    else {
        $mdContent = file_get_contents($mdFile);
        if (preg_match('/^#\s+(.+)$/m', $mdContent, $m2)) $title = trim($m2[1]);
        if (preg_match('/^\*\*Keywords\*\*:\s*(.+)$/m', $mdContent, $m3)) $keywords = trim($m3[1]);
    }
}

// ── Legacy DB source ───────────────────────────────────────────────────────────
if ($source === 'db' && !$mdContent) {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=chipfoundry', 'root', 'fOm7eS:DyRW0');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $search = '%' . str_replace('-', ' ', $slug) . '%';
        $stmt = $pdo->prepare("SELECT keywords, response FROM qa_responses WHERE LOWER(keywords) LIKE LOWER(?) LIMIT 1");
        $stmt->execute([$search]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mdContent = $row['response'];
            $keywords  = $row['keywords'];
            if (preg_match('/\*\*(.+?)\*\*/', $mdContent, $m4)) $title = strip_tags($m4[1]);
        }
    } catch (Exception $e) {}
    if (!$mdContent) { http_response_code(404); echo '<h1>Topic not found</h1>'; exit; }
}

// ── Markdown parser ────────────────────────────────────────────────────────────
function md2html(string $md): array {
    $lines = explode("\n", $md);
    $html  = ''; $toc = []; $i = 0; $n = count($lines);

    $inline = function(string $s): string {
        $s = preg_replace('/`([^`]+)`/', '<code>$1</code>', $s);
        $s = preg_replace('/\*\*\*(.+?)\*\*\*/', '<strong><em>$1</em></strong>', $s);
        $s = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $s);
        $s = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/', '<em>$1</em>', $s);
        $s = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $s);
        return $s;
    };
    $sid = fn(string $t) => preg_replace('/[^a-z0-9]+/', '-', strtolower(strip_tags($t)));

    while ($i < $n) {
        $line = $lines[$i];
        if (trim($line) === '')       { $i++; continue; }
        if (preg_match('/^-{3,}$/', trim($line))) { $html .= '<hr>'; $i++; continue; }

        // Headings
        if (preg_match('/^(#{1,4})\s+(.+)$/', $line, $m)) {
            $lv = strlen($m[1]); $txt = $inline($m[2]); $id = $sid($m[2]);
            $html .= "<h$lv id=\"$id\">$txt</h$lv>";
            if ($lv >= 2 && $lv <= 3) $toc[] = ['id'=>$id,'text'=>strip_tags($txt),'level'=>$lv];
            $i++; continue;
        }

        // Fenced code block
        if (preg_match('/^```(\w*)$/', $line, $m)) {
            $lang = $m[1] ?: 'text'; $code = ''; $i++;
            while ($i < $n && !preg_match('/^```$/', $lines[$i])) {
                $code .= htmlspecialchars($lines[$i], ENT_QUOTES) . "\n"; $i++;
            }
            $html .= "<pre><code class=\"lang-$lang\">$code</code></pre>"; $i++; continue;
        }

        // Table
        if (str_starts_with(trim($line), '|')) {
            $html .= '<table>'; $first = true;
            while ($i < $n && str_starts_with(trim($lines[$i]), '|')) {
                $row = $lines[$i];
                if (preg_match('/^\|[\s\-\|:]+\|$/', $row)) { $i++; $first = false; continue; }
                $cells = array_slice(explode('|', $row), 1, -1);
                $tag = $first ? 'th' : 'td';
                $html .= '<tr>' . implode('', array_map(fn($c) => "<$tag>" . $inline(trim($c)) . "</$tag>", $cells)) . '</tr>';
                $first = false; $i++;
            }
            $html .= '</table>'; continue;
        }

        // List
        if (preg_match('/^(\s*)[-*]\s+(.+)$/', $line)) {
            $html .= '<ul>';
            while ($i < $n && preg_match('/^(\s*)[-*]\s+(.+)$/', $lines[$i], $lm)) {
                $depth = (int)(strlen($lm[1]) / 2);
                $html .= str_repeat('<ul>', $depth) . '<li>' . $inline(trim($lm[2])) . '</li>' . str_repeat('</ul>', $depth);
                $i++;
            }
            $html .= '</ul>'; continue;
        }

        // Blockquote
        if (str_starts_with($line, '> ')) {
            $html .= '<blockquote>';
            while ($i < $n && str_starts_with($lines[$i], '> ')) {
                $html .= '<p>' . $inline(substr($lines[$i], 2)) . '</p>'; $i++;
            }
            $html .= '</blockquote>'; continue;
        }

        // Paragraph
        $para = '';
        while ($i < $n && trim($lines[$i]) !== '' &&
               !preg_match('/^(#{1,4}\s|```|>\s|\||\s*[-*]\s|-{3,})/', $lines[$i])) {
            $para .= ($para ? ' ' : '') . trim($lines[$i]); $i++;
        }
        if ($para) $html .= '<p>' . $inline($para) . '</p>';
    }
    return ['html' => $html, 'toc' => $toc];
}

['html' => $bodyHtml, 'toc' => $toc] = md2html($mdContent);

// ── Meta ──────────────────────────────────────────────────────────────────────
$metaDesc  = substr(preg_replace('/\s+/',' ', preg_replace('/[#*`\[\]_>|]/','',$mdContent)), 0, 300);
$metaDesc  = htmlspecialchars(trim($metaDesc), ENT_QUOTES);
$titleEsc  = htmlspecialchars($title, ENT_QUOTES);
$kwEsc     = htmlspecialchars($keywords, ENT_QUOTES);
$canonical = 'https://www.chipfoundryservices.com/' . ($source === 'markdown' ? 'topics' : 'topic') . '/' . $slug;

// Related topics
$related = '';
$topicsDir = __DIR__ . '/topics/';
if (is_dir($topicsDir)) {
    $prefix = explode('-', $slug)[0];
    $files  = array_filter(glob($topicsDir . $prefix . '*.md') ?: [], fn($f) => basename($f,'.md') !== $slug);
    foreach (array_slice($files, 0, 6) as $f) {
        $s = basename($f, '.md');
        $related .= '<a href="/topics/' . $s . '">' . htmlspecialchars(ucwords(str_replace('-',' ',$s))) . '</a>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=$titleEsc?> | ChipFoundryServices</title>
<meta name="description" content="<?=$metaDesc?>">
<?php if($kwEsc):?><meta name="keywords" content="<?=$kwEsc?>"><?php endif;?>
<meta property="og:title"       content="<?=$titleEsc?> | ChipFoundryServices">
<meta property="og:description" content="<?=$metaDesc?>">
<meta property="og:url"         content="<?=$canonical?>">
<meta property="og:type"        content="article">
<meta name="twitter:card"       content="summary">
<link rel="canonical" href="<?=$canonical?>">
<link rel="icon" href="/favicon.ico">
<style>
:root{--bg:#0d0d0d;--bg2:#111;--bg3:#1a1a2e;--bd:#2a2a2a;--tx:#d1d5db;--tx2:#9ca3af;--hd:#ececec;--gr:#10a37f;--gr2:#0e8c6b;--cb:#161625;--ac:#60a5fa}
*{margin:0;padding:0;box-sizing:border-box}html{scroll-behavior:smooth}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:var(--bg);color:var(--tx);line-height:1.75;font-size:16px}
nav{background:var(--bg2);border-bottom:1px solid var(--bd);padding:12px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;gap:12px}
.brand{color:var(--gr);font-weight:700;font-size:17px;text-decoration:none;white-space:nowrap}
.nav-links{display:flex;gap:18px;flex-wrap:wrap}
.nav-links a{color:var(--tx2);text-decoration:none;font-size:14px;transition:color .2s}
.nav-links a:hover{color:var(--hd)}
.layout{display:grid;grid-template-columns:230px 1fr;max-width:1180px;margin:0 auto;padding:36px 24px;gap:44px}
@media(max-width:860px){.layout{grid-template-columns:1fr}.toc{display:none}}
.toc{position:sticky;top:68px;height:fit-content;max-height:calc(100vh - 90px);overflow-y:auto}
.toc h4{color:var(--tx2);font-size:11px;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;font-weight:600}
.toc a{display:block;color:var(--tx2);text-decoration:none;font-size:13px;padding:4px 0 4px 12px;border-left:2px solid var(--bd);transition:all .15s;line-height:1.35}
.toc a:hover,.toc a.active{color:var(--gr);border-color:var(--gr)}
.toc a.l3{padding-left:22px;font-size:12px}
article{min-width:0;padding-bottom:80px}
.bc{font-size:13px;color:var(--tx2);margin-bottom:24px}
.bc a{color:var(--tx2);text-decoration:none}.bc a:hover{color:var(--gr)}.bc span{margin:0 5px}
article h1{color:var(--hd);font-size:clamp(20px,4vw,30px);line-height:1.3;margin-bottom:16px;font-weight:700}
article h2{color:var(--hd);font-size:19px;margin:36px 0 13px;padding-bottom:8px;border-bottom:1px solid var(--bd);font-weight:600}
article h3{color:var(--hd);font-size:16px;margin:22px 0 9px;font-weight:600}
article h4{color:var(--tx);font-size:14px;margin:16px 0 7px;font-weight:600}
article p{margin-bottom:13px}
article strong{color:var(--hd);font-weight:600}
article em{color:#c4b5fd}
article a{color:var(--ac);text-decoration:none}article a:hover{text-decoration:underline}
article hr{border:none;border-top:1px solid var(--bd);margin:26px 0}
article ul,article ol{margin:10px 0 14px;padding:0;list-style:none}
article li{padding:3px 0 3px 18px;position:relative;font-size:15px}
article ul>li::before{content:'▸';position:absolute;left:0;color:var(--gr);font-size:11px;top:6px}
article ul ul{margin:3px 0 3px 14px}article ul ul li::before{content:'–';color:var(--tx2)}
code{background:var(--cb);color:#e2e8f0;padding:2px 6px;border-radius:4px;font-family:'SF Mono','Fira Code',monospace;font-size:13.5px}
pre{background:var(--cb);border:1px solid var(--bd);border-radius:8px;padding:18px;overflow-x:auto;margin:18px 0;font-size:13px;line-height:1.6}
pre code{background:none;padding:0}
blockquote{border-left:3px solid var(--gr);padding:10px 16px;margin:14px 0;background:var(--bg2);border-radius:0 8px 8px 0}
blockquote p{color:var(--tx2);font-style:italic;margin:0}
table{width:100%;border-collapse:collapse;margin:18px 0;font-size:14px;border-radius:8px;overflow:hidden;border:1px solid var(--bd)}
th{background:var(--bg3);color:var(--hd);padding:9px 13px;text-align:left;font-weight:600}
td{padding:8px 13px;border-top:1px solid var(--bd)}
tr:hover td{background:var(--bg2)}
.kw{display:flex;flex-wrap:wrap;gap:7px;margin:14px 0 26px}
.kw span{background:var(--bg3);color:var(--tx2);font-size:12px;padding:3px 10px;border-radius:20px;border:1px solid var(--bd)}
.rel{margin-top:44px;padding-top:22px;border-top:1px solid var(--bd)}
.rel h3{color:var(--hd);font-size:15px;margin-bottom:12px}
.rel-grid{display:flex;flex-wrap:wrap;gap:8px}
.rel-grid a{background:var(--bg2);border:1px solid var(--bd);color:var(--tx2);font-size:13px;padding:5px 13px;border-radius:6px;text-decoration:none;transition:all .2s}
.rel-grid a:hover{border-color:var(--gr);color:var(--gr)}
.cta{margin-top:40px;background:var(--bg2);border:1px solid var(--bd);border-radius:12px;padding:28px;text-align:center}
.cta h3{color:var(--hd);font-size:17px;margin-bottom:8px}
.cta p{color:var(--tx2);font-size:14px;margin-bottom:18px}
.btns{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.btn{display:inline-block;padding:9px 22px;border-radius:8px;font-weight:600;font-size:14px;text-decoration:none;transition:all .2s}
.btn-p{background:var(--gr);color:#fff}.btn-p:hover{background:var(--gr2)}
.btn-s{background:var(--bg3);color:var(--tx);border:1px solid var(--bd)}.btn-s:hover{border-color:var(--tx2)}
</style>
</head>
<body>
<nav>
  <a class="brand" href="/">⚡ ChipFoundryServices</a>
  <div class="nav-links">
    <a href="/">🔍 Search</a>
    <a href="/topics/">📚 Topics</a>
    <a href="/chat/">💬 CFSGPT</a>
    <a href="/app/">📱 App</a>
  </div>
</nav>
<div class="layout">
  <aside class="toc">
    <?php if($toc):?>
    <h4>Contents</h4>
    <?php foreach($toc as $t):?>
      <a href="#<?=$t['id']?>" class="<?=$t['level']===3?'l3':''?>"><?=htmlspecialchars($t['text'])?></a>
    <?php endforeach;?>
    <?php endif;?>
  </aside>
  <article>
    <div class="bc">
      <a href="/">Home</a><span>›</span>
      <a href="/topics/">Knowledge Base</a><span>›</span>
      <span><?=$titleEsc?></span>
    </div>
    <?=$bodyHtml?>
    <?php if($keywords):?>
    <div class="kw"><?php foreach(explode(',',$keywords) as $k){ $k=trim($k); if($k) echo '<span>'.htmlspecialchars($k).'</span>'; }?></div>
    <?php endif;?>
    <?php if($related):?>
    <div class="rel"><h3>Related Topics</h3><div class="rel-grid"><?=$related?></div></div>
    <?php endif;?>
    <div class="cta">
      <h3>Explore 500+ Semiconductor &amp; AI Topics</h3>
      <p>From EUV lithography to CUDA optimization — search the full knowledge base or chat with our AI assistant.</p>
      <div class="btns">
        <a class="btn btn-p" href="/">🔍 Search Topics</a>
        <a class="btn btn-s" href="/chat/">💬 Ask CFSGPT</a>
        <a class="btn btn-s" href="/topics/">📚 Browse All</a>
      </div>
    </div>
  </article>
</div>
<script>
// Highlight active TOC item on scroll
const obs = new IntersectionObserver(es=>{
  es.forEach(e=>{
    const a = document.querySelector('.toc a[href="#'+e.target.id+'"]');
    if(a) a.classList.toggle('active', e.isIntersecting);
  });
},{rootMargin:'-20% 0px -70% 0px'});
document.querySelectorAll('article h2,article h3').forEach(h=>obs.observe(h));
</script>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"TechArticle","headline":<?=json_encode($title)?>,"description":<?=json_encode(strip_tags($metaDesc))?>,"url":<?=json_encode($canonical)?>,"publisher":{"@type":"Organization","name":"ChipFoundryServices LLC","url":"https://www.chipfoundryservices.com"},"keywords":<?=json_encode($keywords)?>}
</script>
</body>
</html>
