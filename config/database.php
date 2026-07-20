<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'ma7ali');
define('WHATSAPP_NUMBER', '212600000000');
define('CURRENCY', 'DH');
define('PRODUCTS_PER_PAGE', 6);

function getConnection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Erreur de connexion: " . $e->getMessage());
        }
    }
    return $pdo;
}
