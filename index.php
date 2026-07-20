<?php
require_once 'includes/functions.php';
$pageTitle = SITE_NAME . ' - متجر إلكتروني';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));

$result = getProducts($page, PRODUCTS_PER_PAGE, $search, $category);
$products = $result['products'];
$totalPages = $result['totalPages'];
$currentPage = $result['currentPage'];
$categories = getCategories();
$featured = getFeaturedProducts();

if (!$search && !$category && $currentPage === 1):
?>
<?php include 'header.php'; ?>

<?php if (empty($featured) === false): ?>
<section class="hero-section mb-5" style="margin-top: -5%;">
        <div class="hero-particles">
        <div class="particle particle-1"></div>
        <div class="particle particle-2"></div>
        <div class="particle particle-3"></div>
        <div class="particle particle-4"></div>
    </div>
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="hero-badge">
                    <i class="bi bi-fire ms-1"></i> عروض حصرية
                </div>
                <h1 class="hero-title">مرحبا بكم في<br><span class="hero-brand"><?= SITE_NAME ?></span></h1>
                <p class="hero-subtitle">اكتشف تشكيلتنا المميزة من المنتجات عالية الجودة. اطلب بسهولة وسرعة واستلم طلبك مباشرة عبر الواتساب.</p>
                <div class="hero-buttons">
                   
                    <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" class="btn btn-whatsapp btn-lg hero-btn-whatsapp" target="_blank">
                        <i class="bi bi-whatsapp ms-2"></i>تواصل معنا
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-number" data-count="<?= count($products) ?>">0</span>
                        <span class="hero-stat-label">منتج متوفر</span>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <span class="hero-stat-number" data-count="6">0</span>
                        <span class="hero-stat-label">قسم متنوع</span>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <span class="hero-stat-number" data-count="100">0</span>
                        <span class="hero-stat-label">% رضا العملاء</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-visual">
                    <div class="hero-glow"></div>
                    <div class="hero-icon-main">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <div class="hero-float-card hero-float-1">
                        <i class="bi bi-truck"></i>
                        <span>توصيل سريع</span>
                    </div>
                    <div class="hero-float-card hero-float-2">
                        <i class="bi bi-shield-check"></i>
                        <span>دفع آمن</span>
                    </div>
                    <div class="hero-float-card hero-float-3">
                        <i class="bi bi-headset"></i>
                        <span>دعم 24/7</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="container">
        <h2 class="fw-bold mb-4"><i class="bi bi-star-fill text-warning ms-2"></i>منتجات مميزة</h2>
        <div class="row g-4">
            <?php foreach ($featured as $prod): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card product-card h-100">
                    <div class="product-img-wrapper">
                        <span class="badge-overlay badge-featured"><i class="bi bi-star-fill ms-1"></i>مميز</span>
                        <div class="product-img-placeholder">
                            <?php if ($img = productImage($prod['image'])): ?>
                                <img src="<?= $img ?>" alt="<?= sanitize($prod['name']) ?>">
                            <?php else: ?>
                                <i class="bi bi-box-seam"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <span class="product-category"><?= sanitize($prod['category_name'] ?? 'عام') ?></span>
                        <h6 class="product-title"><?= sanitize($prod['name']) ?></h6>
                        <div class="product-meta">
                            <span class="product-price"><?= formatPrice($prod['price']) ?></span>
                            <span class="product-stock"><span class="dot in"></span>متوفر</span>
                        </div>
                        <div class="product-actions">
                            <a href="product.php?id=<?= $prod['id'] ?>" class="btn btn-product-detail"><i class="bi bi-eye ms-1"></i>التفاصيل</a>
                            <form method="POST" action="cart.php" class="d-inline" style="flex:1">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                <button type="submit" class="btn btn-product-cart w-100"><i class="bi bi-cart-plus ms-1"></i>إضافة</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section id="products" class="mb-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0"><i class="bi bi-grid ms-2"></i>جميع المنتجات</h2>
            <div class="btn-group btn-group-sm d-none d-md-flex">
                <a href="index.php" class="btn <?= !$category ? 'btn-primary' : 'btn-outline-primary' ?>">الكل</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="index.php?category=<?= sanitize($cat['slug']) ?>" class="btn <?= $category === $cat['slug'] ? 'btn-primary' : 'btn-outline-primary' ?>"><?= sanitize($cat['name']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($search): ?>
            <div class="mb-3">
                <p class="text-muted">نتائج البحث عن "<strong><?= sanitize($search) ?></strong>" (<?= $result['total'] ?> منتج)</p>
            </div>
        <?php endif; ?>
<?php else: ?>
<?php include 'header.php'; ?>
<section class="mb-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-0">
                <?php if ($search): ?>
                    نتائج "<strong><?= sanitize($search) ?></strong>"
                <?php elseif ($category): ?>
                    <?php foreach ($categories as $cat) { if ($cat['slug'] === $category) { echo sanitize($cat['name']); break; } } ?>
                <?php else: ?>
                    جميع المنتجات
                <?php endif; ?>
            </h2>
            <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-circle ms-1"></i>مسح</a>
        </div>
<?php endif; ?>

<?php if (empty($products)): ?>
    <div class="text-center py-5">
        <div style="width:120px;height:120px;margin:0 auto 1.5rem;background:var(--primary-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:3.5rem;color:var(--primary);opacity:0.5">
            <i class="bi bi-inbox"></i>
        </div>
        <p class="text-muted fs-5">لم يتم العثور على منتجات.</p>
        <a href="index.php" class="btn btn-primary btn-lg mt-2"><i class="bi bi-bag ms-2"></i>عرض جميع المنتجات</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($products as $prod): ?>
        <div class="col-sm-6 col-lg-4">
            <div class="card product-card h-100">
                <div class="product-img-wrapper">
                    <?php if ($prod['featured']): ?>
                        <span class="badge-overlay badge-featured"><i class="bi bi-star-fill ms-1"></i>مميز</span>
                    <?php endif; ?>
                    <?php if ($prod['stock'] <= 0): ?>
                        <span class="badge-overlay badge-stock-out"><i class="bi bi-x-circle ms-1"></i>نفدت</span>
                    <?php endif; ?>
                    <div class="product-img-placeholder">
                        <?php if ($img = productImage($prod['image'])): ?>
                            <img src="<?= $img ?>" alt="<?= sanitize($prod['name']) ?>">
                        <?php else: ?>
                            <i class="bi bi-box-seam"></i>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <span class="product-category"><?= sanitize($prod['category_name'] ?? 'عام') ?></span>
                    <h5 class="product-title"><?= sanitize($prod['name']) ?></h5>
                    <p class="product-desc"><?= sanitize(substr($prod['description'], 0, 80)) ?></p>
                    <div class="product-meta">
                        <span class="product-price"><?= formatPrice($prod['price']) ?></span>
                        <span class="product-stock">
                            <?php if ($prod['stock'] > 0): ?>
                                <span class="dot in"></span><?= $prod['stock'] ?> متوفر
                            <?php else: ?>
                                <span class="dot out"></span>نفدت
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="product-actions">
                        <a href="product.php?id=<?= $prod['id'] ?>" class="btn btn-product-detail"><i class="bi bi-eye ms-1"></i>التفاصيل</a>
                        <?php if ($prod['stock'] > 0): ?>
                        <form method="POST" action="cart.php" class="d-inline" style="flex:1">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                            <button type="submit" class="btn btn-product-cart w-100"><i class="bi bi-cart-plus ms-1"></i>إضافة</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage - 1 ?>&search=<?= sanitize($search) ?>&category=<?= sanitize($category) ?>">السابق</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&search=<?= sanitize($search) ?>&category=<?= sanitize($category) ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?>&search=<?= sanitize($search) ?>&category=<?= sanitize($category) ?>">التالي</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
<?php endif; ?>

</div>
</section>

<?php include 'footer.php'; ?>
