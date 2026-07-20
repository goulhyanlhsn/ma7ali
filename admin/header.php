<?php
$page = basename($_SERVER['PHP_SELF'], '.php');

$pendingCount = 0;
try {
    $pdo = getConnection();
    $pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
} catch(Exception $e) {}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'لوحة التحكم' ?> - <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<button class="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open');document.querySelector('.sidebar-overlay').classList.toggle('open')">
    <i class="bi bi-list"></i>
</button>
<div class="sidebar-overlay" onclick="document.querySelector('.sidebar').classList.remove('open');this.classList.remove('open')"></div>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-bag-check-fill"></i></div>
        <div>
            <div class="brand-text"><?= SITE_NAME ?></div>
            <div class="brand-sub">لوحة التحكم</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">القائمة الرئيسية</div>
        <div class="nav-item">
            <a href="index.php" class="nav-link <?= $page==='index'?'active':'' ?>">
                <i class="bi bi-grid-1x2-fill nav-icon"></i>لوحة التحكم
            </a>
        </div>
        <div class="nav-item">
            <a href="products.php" class="nav-link <?= $page==='products'||$page==='product_form'?'active':'' ?>">
                <i class="bi bi-box-seam-fill nav-icon"></i>المنتجات
            </a>
        </div>
        <div class="nav-item">
            <a href="categories.php" class="nav-link <?= $page==='categories'?'active':'' ?>">
                <i class="bi bi-grid-3x3-gap-fill nav-icon"></i>الأقسام
            </a>
        </div>

        <div class="nav-label">إدارة الطلبات</div>
        <div class="nav-item">
            <a href="orders.php" class="nav-link <?= $page==='orders'||$page==='order_view'?'active':'' ?>">
                <i class="bi bi-receipt-cutoff nav-icon"></i>الطلبات
                <?php if ($pendingCount > 0): ?>
                    <span class="badge-count"><?= $pendingCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="avatar"><i class="bi bi-person-fill"></i></div>
        <div class="user-info">
            <div class="name">المدير</div>
            <div class="role">مدير النظام</div>
        </div>
        <a href="logout.php" class="logout" title="خروج"><i class="bi bi-box-arrow-left"></i></a>
    </div>
</aside>

<div class="topbar">
    <div class="page-title">
        <i class="bi <?= $pageTitleIcon ?? 'bi-grid-1x2-fill' ?>"></i>
        <?= $pageTitle ?? 'لوحة التحكم' ?>
    </div>
    <div class="topbar-actions">
        <a href="../index.php" class="btn-icon" title="عرض المتجر" target="_blank"><i class="bi bi-shop"></i></a>
    </div>
</div>

<div class="main-content">
