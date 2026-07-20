<?php
require_once 'includes.php';
aCheck();
$pdo = getConnection();

$uploadDir = __DIR__ . '/../uploads/products/';
if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$product = null;
$isEdit = false;

if (isset($_GET['id'])) {
    $isEdit = true;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([(int)$_GET['id']]);
    $product = $stmt->fetch();
    if (!$product) { header("Location: products.php"); exit; }
}

$errors = [];
$imageFile = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $stock       = (int)($_POST['stock'] ?? 0);
    $featured    = isset($_POST['featured']) ? 1 : 0;

    if (!$name) $errors[] = 'اسم المنتج مطلوب';
    if ($price <= 0) $errors[] = 'السعر يجب أن يكون أكبر من 0';

    $imageName = $product['image'] ?? 'default.png';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed = ['image/jpeg','image/png','image/gif','image/webp','image/svg+xml'];
        $maxSize = 5 * 1024 * 1024;

        if (!in_array($file['type'], $allowed)) {
            $errors[] = 'صيغة الصورة غير مدعومة (JPG, PNG, GIF, WebP)';
        } elseif ($file['size'] > $maxSize) {
            $errors[] = 'حجم الصورة يتجاوز 5 ميجا';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imageName = 'prod_' . time() . '_' . mt_rand(1000,9999) . '.' . strtolower($ext);
            if (!move_uploaded_file($file['tmp_name'], $uploadDir . $imageName)) {
                $errors[] = 'فشل في رفع الصورة';
            } elseif ($isEdit && $product['image'] !== 'default.png' && file_exists($uploadDir . $product['image'])) {
                unlink($uploadDir . $product['image']);
            }
        }
    }

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, image=?, category_id=?, stock=?, featured=? WHERE id=?");
            $stmt->execute([$name, $description, $price, $imageName, $category_id ?: null, $stock, $featured, $product['id']]);
            flash('success', 'تم تحديث المنتج');
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, category_id, stock, featured) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$name, $description, $price, $imageName, $category_id ?: null, $stock, $featured]);
            flash('success', 'تم إضافة المنتج');
        }
        header("Location: products.php");
        exit;
    }
}

$pageTitle = $isEdit ? 'تعديل المنتج' : 'إضافة منتج جديد';
$pageTitleIcon = 'bi-box-seam-fill';
include 'header.php';
echo flash();

if ($errors): ?>
    <div class="alert-a error"><?= implode('<br>', array_map('e', $errors)) ?></div>
<?php endif; ?>

<style>
.upload-zone {
    border: 2px dashed var(--border);
    border-radius: 14px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: var(--surface-2);
    position: relative;
}
.upload-zone:hover, .upload-zone.dragover {
    border-color: var(--primary);
    background: var(--primary-glow);
}
.upload-zone .upload-icon {
    font-size: 2.5rem;
    color: var(--primary);
    margin-bottom: 0.75rem;
    display: block;
}
.upload-zone .upload-text {
    font-size: 0.9rem;
    color: var(--text-dim);
}
.upload-zone .upload-hint {
    font-size: 0.75rem;
    color: var(--muted);
    margin-top: 0.4rem;
}
.upload-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
}
.upload-preview {
    display: none;
    position: relative;
    width: 160px;
    height: 160px;
    border-radius: 14px;
    overflow: hidden;
    margin: 0 auto 1rem;
    border: 2px solid var(--border);
}
.upload-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.upload-preview .remove-img {
    position: absolute;
    top: 6px;
    left: 6px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--danger);
    color: #fff;
    border: none;
    font-size: 0.8rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.upload-preview .remove-img:hover { background: #dc2626; }
.current-img-label {
    font-size: 0.78rem;
    color: var(--muted);
    margin-bottom: 0.5rem;
    display: block;
}
</style>

<div class="card-a">
    <div class="card-a-header">
        <h3><i class="bi <?= $isEdit ? 'bi-pencil' : 'bi-plus-circle' ?>" style="color:var(--primary)"></i><?= $pageTitle ?></h3>
        <a href="products.php" class="btn-a btn-a-ghost btn-a-sm"><i class="bi bi-arrow-right ms-1"></i>رجوع</a>
    </div>
    <div class="card-a-body">
        <form method="POST" enctype="multipart/form-data">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label>اسم المنتج *</label>
                    <input type="text" name="name" class="form-control" value="<?= e($product['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>السعر (DH) *</label>
                    <input type="number" name="price" class="form-control" step="0.01" min="0" value="<?= $product['price'] ?? '' ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>الوصف</label>
                <textarea name="description" class="form-control" rows="4"><?= e($product['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>صورة المنتج</label>
                <?php if ($isEdit && $product['image'] && $product['image'] !== 'default.png'): ?>
                    <span class="current-img-label">الصورة الحالية:</span>
                    <div class="upload-preview" id="currentPreview" style="display:block">
                        <img src="../uploads/products/<?= e($product['image']) ?>" alt="">
                    </div>
                <?php endif; ?>
                <div class="upload-preview" id="newPreview">
                    <img id="previewImg" src="" alt="">
                    <button type="button" class="remove-img" onclick="removePreview()"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="upload-zone" id="uploadZone">
                    <i class="bi bi-cloud-arrow-up-fill upload-icon"></i>
                    <div class="upload-text">اسحب الصورة هنا أو اضغط لاختيار صورة</div>
                    <div class="upload-hint">JPG, PNG, GIF, WebP — حد أقصى 5 ميجا</div>
                    <input type="file" name="image" id="imageInput" accept="image/*">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label>القسم</label>
                    <select name="category_id" class="form-control">
                        <option value="0">— بدون قسم —</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($product['category_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>المخزون</label>
                    <input type="number" name="stock" class="form-control" min="0" value="<?= $product['stock'] ?? 0 ?>">
                </div>
                <div class="form-group">
                    <label class="form-check" style="margin-top:1.8rem">
                        <input type="checkbox" name="featured" value="1" <?= ($product['featured'] ?? 0) ? 'checked' : '' ?>>
                        <span>منتج مميز</span>
                    </label>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1.5rem">
                <button type="submit" class="btn-a btn-a-primary"><i class="bi bi-check-lg ms-1"></i><?= $isEdit ? 'تحديث' : 'إضافة' ?></button>
                <a href="products.php" class="btn-a btn-a-ghost">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<script>
const zone = document.getElementById('uploadZone');
const input = document.getElementById('imageInput');
const preview = document.getElementById('newPreview');
const previewImg = document.getElementById('previewImg');
const currentPreview = document.getElementById('currentPreview');

input.addEventListener('change', function() {
    if (this.files && this.files[0]) showPreview(this.files[0]);
});

zone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
zone.addEventListener('dragleave', function() { this.classList.remove('dragover'); });
zone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        input.files = e.dataTransfer.files;
        showPreview(e.dataTransfer.files[0]);
    }
});

function showPreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImg.src = e.target.result;
        preview.style.display = 'block';
        if (currentPreview) currentPreview.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function removePreview() {
    input.value = '';
    preview.style.display = 'none';
    previewImg.src = '';
    if (currentPreview) currentPreview.style.display = 'block';
}
</script>

<?php include 'footer.php'; ?>
