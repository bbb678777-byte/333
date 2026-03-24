<?php
require_once 'config.php';

$keyword = trim($_GET['q'] ?? '');
$activecat = (int)($_GET['cat'] ?? 0);

$categories = $pdo->query('SELECT * FROM categories ORDER BY sort_order, id')->fetchAll(PDO::FETCH_ASSOC);

$linksByCategory = [];
if ($keyword !== '') {
    $stmt = $pdo->prepare('SELECT * FROM links WHERE title LIKE ? OR description LIKE ? ORDER BY sort_order, id');
    $stmt->execute(['%' . $keyword . '%', '%' . $keyword . '%']);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $link) {
        $linksByCategory[$link['category_id']][] = $link;
    }
} elseif ($activecat > 0) {
    $stmt = $pdo->prepare('SELECT * FROM links WHERE category_id = ? ORDER BY sort_order, id');
    $stmt->execute([$activecat]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $link) {
        $linksByCategory[$link['category_id']][] = $link;
    }
} else {
    foreach ($pdo->query('SELECT * FROM links ORDER BY sort_order, id')->fetchAll(PDO::FETCH_ASSOC) as $link) {
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
    --bg:        #0d1117;
    --bg2:       #161b22;
    --bg3:       #21262d;
    --accent:    #58a6ff;
    --accent2:   #3fb950;
    --text:      #e6edf3;
    --muted:     #8b949e;
    --border:    rgba(255,255,255,.08);
    --glass:     rgba(255,255,255,.04);
    --radius:    10px;
    --sidebar-w: 220px;
  }

  html, body { height: 100%; }
  body {
    background: var(--bg);
    color: var(--text);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto,
                 'PingFang SC', 'Microsoft YaHei', sans-serif;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  /* ── Top Bar ── */
  .topbar {
    position: sticky;
    top: 0;
    z-index: 100;
    background: rgba(13,17,23,.85);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 0 24px;
    height: 56px;
  }
  .topbar .logo {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--accent);
    white-space: nowrap;
    text-decoration: none;
  }
  .topbar form {
    flex: 1;
    max-width: 480px;
    display: flex;
    background: var(--bg3);
    border: 1px solid var(--border);
    border-radius: 6px;
    overflow: hidden;
    transition: border-color .2s;
  }
  .topbar form:focus-within { border-color: var(--accent); }
  .topbar form input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    color: var(--text);
    padding: 8px 14px;
    font-size: .9rem;
  }
  .topbar form input::placeholder { color: var(--muted); }
  .topbar form button {
    background: transparent;
    border: none;
    color: var(--muted);
    padding: 0 14px;
    cursor: pointer;
    font-size: 1rem;
    transition: color .2s;
  }
  .topbar form button:hover { color: var(--accent); }

  /* ── Layout ── */
  .layout {
    display: flex;
    flex: 1;
    max-width: 1280px;
    width: 100%;
    margin: 0 auto;
    padding: 24px 16px;
    gap: 24px;
  }

  /* ── Sidebar ── */
  aside {
    width: var(--sidebar-w);
    flex-shrink: 0;
  }
  .sidebar-inner {
    position: sticky;
    top: 72px;
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
  }
  .sidebar-inner .sid-head {
    padding: 12px 16px;
    font-size: .75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--muted);
    border-bottom: 1px solid var(--border);
  }
  .sid-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    text-decoration: none;
    color: var(--muted);
    font-size: .9rem;
    transition: background .15s, color .15s;
    border-left: 3px solid transparent;
  }
  .sid-item:hover  { background: var(--glass); color: var(--text); }
  .sid-item.active { background: var(--glass); color: var(--accent); border-left-color: var(--accent); }
  .sid-item .cnt {
    margin-left: auto;
    font-size: .72rem;
    background: var(--bg3);
    color: var(--muted);
    padding: 1px 7px;
    border-radius: 20px;
  }

  /* ── Content ── */
  .content { flex: 1; min-width: 0; }

  .section { margin-bottom: 36px; }
  .section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: .85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--muted);
    margin-bottom: 14px;
  }
  .section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
  }

  /* ── Grid ── */
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 12px;
  }

  /* ── Card ── */
  .card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px;
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    text-decoration: none;
    color: var(--text);
    transition: border-color .2s, background .2s, transform .2s;
    position: relative;
    overflow: hidden;
  }
  .card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(88,166,255,.06), transparent 60%);
    opacity: 0;
    transition: opacity .2s;
  }
  .card:hover {
    border-color: rgba(88,166,255,.4);
    background: var(--bg3);
    transform: translateY(-2px);
  }
  .card:hover::before { opacity: 1; }

  .card .icon {
    font-size: 1.5rem;
    line-height: 1;
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg3);
    border-radius: 8px;
  }
  .card .info { overflow: hidden; }
  .card .info strong {
    display: block;
    font-size: .9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .card .info small {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    color: var(--muted);
    font-size: .76rem;
    margin-top: 3px;
    line-height: 1.45;
  }

  /* ── Empty ── */
  .empty {
    text-align: center;
    color: var(--muted);
    padding: 80px 0;
    font-size: .95rem;
  }
  .empty a { color: var(--accent); }

  /* ── Footer ── */
  footer {
    text-align: center;
    color: var(--muted);
    font-size: .78rem;
    padding: 16px;
    border-top: 1px solid var(--border);
  }

  /* ── Responsive ── */
  @media (max-width: 720px) {
    aside { display: none; }
    .grid { grid-template-columns: repeat(auto-fill, minmax(155px, 1fr)); }
  }
</style>
</head>
<body>

<!-- Top Bar -->
<div class="topbar">
  <a class="logo" href="index.php">🌐 <?= htmlspecialchars(SITE_NAME) ?></a>
  <form method="get" action="index.php">
    <input type="text" name="q"
           value="<?= htmlspecialchars($keyword) ?>"
           placeholder="搜索网站…" autocomplete="off">
    <button type="submit">⌕</button>
  </form>
</div>

<div class="layout">

  <!-- Sidebar -->
  <aside>
    <div class="sidebar-inner">
      <div class="sid-head">分类导航</div>
      <a class="sid-item <?= $activecat === 0 && $keyword === '' ? 'active' : '' ?>"
         href="index.php">🏠 全部网站
        <span class="cnt"><?= array_sum(array_map('count', $linksByCategory)) ?></span>
      </a>
      <?php foreach ($categories as $cat):
        $cnt = count($pdo->query("SELECT id FROM links WHERE category_id={$cat['id']}")->fetchAll());
      ?>
      <a class="sid-item <?= $activecat === (int)$cat['id'] ? 'active' : '' ?>"
         href="index.php?cat=<?= $cat['id'] ?>">
        <?= htmlspecialchars($cat['icon']) ?>
        <?= htmlspecialchars($cat['name']) ?>
        <span class="cnt"><?= $cnt ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="content">
    <?php
    $hasResult = false;
    foreach ($categories as $cat):
      $links = $linksByCategory[$cat['id']] ?? [];
      if (empty($links)) continue;
      $hasResult = true;
    ?>
    <div class="section">
      <div class="section-title">
        <?= htmlspecialchars($cat['icon']) ?> <?= htmlspecialchars($cat['name']) ?>
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
      <?= $keyword
        ? '未找到匹配的网站，请尝试其他关键词。'
        : '暂无数据，请先运行 <a href="install.php">install.php</a> 初始化。' ?>
    </div>
    <?php endif; ?>
  </div>

</div>

<footer>&copy; <?= date('Y') ?> <?= htmlspecialchars(SITE_NAME) ?> &nbsp;·&nbsp; Powered by PHP &amp; MySQL</footer>

</body>
</html>
