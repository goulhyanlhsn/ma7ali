<?php
require_once 'includes.php';
aCheck();
$pdo = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $slug = strtolower(trim($_POST['slug'] ?? ''));
        if ($name && $slug) {
            $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)")->execute([$name, $slug]);
            flash('success', 'تم إضافة القسم');
        }
    } elseif ($action === 'edit') {
        $catId = (int)$_POST['cat_id'];
        $name = trim($_POST['name'] ?? '');
        $slug = strtolower(trim($_POST['slug'] ?? ''));
        if ($name && $slug && $catId) {
            $pdo->prepare("UPDATE categories SET name=?, slug=? WHERE id=?")->execute([$name, $slug, $catId]);
            flash('success', 'تم تحديث القسم');
        }
    }
    header("Location: categories.php");
    exit;
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_GET['delete']]);
    flash('success', 'تم حذف القسم');
    header("Location: categories.php");
    exit;
}

$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id=c.id) as prod_count FROM categories c ORDER BY c.name")->fetchAll();

$pageTitle = 'الأقسام';
$pageTitleIcon = 'bi-grid-3x3-gap-fill';
include 'header.php';
echo flash();
?>

<div style="display:grid;grid-template-columns:1fr 1.5fr;gap:1.5rem">
    <div class="card-a">
        <div class="card-a-header">
            <h3><i class="bi bi-plus-circle-fill" style="color:var(--primary)"></i>إضافة قسم جديد</h3>
        </div>
        <div class="card-a-body">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>اسم القسم *</label>
                    <input type="text" name="name" class="form-control" placeholder="مثال: إلكترونيات" required>
                </div>
                <div class="form-group">
                    <label>الرابط (Slug) *</label>
                    <input type="text" name="slug" class="form-control" placeholder="مثال: electronique" dir="ltr" style="text-align:left" required>
                </div>
                <button type="submit" class="btn-a btn-a-primary" style="width:100%;justify-content:center"><i class="bi bi-plus-lg ms-1"></i>إضافة القسم</button>
            </form>
        </div>
    </div>

    <div class="card-a">
        <div class="card-a-header">
            <h3><i class="bi bi-grid-3x3-gap" style="color:var(--accent)"></i>الأقسام (<?= count($categories) ?>)</h3>
        </div>
        <div class="card-a-body no-pad">
            <?php if (empty($categories)): ?>
                <div class="empty-state"><i class="bi bi-inbox"></i><p>لا توجد أقسام</p></div>
            <?php else: ?>
            <table class="table-a">
                <thead><tr><th>#</th><th>الاسم</th><th>الرابط</th><th>المنتجات</th><th>إجراءات</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr>
                        <td><strong>#<?= $c['id'] ?></strong></td>
                        <td style="font-weight:600"><?= e($c['name']) ?></td>
                        <td style="color:var(--muted);direction:ltr;text-align:right;font-size:0.85rem"><?= e($c['slug']) ?></td>
                        <td><span class="badge-a confirmed"><?= (int)$c['prod_count'] ?> منتج</span></td>
                        <td>
                            <div style="display:flex;gap:0.4rem">
                                <a href="categories.php?delete=<?= $c['id'] ?>" class="btn-a btn-a-danger btn-a-sm delete-btn" title="حذف"><i class="bi bi-trash-fill"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
