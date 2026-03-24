# 网址导航站

基于 PHP + MySQL 的简洁网址导航网站。

## 功能

- 分类展示导航链接
- 关键词搜索
- 点击量统计（通过中转跳转）
- 响应式布局，支持移动端

## 文件说明

| 文件 | 说明 |
|------|------|
| `config.php` | 数据库连接配置 |
| `install.php` | 一键初始化数据库（运行一次后删除） |
| `index.php` | 首页 |
| `click.php` | 点击统计中转跳转 |

## 部署步骤

1. 将文件上传至服务器 Web 目录
2. 修改 `config.php` 中的数据库连接信息
3. 浏览器访问 `install.php` 完成数据库初始化
4. 访问 `index.php` 即可使用
5. **删除 `install.php`**（安全起见）

## 预置导航网址（10条）

| 分类 | 网站 |
|------|------|
| 搜索引擎 | Google、百度、Bing |
| 社交媒体 | GitHub、Twitter/X |
| 技术开发 | Stack Overflow、MDN、npm |
| 资讯娱乐 | YouTube、Wikipedia |

## 环境要求

- PHP 7.4+（使用 PDO）
- MySQL 5.7+ / MariaDB 10.3+
