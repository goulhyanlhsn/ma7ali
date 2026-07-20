<?php
session_start();
require_once __DIR__ . '/../config/database.php';

define('ADMIN_EMAIL', 'admin@store.ma');
define('ADMIN_PASS', 'admin123');

function aCheck() {
    if (empty($_SESSION['admin'])) { header("Location: login.php"); exit; }
}
function e($s) { return htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8'); }
function p($p) { return number_format((float)$p, 2, ',', ' ') . ' DH'; }
function flash($t='success',$m='') {
    if ($m!=='') { $_SESSION['aflash']=['t'=>$t,'m'=>$m]; }
    elseif (!empty($_SESSION['aflash'])) {
        $f=$_SESSION['aflash']; unset($_SESSION['aflash']);
        $cls=$f['t']==='error'?'alert-error':'alert-success';
        return '<div class="alert-a '.$cls.'">'.$f['m'].'</div>';
    }
    return '';
}

function productImage($image) {
    if (!$image || $image === 'default.png') return '';
    $path = __DIR__ . '/../uploads/products/' . $image;
    if (file_exists($path)) return 'uploads/products/' . $image;
    return '';
}
