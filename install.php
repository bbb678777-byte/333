<?php
/**
 * 安装脚本 - 初始化数据库表和数据
 * 运行一次后可删除此文件
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nav_site');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // 创建数据库
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");

    // 创建分类表
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `categories` (
            `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name`       VARCHAR(50) NOT NULL,
            `icon`       VARCHAR(10) NOT NULL DEFAULT '🔗',
            `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // 创建网址表
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `links` (
            `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `category_id` INT UNSIGNED NOT NULL,
            `title`       VARCHAR(100) NOT NULL,
            `url`         VARCHAR(500) NOT NULL,
            `description` VARCHAR(200) NOT NULL DEFAULT '',
            `icon`        VARCHAR(10) NOT NULL DEFAULT '🌐',
            `clicks`      INT UNSIGNED NOT NULL DEFAULT 0,
            `sort_order`  TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY `fk_category` (`category_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // 插入分类数据
    $pdo->exec("INSERT IGNORE INTO `categories` (`id`, `name`, `icon`, `sort_order`) VALUES
        (1, '搜索引擎', '🔍', 1),
        (2, '社交媒体', '💬', 2),
        (3, '技术开发', '💻', 3),
        (4, '资讯娱乐', '📰', 4)
    ");

    // 插入10条网址数据
    $pdo->exec("INSERT IGNORE INTO `links` (`id`, `category_id`, `title`, `url`, `description`, `icon`, `sort_order`) VALUES
        (1,  1, 'Google',     'https://www.google.com',     '全球最大搜索引擎',         '🔍', 1),
        (2,  1, '百度',       'https://www.baidu.com',      '中文互联网搜索引擎',       '🔎', 2),
        (3,  1, 'Bing',       'https://www.bing.com',       '微软必应搜索',             '🌐', 3),
        (4,  2, 'GitHub',     'https://github.com',         '全球最大代码托管平台',     '🐙', 1),
        (5,  2, 'Twitter/X',  'https://twitter.com',        '全球实时信息社交平台',     '🐦', 2),
        (6,  3, 'Stack Overflow', 'https://stackoverflow.com', '程序员问答社区',        '📚', 1),
        (7,  3, 'MDN',        'https://developer.mozilla.org', 'Web 技术权威文档',      '📖', 2),
        (8,  3, 'npm',        'https://www.npmjs.com',      'JavaScript 包管理平台',   '📦', 3),
        (9,  4, 'YouTube',    'https://www.youtube.com',    '全球最大视频平台',         '▶️',  1),
        (10, 4, 'Wikipedia',  'https://www.wikipedia.org',  '自由的网络百科全书',       '📕', 2)
    ");

    echo '<p style="color:green;font-family:sans-serif;">✅ 安装成功！数据库和数据初始化完毕。</p>';
    echo '<p style="font-family:sans-serif;"><a href="index.php">前往首页 →</a></p>';
    echo '<p style="color:red;font-size:13px;font-family:sans-serif;">⚠️ 安全提示：安装完成后请删除此文件 install.php</p>';

} catch (PDOException $e) {
    echo '<p style="color:red;font-family:sans-serif;">❌ 安装失败：' . htmlspecialchars($e->getMessage()) . '</p>';
}
