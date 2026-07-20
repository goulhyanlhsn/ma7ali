<?php
require_once 'includes/functions.php';

$items = getCartItems();
if (empty($items)) {
    flashMessage('error', 'سلتك فارغة. أضف منتجات أولاً.');
    header("Location: index.php");
    exit;
}

$pageTitle = 'إتمام الطلب - ' . SITE_NAME;

$fullName = '';
$phone = '';
$email = '';
$city = '';
$address = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRF();
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($fullName) || empty($phone) || empty($city) || empty($address)) {
        flashMessage('error', 'يرجى ملء جميع الحقول الإجبارية (الاسم، الهاتف، المدينة، العنوان).');
    } else {
        $whatsappUrl = generateWhatsAppLink($items, $fullName, $phone, $email, $city, $address, $message);

        $total = getCartTotal();
        $pdo = getConnection();
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, full_name, phone, email, city, address, total, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            null,
            $fullName,
            $phone,
            $email,
            $city,
            $address,
            $total,
            $message
        ]);
        $orderId = $pdo->lastInsertId();

        foreach ($items as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$orderId, $item['id'], $item['name'], $item['quantity'], $item['price']]);
        }

        clearCart();

        header("Location: " . $whatsappUrl);
        exit;
    }
}

include 'header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-4" style="font-size:1.8rem"><i class="bi bi-credit-card ms-2"></i>إتمام الطلب</h1>

            <div class="row g-4">
                <div class="col-md-7">
                    <div class="card checkout-order-card">
                        <div class="card-header" style="padding:1rem 1.2rem">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill ms-2" style="color:var(--primary)"></i>معلوماتك</h5>
                        </div>
                        <div class="card-body checkout-form" style="padding:1.5rem">
                            <form method="POST" id="checkoutForm">
                                <?= csrfField() ?>
                                <div class="mb-3">
                                    <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" value="<?= sanitize($fullName) ?>" placeholder="محمد أمين" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">الهاتف <span class="text-danger">*</span></label>
                                        <input type="tel" name="phone" class="form-control" value="<?= sanitize($phone) ?>" placeholder="0612345678" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">البريد الإلكتروني</label>
                                        <input type="email" name="email" class="form-control" value="<?= sanitize($email) ?>" placeholder="votre@email.com">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">المدينة <span class="text-danger">*</span></label>
                                        <input type="text" name="city" class="form-control" value="<?= sanitize($city) ?>" placeholder="الدار البيضاء" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                        <input type="text" name="address" class="form-control" value="<?= sanitize($address) ?>" placeholder="الشارع، رقم المنزل، الحي" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">رسالة (اختياري)</label>
                                    <textarea name="message" class="form-control" rows="3" placeholder="تعليمات التوصيل، الوقت المفضل..."><?= sanitize($message) ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-whatsapp btn-lg w-100">
                                    <i class="bi bi-whatsapp ms-2"></i>طلب عبر الواتساب
                                </button>
                                <p class="text-muted text-center mt-2 small">
                                    <i class="bi bi-info-circle ms-1"></i>
                                    سيتم تحويلك إلى الواتساب مع ملخص طلبك.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card checkout-order-card">
                        <div class="card-header" style="padding:1rem 1.2rem">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-bag-check ms-2" style="color:var(--primary)"></i>ملخص الطلب</h5>
                        </div>
                        <div class="card-body" style="padding:1.5rem">
                            <?php foreach ($items as $item): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <span class="fw-bold"><?= sanitize($item['name']) ?></span>
                                    <small class="text-muted d-block">× <?= $item['quantity'] ?></small>
                                </div>
                                <span class="fw-bold"><?= formatPrice($item['price'] * $item['quantity']) ?></span>
                            </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">المجموع الفرعي</span>
                                <span class="fw-bold"><?= formatPrice(getCartTotal()) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">التوصيل</span>
                                <span class="fw-bold" style="color:var(--green)">مجاني <i class="bi bi-truck"></i></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span style="font-size:1.2rem;font-weight:900">الإجمالي</span>
                                <span style="font-size:1.5rem;font-weight:900;background:var(--gradient-main);-webkit-background-clip:text;-webkit-text-fill-color:transparent"><?= formatPrice(getCartTotal()) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3" style="border:2px solid var(--green);border-radius:var(--radius)">
                        <div class="card-body text-center" style="padding:1.5rem">
                            <div style="width:60px;height:60px;margin:0 auto 0.8rem;background:var(--gradient-green);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#fff">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h6 class="mt-2 fw-bold">طلب آمن</h6>
                            <p class="text-muted small mb-0">ستتلقى ملخص الطلب عبر الواتساب لتأكيد طلبك.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
