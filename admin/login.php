<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes.php';

if (!empty($_SESSION['admin'])) { header("Location: index.php"); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if ($email === ADMIN_EMAIL && $pass === ADMIN_PASS) {
        $_SESSION['admin'] = true;
        header("Location: index.php");
        exit;
    }
    $error = 'بيانات الدخول غير صحيحة';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول لوحة التحكم - <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Tajawal', sans-serif;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: #0a0e17;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(6,182,212,0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(245,158,11,0.06) 0%, transparent 50%);
            overflow: hidden;
        }
        .login-bg { position:fixed; inset:0; z-index:0; }
        .login-bg .orb {
            position:absolute; border-radius:50%; filter:blur(80px); opacity:0.15; animation:float 20s ease-in-out infinite;
        }
        .login-bg .orb:nth-child(1){ width:400px;height:400px;background:#06b6d4;top:-100px;right:-100px; }
        .login-bg .orb:nth-child(2){ width:300px;height:300px;background:#f59e0b;bottom:-50px;left:-50px;animation-delay:-7s; }
        .login-bg .orb:nth-child(3){ width:250px;height:250px;background:#8b5cf6;top:50%;left:50%;animation-delay:-14s; }
        @keyframes float { 0%,100%{transform:translate(0,0)} 50%{transform:translate(30px,-30px)} }
        .login-card {
            position:relative; z-index:1;
            width:420px; max-width:90vw;
            background: rgba(15,23,42,0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        }
        .login-logo {
            text-align:center; margin-bottom:2rem;
        }
        .login-logo .icon {
            width:64px; height:64px; border-radius:16px; margin:0 auto 1rem;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            display:flex; align-items:center; justify-content:center;
            font-size:1.8rem; color:#fff;
            box-shadow: 0 8px 24px rgba(6,182,212,0.3);
        }
        .login-logo h1 {
            font-size:1.4rem; font-weight:800; color:#e2e8f0;
        }
        .login-logo p { font-size:0.85rem; color:#64748b; margin-top:0.3rem; }
        .form-g { margin-bottom:1.2rem; }
        .form-g label { display:block; font-size:0.8rem; font-weight:700; color:#94a3b8; margin-bottom:0.4rem; }
        .form-g input {
            width:100%; padding:0.75rem 1rem;
            background: rgba(30,41,59,0.8);
            border:1px solid rgba(255,255,255,0.08);
            border-radius:10px; color:#e2e8f0;
            font-family:inherit; font-size:0.9rem;
            transition: border-color 0.2s;
        }
        .form-g input:focus { outline:none; border-color:#06b6d4; box-shadow:0 0 0 3px rgba(6,182,212,0.15); }
        .form-g input::placeholder { color:#475569; }
        .btn-login {
            width:100%; padding:0.8rem;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            border:none; border-radius:10px;
            color:#fff; font-family:inherit; font-size:1rem; font-weight:700;
            cursor:pointer; transition:all 0.2s;
            margin-top:0.5rem;
        }
        .btn-login:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(6,182,212,0.35); }
        .alert-login {
            background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2);
            border-radius:10px; padding:0.7rem 1rem; margin-bottom:1.2rem;
            color:#fca5a5; font-size:0.85rem; text-align:center;
        }
        .back-link {
            text-align:center; margin-top:1.5rem;
        }
        .back-link a { color:#64748b; text-decoration:none; font-size:0.8rem; transition:color 0.2s; }
        .back-link a:hover { color:#06b6d4; }
    </style>
</head>
<body>
    <div class="login-bg">
        <div class="orb"></div><div class="orb"></div><div class="orb"></div>
    </div>
    <div class="login-card">
        <div class="login-logo">
            <div class="icon"><i class="bi bi-shield-lock-fill"></i></div>
            <h1><?= SITE_NAME ?></h1>
            <p>لوحة التحكم - تسجيل الدخول</p>
        </div>
        <?php if ($error): ?>
            <div class="alert-login"><i class="bi bi-exclamation-circle ms-1"></i><?= e($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-g">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" placeholder="admin@store.ma" value="admin@store.ma" required autofocus>
            </div>
            <div class="form-g">
                <label>كلمة المرور</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-left ms-2"></i>دخول</button>
        </form>
        <div class="back-link"><a href="../index.php"><i class="bi bi-arrow-right ms-1"></i>العودة للمتجر</a></div>
    </div>
</body>
</html>
