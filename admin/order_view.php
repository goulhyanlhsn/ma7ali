<?php
require_once 'includes.php';
aCheck();
$pdo = getConnection();

if (!isset($_GET['id'])) { header("Location: orders.php"); exit; }

$orderId = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();
if (!$order) { header("Location: orders.php"); exit; }

$stmt2 = $pdo->prepare("SELECT * FROM order_items WHERE order_id=?");
$stmt2->execute([$orderId]);
$items = $stmt2->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'];
    if (in_array($newStatus, ['pending','confirmed','shipped','delivered'])) {
        $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$newStatus, $orderId]);
        flash('success', 'تم تحديث حالة الطلب');
        header("Location: order_view.php?id=$orderId");
        exit;
    }
}

$statusLabels = ['pending'=>'قيد الانتظار','confirmed'=>'مؤكد','shipped'=>'شُحن','delivered'=>'تم التوصيل'];

$pageTitle = "طلب #$orderId";
$pageTitleIcon = 'bi-receipt-cutoff';
include 'header.php';
echo flash();
?>

<div class="order-meta-grid">
    <div class="meta-item">
        <div class="meta-label">رقم الطلب</div>
        <div class="meta-value">#<?= $order['id'] ?></div>
    </div>
    <div class="meta-item">
        <div class="meta-label">العميل</div>
        <div class="meta-value"><?= e($order['full_name']) ?></div>
    </div>
    <div class="meta-item">
        <div class="meta-label">الهاتف</div>
        <div class="meta-value" style="direction:ltr;text-align:right"><a href="tel:<?= e($order['phone']) ?>" style="color:var(--primary);text-decoration:none"><?= e($order['phone']) ?></a></div>
    </div>
    <div class="meta-item">
        <div class="meta-label">المدينة</div>
        <div class="meta-value"><?= e($order['city']) ?></div>
    </div>
    <div class="meta-item">
        <div class="meta-label">العنوان</div>
        <div class="meta-value" style="font-size:0.85rem"><?= e($order['address']) ?></div>
    </div>
    <div class="meta-item">
        <div class="meta-label">التاريخ</div>
        <div class="meta-value"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1rem">
    <div class="card-a">
        <div class="card-a-header"><h3><i class="bi bi-box-seam" style="color:var(--primary)"></i>المنتجات</h3></div>
        <div class="card-a-body no-pad">
            <table class="table-a">
                <thead><tr><th>المنتج</th><th>الكمية</th><th>السعر</th><th>المجموع</th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td style="font-weight:600"><?= e($item['product_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= p($item['price']) ?></td>
                        <td style="font-weight:700;color:var(--accent)"><?= p($item['price'] * $item['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot><tr>
                    <td colspan="3" style="font-weight:700;text-align:left">الإجمالي</td>
                    <td style="font-weight:800;color:var(--accent);font-size:1.05rem"><?= p($order['total']) ?></td>
                </tr></tfoot>
            </table>
        </div>
    </div>

    <div>
        <div class="card-a" style="margin-bottom:1rem">
            <div class="card-a-header"><h3><i class="bi bi-gear-fill" style="color:var(--accent)"></i>تحديث الحالة</h3></div>
            <div class="card-a-body">
                <form method="POST">
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <?php foreach ($statusLabels as $k => $v): ?>
                                <option value="<?= $k ?>" <?= $order['status']===$k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-a btn-a-primary" style="width:100%;justify-content:center"><i class="bi bi-check-lg ms-1"></i>تحديث</button>
                </form>
            </div>
        </div>

        <div class="card-a">
            <div class="card-a-body" style="display:flex;flex-direction:column;gap:0.75rem">
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $order['phone']) ?>" class="btn-a btn-a-success" style="justify-content:center" target="_blank">
                    <i class="bi bi-whatsapp ms-1"></i>تواصل واتساب
                </a>
                <a href="tel:<?= e($order['phone']) ?>" class="btn-a btn-a-ghost" style="justify-content:center">
                    <i class="bi bi-telephone ms-1"></i>اتصال هاتفي
                </a>
                <?php if ($order['email']): ?>
                    <a href="mailto:<?= e($order['email']) ?>" class="btn-a btn-a-ghost" style="justify-content:center">
                        <i class="bi bi-envelope ms-1"></i>إرسال إيميل
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($order['message']): ?>
        <div class="card-a" style="margin-top:1rem">
            <div class="card-a-header"><h3><i class="bi bi-chat-dots" style="color:var(--primary)"></i>رسالة العميل</h3></div>
            <div class="card-a-body"><p style="color:var(--text-dim);line-height:1.7"><?= nl2br(e($order['message'])) ?></p></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div style="margin-top:1.2rem">
    <a href="orders.php" class="btn-a btn-a-ghost"><i class="bi bi-arrow-right ms-1"></i>العودة للطلبات</a>
</div>

<?php include 'footer.php'; ?>
