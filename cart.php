<?php
require_once 'includes/functions.php';
verifyCSRF();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$productId = (int)($_POST['product_id'] ?? 0);

if ($action === 'add' && $productId > 0) {
    $qty = max(1, min(99, (int)($_POST['quantity'] ?? 1)));
    addToCart($productId, $qty);
    flashMessage('success', 'تمت إضافة المنتج للسلة بنجاح!');
    $redirect = $_POST['redirect'] ?? 'cart.php';
    if ($redirect === 'product?id=' . $productId) {
        header("Location: product.php?id=" . $productId);
    } else {
        header("Location: cart.php");
    }
    exit;
}

if ($action === 'update') {
    verifyCSRF();
    $cartId = (int)($_POST['cart_id'] ?? 0);
    $qty = (int)($_POST['quantity'] ?? 1);
    updateCartItem($cartId, $qty);
    header("Location: cart.php");
    exit;
}

if ($action === 'remove') {
    verifyCSRF();
    $cartId = (int)($_POST['cart_id'] ?? 0);
    removeFromCart($cartId);
    flashMessage('success', 'تم حذف المنتج من السلة.');
    header("Location: cart.php");
    exit;
}

$items = getCartItems();
$total = getCartTotal();
$pageTitle = 'سلة المشتريات - ' . SITE_NAME;
include 'header.php';
?>

<div class="container">
    <h1 class="fw-bold mb-4" style="font-size:1.8rem"><i class="bi bi-cart3 ms-2"></i>سلة المشتريات</h1>

    <?php if (empty($items)): ?>
        <div class="cart-empty">
            <div class="cart-empty-icon">
                <i class="bi bi-cart-x"></i>
            </div>
            <h3 class="fw-bold">سلتك فارغة</h3>
            <p class="text-muted">لم تضف أي منتجات بعد. ابدأ التسوق الآن!</p>
            <a href="index.php" class="btn btn-primary btn-lg mt-2"><i class="bi bi-bag ms-2"></i>ابدأ التسوق</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cart-items-count mb-3">
                    <i class="bi bi-bag-check ms-1"></i><?= count($items) ?> منتج في السلة
                </div>
                <div class="cart-cards">
                    <?php foreach ($items as $item): ?>
                    <div class="cart-card">
                        <div class="cart-card-img">
                            <?php if ($img = productImage($item['image'])): ?>
                                <img src="<?= $img ?>" alt="">
                            <?php else: ?>
                                <i class="bi bi-box-seam"></i>
                            <?php endif; ?>
                        </div>
                        <div class="cart-card-body">
                            <a href="product.php?id=<?= $item['id'] ?>" class="cart-card-name"><?= sanitize($item['name']) ?></a>
                            <div class="cart-card-price"><?= formatPrice($item['price']) ?></div>
                            <div class="cart-card-controls">
                                <form method="POST" action="cart.php" class="d-inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_id" value="<?= (int)$item['cart_id'] ?>">
                                    <button type="submit" class="cart-qty-btn cart-qty-remove" title="حذف"><i class="bi bi-trash3"></i></button>
                                </form>
                                <form method="POST" action="cart.php" class="d-inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_id" value="<?= (int)$item['cart_id'] ?>">
                                    <input type="hidden" name="quantity" value="<?= max(1, (int)$item['quantity'] - 1) ?>">
                                    <button type="submit" class="cart-qty-btn" <?= (int)$item['quantity'] <= 1 ? 'disabled' : '' ?>><i class="bi bi-dash"></i></button>
                                </form>
                                <span class="cart-qty-value"><?= (int)$item['quantity'] ?></span>
                                <form method="POST" action="cart.php" class="d-inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_id" value="<?= (int)$item['cart_id'] ?>">
                                    <input type="hidden" name="quantity" value="<?= min(99, (int)$item['quantity'] + 1) ?>">
                                    <button type="submit" class="cart-qty-btn" <?= (int)$item['quantity'] >= 99 ? 'disabled' : '' ?>><i class="bi bi-plus"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="cart-card-total">
                            <div class="cart-card-subtotal-label">المجموع</div>
                            <div class="cart-card-subtotal"><?= formatPrice($item['price'] * $item['quantity']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h5 class="cart-summary-title"><i class="bi bi-receipt ms-1"></i>ملخص الطلب</h5>
                    <div class="cart-summary-row">
                        <span>المجموع الفرعي</span>
                        <span class="fw-bold"><?= formatPrice($total) ?></span>
                    </div>
                    <div class="cart-summary-row">
                        <span>التوصيل</span>
                        <span class="cart-delivery">مجاني <i class="bi bi-truck"></i></span>
                    </div>
                    <div class="cart-summary-divider"></div>
                    <div class="cart-summary-total">
                        <span>الإجمالي</span>
                        <span><?= formatPrice($total) ?></span>
                    </div>
                    <a href="checkout.php" class="btn btn-primary btn-lg w-100 cart-checkout-btn">
                        <i class="bi bi-credit-card ms-2"></i>إتمام الطلب
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary w-100" style="margin-top:0.5rem">
                        <i class="bi bi-arrow-right ms-2"></i>متابعة التسوق
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
