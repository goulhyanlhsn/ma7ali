<?php
require_once 'includes.php';
aCheck();
$pdo = getConnection();

$statusFilter = $_GET['status'] ?? '';
$where = "WHERE 1=1";
$params = [];
if ($statusFilter && in_array($statusFilter, ['pending','confirmed','shipped','delivered'])) {
    $where .= " AND o.status = ?";
    $params[] = $statusFilter;
}
$stmt = $pdo->prepare("SELECT o.* FROM orders o $where ORDER BY o.created_at DESC");
$stmt->execute($params);
$orders = $stmt->fetchAll();

$pageTitle = 'الطلبات';
$pageTitleIcon = 'bi-receipt-cutoff';
include 'header.php';
echo flash();

$statusLabels = ['pending'=>'قيد الانتظار','confirmed'=>'مؤكد','shipped'=>'شُحن','delivered'=>'تم التوصيل'];
$statusKeys = ['pending','confirmed','shipped','delivered'];
?>

<div style="display:flex;gap:0.5rem;margin-bottom:1.2rem;flex-wrap:wrap">
    <a href="orders.php" class="btn-a <?= !$statusFilter ? 'btn-a-primary' : 'btn-a-ghost' ?> btn-a-sm">الكل (<?= count($orders) ?>)</a>
    <?php foreach ($statusKeys as $sk): ?>
        <a href="orders.php?status=<?= $sk ?>" class="btn-a <?= $statusFilter===$sk ? 'btn-a-primary' : 'btn-a-ghost' ?> btn-a-sm"><?= $statusLabels[$sk] ?></a>
    <?php endforeach; ?>
</div>

<div class="card-a">
    <div class="card-a-body no-pad">
        <?php if (empty($orders)): ?>
            <div class="empty-state"><i class="bi bi-inbox"></i><p>لا توجد طلبات</p></div>
        <?php else: ?>
        <table class="table-a">
            <thead><tr>
                <th>#</th><th>العميل</th><th>الهاتف</th><th>المدينة</th><th>المبلغ</th><th>الحالة</th><th>التاريخ</th><th>إجراءات</th>
            </tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><strong>#<?= $o['id'] ?></strong></td>
                    <td style="font-weight:600"><?= e($o['full_name']) ?></td>
                    <td style="direction:ltr;text-align:right"><a href="tel:<?= e($o['phone']) ?>" style="color:var(--primary);text-decoration:none"><?= e($o['phone']) ?></a></td>
                    <td><?= e($o['city']) ?></td>
                    <td style="font-weight:700;color:var(--accent)"><?= p($o['total']) ?></td>
                    <td><span class="badge-a <?= $o['status'] ?>"><?= $statusLabels[$o['status']] ?? e($o['status']) ?></span></td>
                    <td style="color:var(--muted);font-size:0.82rem"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                    <td>
                        <div style="display:flex;gap:0.4rem">
                            <a href="order_view.php?id=<?= $o['id'] ?>" class="btn-a btn-a-ghost btn-a-sm" title="عرض"><i class="bi bi-eye-fill"></i></a>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $o['phone']) ?>" class="btn-a btn-a-success btn-a-sm" target="_blank" title="واتساب"><i class="bi bi-whatsapp"></i></a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
