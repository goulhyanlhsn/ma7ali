-- ============================================
-- Site Ecommerce - Setup Base de Données
-- ============================================

CREATE DATABASE IF NOT EXISTS ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce;

-- Table Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table Categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Table Products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.png',
    category_id INT,
    stock INT DEFAULT 0,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table Cart
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(255),
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table Orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(150),
    city VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    message TEXT,
    status ENUM('pending','confirmed','shipped','delivered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table Order Items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- Données Example
-- ============================================

-- Catégories
INSERT INTO categories (name, slug) VALUES
('إلكترونيات', 'electronique'),
('ملابس', 'vetements'),
('المنزل', 'maison'),
('رياضة', 'sport'),
('جمال', 'beaute'),
('كتب', 'livres');

-- Produits
INSERT INTO products (name, description, price, image, category_id, stock, featured) VALUES
('هاتف ذكي برو X', 'أحدث هاتف بشاشة OLED 6.5 بوصة، معالج سريع، 128 جيجا تخزين. كاميرا 48 ميجا بيكسل لصور استثنائية.', 4999.00, 'smartphone.jpg', 1, 25, 1),
('سماعات بلوتوث بريميوم', 'سماعات لاسلكية مع خفض الضوضاء النشط، عمر بطارية 30 ساعة، صوت عالي الجودة. مريحة للاستخدام المطول.', 899.00, 'casque.jpg', 1, 50, 1),
('لابتوب ألترا سليم', 'جهاز كمبيوتر محمول 14 بوصة، 16 جيجا رام، 512 جيجا SSD، معالج الجيل الأخير. مثالي للعمل والدراسة.', 7999.00, 'laptop.jpg', 1, 15, 0),
('تيشيرت قطن بريميوم', 'تيشيرت من القطن العضوي 100%، قصّة عصرية، متوفر بعدة أحجام وألوان. مريح وأنيق.', 149.00, 'tshirt.jpg', 2, 200, 1),
('جينز سليم فيت', 'جينز سليم فيت من جينز عالي الجودة، مريح وstylish. مثالي لإطلالة كاجوال.', 349.00, 'jean.jpg', 2, 100, 1),
('جاكيت جلد', 'جاكيت جلد طبيعي، تصميم كلاسيكي وخالد. بطانة داخلية لمزيد من الراحة.', 1299.00, 'veste.jpg', 2, 30, 0),
('مصباح مكتب LED', 'مصباح مكتب عصري مع 3 مستويات إضاءة، شاحن USB مدمج، تصميم بسيط.', 249.00, 'lampe.jpg', 3, 75, 0),
('لحاف مقاوم للحساسية', 'لحاف بريميوم مقاوم للحساسية، حجم 200×200 سم، ناعم وخفيف. مثالي لجميع الفصول.', 399.00, 'couette.jpg', 3, 40, 0),
('كرة قدم', 'كرة قدم رسمية، مقاس 5، مصنوعة من مادة PU عالية الجودة. مثالية للتدريب والمباريات.', 199.00, 'ballon.jpg', 4, 80, 1),
('سجادة يوغا بريميوم', 'سجادة يوغا مقاومة للانزلاق، سماكة 6 مم، مواد صديقة للبيئة. مثالية لليوга والبيلاتس واللياقة.', 299.00, 'yoga.jpg', 4, 60, 0),
('عطر شرقي', 'عطر رجل/امرأة بلمحات شرقية، ثبات طويل 12+ ساعة. زجاجة أنيقة 100 مل.', 599.00, 'parfum.jpg', 5, 45, 1),
('رواية الأكثر مبيعاً', 'رواية الأكثر مبيعاً دولياً، طباعة جيب. قصة مشوقة ستمسكك حتى النهاية.', 79.00, 'livre.jpg', 6, 150, 0);
