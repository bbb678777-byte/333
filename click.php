<?php
/**
 * 点击跳转并统计点击量
 */
require_once 'config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT url FROM links WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('Location: index.php');
    exit;
}

// 更新点击量
$pdo->prepare('UPDATE links SET clicks = clicks + 1 WHERE id = ?')->execute([$id]);

header('Location: ' . $row['url']);
exit;
