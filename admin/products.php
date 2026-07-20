<?php
require_once 'includes.php';
aCheck();
$pdo = getConnection();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
    flash('success', 'تم حذف المنتج');
    header("Location: products.php");
    exit;
}

$search = trim($_GET['q'] ?? '');
$where = "WHERE 1=1";
$params = [];
if ($search) {
    $where .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$stmt = $pdo->prepare("
    SELECT p.*, c.name as cat_name
    FROM products p LEFT JOIN categories c ON p.category_id = c.id
    $where ORDER BY p.created_at DESC
");
$stmt->execute($params);
$products = $stmt->fetchAll();

$pageTitle = 'المنتجات';
$pageTitleIcon = 'bi-box-seam-fill';
include 'header.php';
echo flash();
?>

<div class="card-a">
    <div class="card-a-header">
        <h3><i class="bi bi-box-seam" style="color:var(--primary)"></i>المنتجات (<?= count($products) ?>)</h3>
        <div style="display:flex;gap:0.75rem;align-items:center">
            <form method="GET" class="search-box" style="width:220px">
                <input type="text" name="q" class="form-control" placeholder="بحث في المنتجات..." value="<?= e($search) ?>">
                <i class="bi bi-search"></i>
            </form>
            <a href="product_form.php" class="btn-a btn-a-primary"><i class="bi bi-plus-lg ms-1"></i>منتج جديد</a>
        </div>
    </div>
    <div class="card-a-body no-pad">
        <?php if (empty($products)): ?>
            <div class="empty-state"><i class="bi bi-inbox"></i><p>لا توجد منتجات</p></div>
        <?php else: ?>
        <table class="table-a">
            <thead><tr>
                <th style="width:50px">#</th>
                <th>المنتج</th>
                <th>القسم</th>
                <th>السعر</th>
                <th>المخزون</th>
                <th>مميز</th>
                <th>إجراءات</th>
            </tr></thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><strong>#<?= $p['id'] ?></strong></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem">
                            <div class="prod-thumb">
                                <?php if ($img = productImage($p['image'])): ?>
                                    <img src="../<?= $img ?>" alt="">
                                <?php else: ?>
                                    <i class="bi bi-image"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div style="font-weight:600"><?= e($p['name']) ?></div>
                                <div style="font-size:0.75rem;color:var(--muted);max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e(mb_substr($p['description'],0,60)) ?>...</div>
                            </div>
                        </div>
                    </td>
                    <td><span style="color:var(--text-dim);font-size:0.85rem"><?= e($p['cat_name'] ?? '—') ?></span></td>
                    <td style="font-weight:700;color:var(--accent)"><?= p($p['price']) ?></td>
                    <td>
                        <?php if ($p['stock'] <= 0): ?>
                            <span class="badge-a pending">نفد</span>
                        <?php elseif ($p['stock'] <= 10): ?>
                            <span style="color:var(--danger);font-weight:600"><?= $p['stock'] ?></span>
                        <?php else: ?>
                            <span style="color:var(--success);font-weight:600"><?= $p['stock'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($p['featured']): ?>
                            <i class="bi bi-star-fill" style="color:var(--accent)"></i>
                        <?php else: ?>
                            <span style="color:var(--muted)">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:0.4rem">
                            <a href="product_form.php?id=<?= $p['id'] ?>" class="btn-a btn-a-ghost btn-a-sm" title="تعديل"><i class="bi bi-pencil-fill"></i></a>
                            <a href="products.php?delete=<?= $p['id'] ?>" class="btn-a btn-a-danger btn-a-sm delete-btn" title="حذف"><i class="bi bi-trash-fill"></i></a>
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
