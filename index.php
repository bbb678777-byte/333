<?php
require_once 'config.php';

// 搜索关键词
$keyword = trim($_GET['q'] ?? '');

// 查询分类及其下所有链接
$categories = $pdo->query('SELECT * FROM categories ORDER BY sort_order, id')->fetchAll(PDO::FETCH_ASSOC);

$linksByCategory = [];
if ($keyword !== '') {
    $stmt = $pdo->prepare('SELECT * FROM links WHERE title LIKE ? OR description LIKE ? ORDER BY sort_order, id');
    $stmt->execute(['%' . $keyword . '%', '%' . $keyword . '%']);
    $allLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($allLinks as $link) {
        $linksByCategory[$link['category_id']][] = $link;
    }
} else {
    $stmt = $pdo->query('SELECT * FROM links ORDER BY sort_order, id');
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $link) {
        $linksByCategory[$link['category_id']][] = $link;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars(SITE_NAME) ?></title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #f0f2f5;
    --card-bg: #ffffff;
    --primary: #4f6ef7;
    --primary-dark: #3a57e8;
    --text: #1a1a2e;
    --muted: #6b7280;
    --border: #e5e7eb;
    --shadow: 0 2px 8px rgba(0,0,0,.08);
    --radius: 12px;
  }

  body {
    background: var(--bg);
    color: var(--text);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'PingFang SC', 'Microsoft YaHei', sans-serif;
    min-height: 100vh;
  }

  /* ── Header ── */
  header {
    background: linear-gradient(135deg, #4f6ef7 0%, #6a4fdb 100%);
    color: #fff;
    padding: 40px 20px 80px;
    text-align: center;
  }
  header h1 { font-size: 2rem; letter-spacing: .05em; margin-bottom: 8px; }
  header p  { opacity: .85; font-size: .95rem; }

  /* ── Search ── */
  .search-wrap {
    max-width: 560px;
    margin: -30px auto 0;
    padding: 0 16px;
    position: relative;
    z-index: 10;
  }
  .search-box {
    display: flex;
    background: #fff;
    border-radius: 50px;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
    overflow: hidden;
  }
  .search-box input {
    flex: 1;
    border: none;
    outline: none;
    padding: 14px 20px;
    font-size: 1rem;
    color: var(--text);
    background: transparent;
  }
  .search-box button {
    background: var(--primary);
    color: #fff;
    border: none;
    padding: 0 24px;
    cursor: pointer;
    font-size: 1.1rem;
    transition: background .2s;
  }
  .search-box button:hover { background: var(--primary-dark); }

  /* ── Main ── */
  main {
    max-width: 1100px;
    margin: 40px auto;
    padding: 0 16px 60px;
  }

  /* ── Section ── */
  .section { margin-bottom: 36px; }
  .section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text);
  }
  .section-title span.line {
    flex: 1;
    height: 1px;
    background: var(--border);
  }

  /* ── Grid ── */
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 14px;
  }

  /* ── Card ── */
  .card {
    background: var(--card-bg);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 18px 16px;
    text-decoration: none;
    color: var(--text);
    transition: transform .2s, box-shadow .2s;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    border: 1px solid var(--border);
  }
  .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
  }
  .card .icon {
    font-size: 1.6rem;
    line-height: 1;
    flex-shrink: 0;
  }
  .card .info { overflow: hidden; }
  .card .info strong {
    display: block;
    font-size: .95rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .card .info small {
    display: block;
    color: var(--muted);
    font-size: .78rem;
    margin-top: 4px;
    line-height: 1.4;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }

  /* ── Empty ── */
  .empty { text-align: center; color: var(--muted); padding: 60px 0; font-size: 1rem; }

  /* ── Footer ── */
  footer {
    text-align: center;
    color: var(--muted);
    font-size: .82rem;
    padding: 20px;
  }

  @media (max-width: 480px) {
    header h1 { font-size: 1.5rem; }
    .grid { grid-template-columns: repeat(auto-fill, minmax(155px, 1fr)); }
  }
</style>
</head>
<body>

<header>
  <h1>🌐 <?= htmlspecialchars(SITE_NAME) ?></h1>
  <p>收录精选网站，快速直达</p>
</header>

<div class="search-wrap">
  <form class="search-box" method="get" action="index.php">
    <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>"
           placeholder="搜索网站名称或描述…" autocomplete="off">
    <button type="submit">🔍</button>
  </form>
</div>

<main>
<?php
$hasResult = false;
foreach ($categories as $cat):
    $links = $linksByCategory[$cat['id']] ?? [];
    if (empty($links)) continue;
    $hasResult = true;
?>
  <div class="section">
    <div class="section-title">
      <?= htmlspecialchars($cat['icon']) ?>
      <?= htmlspecialchars($cat['name']) ?>
      <span class="line"></span>
    </div>
    <div class="grid">
      <?php foreach ($links as $link): ?>
      <a class="card" href="click.php?id=<?= $link['id'] ?>" target="_blank" rel="noopener">
        <span class="icon"><?= htmlspecialchars($link['icon']) ?></span>
        <div class="info">
          <strong><?= htmlspecialchars($link['title']) ?></strong>
          <small><?= htmlspecialchars($link['description']) ?></small>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
<?php endforeach; ?>

<?php if (!$hasResult): ?>
  <div class="empty">
    <?= $keyword ? '未找到匹配的网站，请尝试其他关键词。' : '暂无数据，请先运行 <a href="install.php">install.php</a> 初始化。' ?>
  </div>
<?php endif; ?>
</main>

<footer>
  &copy; <?= date('Y') ?> <?= htmlspecialchars(SITE_NAME) ?> &nbsp;·&nbsp; Powered by PHP &amp; MySQL
</footer>

</body>
</html>
