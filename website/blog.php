<?php
// blog.php - SEO-optimized blog listing page
header('Content-Type: text/html; charset=UTF-8');

$articles = [
    [
        'slug' => 'what-is-euv-lithography',
        'title' => 'What is EUV Lithography? Complete Guide 2025',
        'description' => 'Learn how Extreme Ultraviolet (EUV) lithography works, why ASML is the only manufacturer, and how it enables 3nm and 2nm chips at TSMC, Intel, and Samsung.',
        'category' => 'Semiconductor',
        'date' => 'December 10, 2025',
        'readTime' => '8 min read'
    ],
    [
        'slug' => 'how-to-train-llm',
        'title' => 'How to Train an LLM: Step-by-Step Guide 2025',
        'description' => 'Complete guide to training Large Language Models from scratch. Covers pre-training, fine-tuning, RLHF, data preparation, and infrastructure requirements.',
        'category' => 'Generative AI',
        'date' => 'December 10, 2025',
        'readTime' => '12 min read'
    ],
    [
        'slug' => 'tsmc-vs-intel-vs-samsung',
        'title' => 'TSMC vs Intel vs Samsung: Foundry Comparison 2025',
        'description' => 'Compare the big 3 semiconductor foundries: process nodes, capacity, customers, technology roadmaps, and market share analysis.',
        'category' => 'Semiconductor',
        'date' => 'December 10, 2025',
        'readTime' => '10 min read'
    ],
    [
        'slug' => 'transformer-architecture-explained',
        'title' => 'Transformer Architecture Explained: Attention Is All You Need',
        'description' => 'Deep dive into the Transformer architecture powering GPT, BERT, and all modern LLMs. Understand self-attention, positional encoding, and multi-head attention.',
        'category' => 'Generative AI',
        'date' => 'December 10, 2025',
        'readTime' => '15 min read'
    ],
    [
        'slug' => 'semiconductor-fabrication-process',
        'title' => 'Semiconductor Fabrication: From Sand to Chip',
        'description' => 'Complete overview of the chip manufacturing process: wafer preparation, photolithography, etching, deposition, doping, and packaging.',
        'category' => 'Semiconductor',
        'date' => 'December 10, 2025',
        'readTime' => '10 min read'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Semiconductor & AI Insights | Chip Foundry Services</title>
    <meta name="description" content="Expert articles on semiconductor manufacturing, chip design, LLM training, and generative AI. Learn from industry professionals.">
    <meta name="keywords" content="semiconductor blog, AI articles, EUV lithography, LLM training, TSMC, Intel, chip manufacturing">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://www.chipfoundryservices.com/blog.php">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Blog - Semiconductor & AI Insights">
    <meta property="og:description" content="Expert articles on semiconductor manufacturing and generative AI.">
    <meta property="og:url" content="https://www.chipfoundryservices.com/blog.php">
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-XXXXXXXXXX');
    </script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0a0a0a; color: #fff; min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; padding: 2rem; }
        
        /* Header */
        header { text-align: center; margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid #222; }
        .logo { color: #10a37f; text-decoration: none; font-size: 1.5rem; font-weight: 600; }
        h1 { font-size: 2.5rem; margin: 1rem 0 0.5rem; }
        .subtitle { color: #888; font-size: 1.1rem; }
        
        /* Nav */
        nav { display: flex; gap: 1.5rem; justify-content: center; margin-top: 1.5rem; }
        nav a { color: #888; text-decoration: none; padding: 0.5rem 1rem; border-radius: 20px; transition: all 0.2s; }
        nav a:hover, nav a.active { color: #fff; background: #1a1a1a; }
        
        /* Articles Grid */
        .articles { display: flex; flex-direction: column; gap: 1.5rem; }
        
        .article-card { background: #111; border: 1px solid #222; border-radius: 12px; padding: 1.5rem; transition: all 0.2s; }
        .article-card:hover { border-color: #10a37f; transform: translateY(-2px); }
        .article-card a { text-decoration: none; color: inherit; }
        
        .article-meta { display: flex; gap: 1rem; margin-bottom: 0.75rem; font-size: 0.85rem; }
        .category { color: #10a37f; font-weight: 500; }
        .date, .read-time { color: #666; }
        
        .article-title { font-size: 1.4rem; color: #fff; margin-bottom: 0.75rem; line-height: 1.3; }
        .article-title:hover { color: #10a37f; }
        
        .article-excerpt { color: #999; line-height: 1.6; }
        
        .read-more { display: inline-block; margin-top: 1rem; color: #10a37f; font-weight: 500; }
        .read-more:hover { text-decoration: underline; }
        
        /* Footer */
        footer { margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #222; text-align: center; color: #666; }
        footer a { color: #10a37f; text-decoration: none; }
        
        /* Responsive */
        @media (max-width: 600px) {
            h1 { font-size: 1.8rem; }
            .article-title { font-size: 1.2rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <a href="/" class="logo">AI Factory</a>
            <h1>Blog</h1>
            <p class="subtitle">Expert insights on semiconductor manufacturing & generative AI</p>
            <nav>
                <a href="/">Chat</a>
                <a href="/glossary.php">Glossary</a>
                <a href="/blog.php" class="active">Blog</a>
            </nav>
        </header>
        
        <main class="articles">
            <?php foreach ($articles as $article): ?>
            <article class="article-card">
                <a href="/blog/<?= $article['slug'] ?>.php">
                    <div class="article-meta">
                        <span class="category"><?= $article['category'] ?></span>
                        <span class="date"><?= $article['date'] ?></span>
                        <span class="read-time"><?= $article['readTime'] ?></span>
                    </div>
                    <h2 class="article-title"><?= $article['title'] ?></h2>
                    <p class="article-excerpt"><?= $article['description'] ?></p>
                    <span class="read-more">Read more →</span>
                </a>
            </article>
            <?php endforeach; ?>
        </main>
        
        <footer>
            <p>© 2025 <a href="/">Chip Foundry Services</a></p>
            <p style="margin-top: 0.5rem; font-size: 0.85rem;">
                <a href="/">AI Chat</a> · <a href="/glossary.php">Glossary (10K+ terms)</a> · <a href="/blog.php">Blog</a>
            </p>
        </footer>
    </div>
</body>
</html>
