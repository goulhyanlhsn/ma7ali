<?php
require_once 'includes.php';
aCheck();
$pdo = getConnection();

$pageTitle = 'لوحة التحكم';
$pageTitleIcon = 'bi-grid-1x2-fill';
include 'header.php';

$totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders   = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
$totalRevenue  = (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ('confirmed','shipped','delivered')")->fetchColumn();
$monthRevenue  = (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ('confirmed','shipped','delivered') AND MONTH(created_at)=MONTH(CURRENT_DATE()) AND YEAR(created_at)=YEAR(CURRENT_DATE())")->fetchColumn();
$recentOrders  = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();

$revData = $pdo->query("
    SELECT DATE(created_at) as d, SUM(total) as t
    FROM orders WHERE status IN ('confirmed','shipped','delivered')
    AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at) ORDER BY d
")->fetchAll();

$statusCounts = $pdo->query("SELECT status, COUNT(*) as c FROM orders GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<?php echo flash(); ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon cyan"><i class="bi bi-currency-dollar"></i></div>
        <div class="stat-info">
            <div class="label">إجمالي الإيرادات</div>
            <div class="value"><?= number_format($totalRevenue, 0) ?> <small style="font-size:0.7em;color:var(--muted)">DH</small></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-receipt"></i></div>
        <div class="stat-info">
            <div class="label">الطلبات</div>
            <div class="value"><?= $totalOrders ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-info">
            <div class="label">قيد الانتظار</div>
            <div class="value"><?= $pendingOrders ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-box-seam"></i></div>
        <div class="stat-info">
            <div class="label">المنتجات</div>
            <div class="value"><?= $totalProducts ?></div>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="card-a">
        <div class="card-a-header">
            <h3><i class="bi bi-graph-up" style="color:var(--primary)"></i>إيرادات آخر 30 يوم</h3>
            <span style="font-size:0.8rem;color:var(--muted)">هذا الشهر: <?= number_format($monthRevenue, 0) ?> DH</span>
        </div>
        <div class="card-a-body">
            <div class="chart-container"><canvas id="revenueChart"></canvas></div>
        </div>
    </div>
    <div class="card-a">
        <div class="card-a-header">
            <h3><i class="bi bi-pie-chart-fill" style="color:var(--accent)"></i>حالة الطلبات</h3>
        </div>
        <div class="card-a-body">
            <div class="chart-container"><canvas id="statusChart"></canvas></div>
        </div>
    </div>
</div>

<div class="card-a">
    <div class="card-a-header">
        <h3><i class="bi bi-clock-history" style="color:var(--primary)"></i>آخر الطلبات</h3>
        <a href="orders.php" class="btn-a btn-a-ghost btn-a-sm">عرض الكل</a>
    </div>
    <div class="card-a-body no-pad">
        <?php if (empty($recentOrders)): ?>
            <div class="empty-state"><i class="bi bi-inbox"></i><p>لا توجد طلبات بعد</p></div>
        <?php else: ?>
        <table class="table-a">
            <thead><tr>
                <th>#</th><th>العميل</th><th>المدينة</th><th>المبلغ</th><th>الحالة</th><th>التاريخ</th>
            </tr></thead>
            <tbody>
            <?php foreach ($recentOrders as $o): ?>
                <tr>
                    <td><strong>#<?= $o['id'] ?></strong></td>
                    <td><?= e($o['full_name']) ?></td>
                    <td><?= e($o['city']) ?></td>
                    <td style="font-weight:700;color:var(--accent)"><?= p($o['total']) ?></td>
                    <td><span class="badge-a <?= $o['status'] ?>"><?= e($o['status']) ?></span></td>
                    <td style="color:var(--muted);font-size:0.82rem"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
const cOpts = { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false} },
    scales:{ x:{ grid:{color:'rgba(255,255,255,0.04)'}, ticks:{color:'#64748b',font:{family:'Tajawal',size:11}} },
             y:{ grid:{color:'rgba(255,255,255,0.04)'}, ticks:{color:'#64748b',font:{family:'Tajawal',size:11}} } } };

new Chart(document.getElementById('revenueChart'), {
    type:'line',
    data:{ labels:<?= json_encode(array_map(fn($r)=>date('d/m', strtotime($r['d'])), $revData)) ?>,
           datasets:[{ data:<?= json_encode(array_map(fn($r)=>(float)$r['t'], $revData)) ?>,
           borderColor:'#06b6d4', backgroundColor:'rgba(6,182,212,0.1)', fill:true, tension:0.4, borderWidth:2, pointRadius:3, pointBackgroundColor:'#06b6d4' }] },
    options:cOpts
});

new Chart(document.getElementById('statusChart'), {
    type:'doughnut',
    data:{ labels:['قيد الانتظار','مؤكد','شحنا','تم التوصيل'],
           datasets:[{ data:[<?= $statusCounts['pending'] ?? 0 ?>, <?= $statusCounts['confirmed'] ?? 0 ?>, <?= $statusCounts['shipped'] ?? 0 ?>, <?= $statusCounts['delivered'] ?? 0 ?>],
           backgroundColor:['#f59e0b','#06b6d4','#8b5cf6','#22c55e'], borderWidth:0 }] },
    options:{ responsive:true, maintainAspectRatio:false, cutout:'65%',
        plugins:{ legend:{ position:'bottom', labels:{ color:'#94a3b8', font:{family:'Tajawal',size:12}, padding:15 } } } }
});
</script>

<?php include 'footer.php'; ?>
