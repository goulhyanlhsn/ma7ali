
</main>

<footer class="site-footer mt-5">
    <div class="container">
        <div class="row py-5">
            <div class="col-lg-4 mb-4">
                <h5 class="fw-bold mb-3">
                    <span class="brand-icon" style="width:32px;height:32px;font-size:0.9rem;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;background:var(--gradient-main);color:#fff;margin-left:0.5rem;vertical-align:middle"><i class="bi bi-lightning-charge-fill"></i></span><?= SITE_NAME ?>
                </h5>
                <p style="color:rgba(255,255,255,0.6);line-height:1.7">وجهتك للمنتجات عالية الجودة. اطلب بسهولة واستلم طلبك مباشرة عبر الواتساب.</p>
            </div>
            <div class="col-lg-2 col-md-4 mb-4">
                <h6 class="fw-bold mb-3">التنقل</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" style="color:rgba(255,255,255,0.6);text-decoration:none">الرئيسية</a></li>
                    <li class="mb-2"><a href="cart.php" style="color:rgba(255,255,255,0.6);text-decoration:none">سلة المشتريات</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4 mb-4">
                <h6 class="fw-bold mb-3">الأقسام</h6>
                <ul class="list-unstyled">
                    <?php
                    $footerCats = getCategories();
                    foreach (array_slice($footerCats, 0, 5) as $cat): ?>
                        <li class="mb-2"><a href="index.php?category=<?= sanitize($cat['slug']) ?>" style="color:rgba(255,255,255,0.6);text-decoration:none"><?= sanitize($cat['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4 mb-4">
                <h6 class="fw-bold mb-3">التواصل</h6>
                <ul class="list-unstyled">
                    <li class="mb-2" style="color:rgba(255,255,255,0.6)"><i class="bi bi-telephone ms-2"></i>+212 6 00 00 00 00</li>
                    <li class="mb-2" style="color:rgba(255,255,255,0.6)"><i class="bi bi-envelope ms-2"></i>contact@<?= SITE_NAME ?>.ma</li>
                    <li class="mb-2" style="color:rgba(255,255,255,0.6)"><i class="bi bi-geo-alt ms-2"></i>الدار البيضاء، المغرب</li>
                </ul>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,0.1)">
        <div class="row py-3">
            <div class="col-md-6 text-center text-md-start">
                <p style="color:rgba(255,255,255,0.4)" class="mb-0 small">&copy; <?= date('Y') ?> <?= SITE_NAME ?>. جميع الحقوق محفوظة.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" class="btn btn-sm btn-success" target="_blank">
                    <i class="bi bi-whatsapp ms-1"></i>واتساب
                </a>
                <a href="admin/login.php" class="btn btn-sm btn-outline-secondary ms-2" title="Admin">
                    <i class="bi bi-gear-fill"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
