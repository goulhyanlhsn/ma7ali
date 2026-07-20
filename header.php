<?php if (!isset($pageTitle)) $pageTitle = SITE_NAME; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <span class="brand-icon"><i class="bi bi-lightning-charge-fill"></i></span><?= SITE_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>الرئيسية</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-grid me-1"></i>الأقسام</a>
                    <ul class="dropdown-menu">
                        <?php
                        $navCats = getCategories();
                        foreach ($navCats as $cat): ?>
                            <li><a class="dropdown-item" href="index.php?category=<?= sanitize($cat['slug']) ?>"><?= sanitize($cat['name']) ?> <span class="badge bg-primary ms-1" style="font-size:0.65rem"><?= $cat['product_count'] ?></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
            <form class="d-flex me-3 search-form" action="index.php" method="GET">
                <div class="input-group">
                    <input type="search" name="search" class="form-control" placeholder="بحث عن منتج..." value="<?= sanitize($_GET['search'] ?? '') ?>">
                    <button class="btn" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link cart-icon-link" href="cart.php" aria-label="سلة المشتريات (<?= getCartCount() ?> منتج)" role="button">
                        <svg class="cart-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75-9a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        <span class="cart-badge" id="cartBadge" aria-hidden="true"><?= getCartCount() ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?= flashMessage() ?>
</main>
