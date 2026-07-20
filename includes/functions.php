<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';

function formatPrice($price) {
    return number_format((float)$price, 2, ',', ' ') . ' ' . CURRENCY;
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function flashMessage($type = 'success', $message = '') {
    if ($message !== '') {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    } elseif (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $class = $flash['type'] === 'error' ? 'alert-danger' : 'alert-success';
        return '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">'
             . sanitize($flash['message'])
             . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
    return '';
}

function getCartCount() {
    $pdo = getConnection();
    $sid = session_id();
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as total FROM cart WHERE session_id = ?");
    $stmt->execute([$sid]);
    return (int)$stmt->fetch()['total'];
}

function addToCart($productId, $quantity = 1) {
    $pdo = getConnection();
    $sid = session_id();
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$sid, $productId]);
    $existing = $stmt->fetch();
    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$newQty, $existing['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$sid, $productId, $quantity]);
    }
}

function getCartItems() {
    $pdo = getConnection();
    $sid = session_id();
    $stmt = $pdo->prepare("
        SELECT c.id as cart_id, c.quantity, p.id, p.name, p.price, p.image
        FROM cart c JOIN products p ON c.product_id = p.id
        WHERE c.session_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$sid]);
    return $stmt->fetchAll();
}

function getCartTotal() {
    $items = getCartItems();
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function updateCartItem($cartId, $quantity) {
    $pdo = getConnection();
    if ($quantity <= 0) {
        removeFromCart($cartId);
        return;
    }
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([(int)$quantity, (int)$cartId]);
}

function removeFromCart($cartId) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->execute([(int)$cartId]);
}

function clearCart() {
    $pdo = getConnection();
    $sid = session_id();
    $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
    $stmt->execute([$sid]);
}

function generateWhatsAppLink($items, $fullName, $phone, $email, $city, $address, $message = '') {
    $total = 0;
    $lines = [];
    $lines[] = "🛒 *طلب جديد - " . SITE_NAME . "*";
    $lines[] = "━━━━━━━━━━━━━━━━━━━━";
    $lines[] = "👤 *العميل:* " . $fullName;
    $lines[] = "📱 *الهاتف:* " . $phone;
    if ($email) $lines[] = "📧 *البريد:* " . $email;
    $lines[] = "📍 *المدينة:* " . $city;
    $lines[] = "🏠 *العنوان:* " . $address;
    $lines[] = "━━━━━━━━━━━━━━━━━━━━";
    $lines[] = "📦 *المنتجات:*";
    foreach ($items as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        $lines[] = "• " . $item['name'] . " × " . $item['quantity'] . " = " . formatPrice($subtotal);
    }
    $lines[] = "━━━━━━━━━━━━━━━━━━━━";
    $lines[] = "💰 *الإجمالي:* " . formatPrice($total);
    if ($message) {
        $lines[] = "💬 *رسالة:* " . $message;
    }
    $text = implode("\n", $lines);
    $encoded = urlencode($text);
    return "https://wa.me/" . WHATSAPP_NUMBER . "?text=" . $encoded;
}

function getProduct($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug
        FROM products p LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?
    ");
    $stmt->execute([(int)$id]);
    return $stmt->fetch();
}

function getProducts($page = 1, $perPage = PRODUCTS_PER_PAGE, $search = '', $category = '') {
    $pdo = getConnection();
    $where = "WHERE 1=1";
    $params = [];
    if ($search) {
        $where .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    if ($category) {
        $where .= " AND c.slug = ?";
        $params[] = $category;
    }
    $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM products p LEFT JOIN categories c ON p.category_id = c.id $where");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetch()['total'];
    $totalPages = max(1, ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    $params[] = $offset;
    $params[] = $perPage;
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug
        FROM products p LEFT JOIN categories c ON p.category_id = c.id
        $where
        ORDER BY p.created_at DESC
        LIMIT ?, ?
    ");
    $stmt->execute($params);
    return [
        'products' => $stmt->fetchAll(),
        'total' => $total,
        'totalPages' => $totalPages,
        'currentPage' => $page,
    ];
}

function getCategories() {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count FROM categories c ORDER BY c.name");
    return $stmt->fetchAll();
}

function getFeaturedProducts() {
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name
        FROM products p LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.featured = 1 AND p.stock > 0
        ORDER BY p.created_at DESC LIMIT 4
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getRelatedProducts($categoryId, $productId, $limit = 4) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name
        FROM products p LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = ? AND p.id != ?
        ORDER BY RAND() LIMIT ?
    ");
    $stmt->execute([(int)$categoryId, (int)$productId, (int)$limit]);
    return $stmt->fetchAll();
}

function productImage($image) {
    if (!$image || $image === 'default.png') return '';
    $path = __DIR__ . '/../uploads/products/' . $image;
    if (file_exists($path)) return 'uploads/products/' . $image;
    return '';
}
