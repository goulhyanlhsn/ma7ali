<?php
require_once 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$product = getProduct($id);

if (!$product) {
    header("Location: index.php");
    exit;
}

$pageTitle = $product['name'] . ' - ' . SITE_NAME;
$related = [];
if ($product['category_id']) {
    $related = getRelatedProducts($product['category_id'], $product['id']);
}
include 'header.php';
?>

<nav aria-label="breadcrumb" class="mb-4">
    <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">الرئيسية</a></li>
            <?php if ($product['category_name']): ?>
            <li class="breadcrumb-item"><a href="index.php?category=<?= sanitize($product['category_slug']) ?>"><?= sanitize($product['category_name']) ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?= sanitize($product['name']) ?></li>
        </ol>
    </div>
</nav>

<div class="container">
    <div class="row g-5">
        <div class="col-lg-6">
            <div class="product-detail-img">
                <?php if ($img = productImage($product['image'])): ?>
                    <img src="<?= $img ?>" alt="<?= sanitize($product['name']) ?>" style="width:100%;border-radius:var(--radius)">
                <?php else: ?>
                    <div class="product-img-placeholder-large">
                        <i class="bi bi-box-seam"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <?php if ($product['category_name']): ?>
                <span class="product-category" style="margin-bottom:0.8rem;display:inline-block;font-size:0.8rem;padding:0.3rem 0.8rem"><?= sanitize($product['category_name']) ?></span>
            <?php endif; ?>
            <h1 class="fw-bold mb-3" style="font-size:2rem;line-height:1.3"><?= sanitize($product['name']) ?></h1>

            <div class="d-flex align-items-center gap-3 mb-3">
                <span style="font-size:2.2rem;font-weight:900;background:var(--gradient-main);-webkit-background-clip:text;-webkit-text-fill-color:transparent"><?= formatPrice($product['price']) ?></span>
            </div>

            <div class="mb-3">
                <?php if ($product['stock'] > 0): ?>
                    <span class="badge" style="background:var(--gradient-green);font-size:0.8rem;padding:0.5em 0.8em"><i class="bi bi-check-circle ms-1"></i>متوفر (<?= $product['stock'] ?>)</span>
                <?php else: ?>
                    <span class="badge bg-danger" style="font-size:0.8rem;padding:0.5em 0.8em"><i class="bi bi-x-circle ms-1"></i>نفدت الكمية</span>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold">الوصف</h5>
                <p class="text-muted" style="line-height:1.8"><?= nl2br(sanitize($product['description'])) ?></p>
            </div>

            <?php if ($product['stock'] > 0): ?>
            <form method="POST" action="cart.php" class="mb-4">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="redirect" value="product?id=<?= $product['id'] ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-auto">
                        <label class="form-label fw-bold">الكمية</label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?= $product['stock'] ?>" style="width: 90px;">
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-cart-plus ms-2"></i>إضافة للسلة
                        </button>
                    </div>
                </div>
            </form>
            <?php endif; ?>

            <div class="d-flex gap-2">
                <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=<?= urlencode('مرحبا، أنا مهتم بالمنتج: ' . $product['name'] . ' - ' . formatPrice($product['price'])) ?>" class="btn btn-success btn-lg" target="_blank" style="flex:1">
                    <i class="bi bi-whatsapp ms-2"></i>استفسار عبر الواتساب
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty($related)): ?>
    <hr class="my-5">
    <h3 class="fw-bold mb-4">منتجات مشابهة</h3>
    <div class="row g-4">
        <?php foreach ($related as $rp): ?>
        <div class="col-sm-6 col-md-3">
            <div class="card product-card h-100">
                <div class="product-img-wrapper">
                    <div class="product-img-placeholder">
                        <?php if ($img = productImage($rp['image'])): ?>
                            <img src="<?= $img ?>" alt="<?= sanitize($rp['name']) ?>">
                        <?php else: ?>
                            <i class="bi bi-box-seam"></i>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <span class="product-category"><?= sanitize($rp['category_name'] ?? 'عام') ?></span>
                    <h6 class="product-title"><?= sanitize($rp['name']) ?></h6>
                    <div class="product-meta">
                        <span class="product-price"><?= formatPrice($rp['price']) ?></span>
                    </div>
                    <div class="product-actions">
                        <a href="product.php?id=<?= $rp['id'] ?>" class="btn btn-product-detail w-100"><i class="bi bi-eye ms-1"></i>عرض</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
