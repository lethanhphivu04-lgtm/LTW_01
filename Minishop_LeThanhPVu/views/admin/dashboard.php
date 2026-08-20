<?php
if (!isset($totalCategory)) {
    if (!class_exists('DAO\CategoryDAO')) {
        require_once __DIR__ . "/../../autoload.php";
    }
    $categoryDAO = new \DAO\CategoryDAO();
    $brandDAO = new \DAO\BrandDAO();
    $productDAO = new \DAO\ProductDAO();
    $customerDAO = new \DAO\CustomerDAO();
    $orderDAO = new \DAO\OrderDAO();

    $totalCategory = $categoryDAO->countAll();
    $totalBrand = $brandDAO->countAll();
    $totalProduct = $productDAO->countAll();
    $totalCustomer = $customerDAO->countAll();
    $totalOrder = $orderDAO->countAll();
    $totalRevenue = $orderDAO->getTotalRevenue();

    $revenue7Days = $orderDAO->getRevenueLast7Days();
    $paymentStats = $orderDAO->getPaymentMethodStats();
    $statusStats = $orderDAO->getOrderStatusStats();

    $newestProducts = $productDAO->getNewest(5);
    $newestOrders = $orderDAO->getNewest(5);
}

$pageTitle = $pageTitle ?? "Dashboard - Quản trị hệ thống";

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$pos = strpos($scriptDir, '/views');
$baseUrl = ($pos !== false) ? substr($scriptDir, 0, $pos) : $scriptDir;
$baseUrl = rtrim($baseUrl, '/');
if ($baseUrl === '') $baseUrl = '/LTW_01/Minishop_LeThanhPVu';

// Chuẩn bị dữ liệu cho Chart.js 7 ngày
$dates7Days = [];
$rev7Days = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $dates7Days[$d] = date('d/m', strtotime($d));
    $rev7Days[$d] = 0;
}
if (!empty($revenue7Days)) {
    foreach ($revenue7Days as $r) {
        $od = $r['order_date'] ?? '';
        if (isset($rev7Days[$od])) {
            $rev7Days[$od] = (float)$r['daily_revenue'];
        }
    }
}
$chartLabels = array_values($dates7Days);
$chartData = array_values($rev7Days);

// Chuẩn bị dữ liệu phương thức thanh toán
$codCount = 0;
$vnpayCount = 0;
if (!empty($paymentStats)) {
    foreach ($paymentStats as $ps) {
        if (strtolower($ps['payment_method'] ?? '') === 'vnpay') {
            $vnpayCount += (int)$ps['total_orders'];
        } else {
            $codCount += (int)$ps['total_orders'];
        }
    }
}
if ($codCount === 0 && $vnpayCount === 0) {
    $codCount = 1; // Giá trị mặc định hiển thị
}

ob_start();
?>

<!-- Tiêu đề trang Dashboard -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-speedometer2 text-primary me-2"></i>Tổng quan hệ thống</h3>
        <p class="text-secondary small mb-0">Thống kê dữ liệu kinh doanh và báo cáo trực quan thời gian thực</p>
    </div>
    <span class="badge bg-dark px-3 py-2 fs-6"><i class="bi bi-shield-check me-1 text-success"></i>Hệ thống MiniShop</span>
</div>

<!-- Thống kê tổng số (Stat Cards) -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-6 g-3 mb-4">
    <div class="col">
        <div class="card stat-card bg-primary text-white p-3 shadow-sm h-100 rounded-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-6 text-white-50">Danh mục</div>
                    <div class="fs-4 fw-bold"><?= $totalCategory ?? 0 ?></div>
                </div>
                <i class="bi bi-folder fs-1 text-white-50"></i>
            </div>
            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=category&action=index" class="text-white text-decoration-none small mt-2 d-block opacity-75">Chi tiết &rarr;</a>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card bg-info text-white p-3 shadow-sm h-100 rounded-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-6 text-white-50">Thương hiệu</div>
                    <div class="fs-4 fw-bold"><?= $totalBrand ?? 0 ?></div>
                </div>
                <i class="bi bi-tag fs-1 text-white-50"></i>
            </div>
            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=brand&action=index" class="text-white text-decoration-none small mt-2 d-block opacity-75">Chi tiết &rarr;</a>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card bg-secondary text-white p-3 shadow-sm h-100 rounded-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-6 text-white-50">Sản phẩm</div>
                    <div class="fs-4 fw-bold"><?= $totalProduct ?? 0 ?></div>
                </div>
                <i class="bi bi-box-seam fs-1 text-white-50"></i>
            </div>
            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=product&action=index" class="text-white text-decoration-none small mt-2 d-block opacity-75">Chi tiết &rarr;</a>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card bg-warning text-dark p-3 shadow-sm h-100 rounded-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-6 text-black-50">Khách hàng</div>
                    <div class="fs-4 fw-bold"><?= $totalCustomer ?? 0 ?></div>
                </div>
                <i class="bi bi-people fs-1 text-black-50"></i>
            </div>
            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=customer&action=index" class="text-dark text-decoration-none small mt-2 d-block opacity-75">Chi tiết &rarr;</a>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card bg-danger text-white p-3 shadow-sm h-100 rounded-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-6 text-white-50">Đơn hàng</div>
                    <div class="fs-4 fw-bold"><?= $totalOrder ?? 0 ?></div>
                </div>
                <i class="bi bi-receipt fs-1 text-white-50"></i>
            </div>
            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=order&action=index" class="text-white text-decoration-none small mt-2 d-block opacity-75">Chi tiết &rarr;</a>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card text-white p-3 shadow-sm h-100 rounded-3" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-6 text-white-50">Tổng doanh thu</div>
                    <div class="fs-5 fw-bold text-truncate" title="<?= number_format($totalRevenue ?? 0, 0, ',', '.') ?> đ">
                        <?= number_format($totalRevenue ?? 0, 0, ',', '.') ?> đ
                    </div>
                </div>
                <i class="bi bi-cash-coin fs-1 text-white-50"></i>
            </div>
            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=order&action=index" class="text-white text-decoration-none small mt-2 d-block opacity-75">Báo cáo &rarr;</a>
        </div>
    </div>
</div>

<!-- Khu vực Biểu đồ Phân tích (Charts) -->
<div class="row g-4 mb-4">
    <!-- Biểu đồ Doanh thu 7 ngày -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100 rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-graph-up text-primary me-2"></i>Biểu đồ Doanh thu 7 ngày gần nhất (VNĐ)
                </h5>
                <span class="badge bg-primary-subtle text-primary fw-semibold">Cập nhật tự động</span>
            </div>
            <div class="card-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ Tỷ lệ Thanh toán -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100 rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-pie-chart text-success me-2"></i>Phương thức thanh toán
                </h5>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="height: 220px; width: 100%; position: relative;">
                    <canvas id="paymentChart"></canvas>
                </div>
                <div class="d-flex gap-4 justify-content-center mt-3 text-center small">
                    <div>
                        <span class="d-inline-block rounded-circle bg-secondary me-1" style="width: 10px; height: 10px;"></span>
                        <strong>COD:</strong> <?= $codCount ?> đơn
                    </div>
                    <div>
                        <span class="d-inline-block rounded-circle bg-primary me-1" style="width: 10px; height: 10px;"></span>
                        <strong>VNPay:</strong> <?= $vnpayCount ?> đơn
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Danh sách 5 sản phẩm mới nhất -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0 text-primary"><i class="bi bi-box-seam me-2"></i>05 Sản phẩm mới nhất</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Giá bán</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($newestProducts)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Chưa có dữ liệu sản phẩm</td></tr>
                            <?php else: ?>
                                <?php foreach ($newestProducts as $index => $p): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($p['proname']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name']) ?></span></td>
                                        <td class="text-danger fw-bold"><?= number_format($p['discount_price'], 0, ',', '.') ?> đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách 5 đơn hàng mới nhất -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0 text-danger"><i class="bi bi-receipt me-2"></i>05 Đơn hàng mới nhất</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($newestOrders)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Chưa có dữ liệu đơn hàng</td></tr>
                            <?php else: ?>
                                <?php foreach ($newestOrders as $o): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=order&action=detail&id=<?= $o['id'] ?>" class="fw-bold text-primary text-decoration-none">
                                                #<?= htmlspecialchars($o['order_code']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($o['customer_name'] ?? 'Khách lẻ') ?></td>
                                        <td class="fw-bold text-success"><?= number_format($o['total_amount'], 0, ',', '.') ?> đ</td>
                                        <td>
                                            <?php if ($o['status'] == 3): ?>
                                                <span class="badge bg-success">Hoàn thành</span>
                                            <?php elseif ($o['status'] == 4): ?>
                                                <span class="badge bg-danger">Đã hủy</span>
                                            <?php elseif ($o['status'] == 1): ?>
                                                <span class="badge bg-info text-white">Đã xác nhận</span>
                                            <?php elseif ($o['status'] == 2): ?>
                                                <span class="badge bg-primary">Đang giao</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN và Khởi tạo biểu đồ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Biểu đồ Doanh thu 7 ngày (Line + Bar Chart)
    const ctxRev = document.getElementById('revenueChart');
    if (ctxRev) {
        new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: <?= json_encode($chartData) ?>,
                    backgroundColor: 'rgba(59, 130, 246, 0.65)',
                    borderColor: '#2563eb',
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Biểu đồ Phương thức thanh toán (Doughnut Chart)
    const ctxPay = document.getElementById('paymentChart');
    if (ctxPay) {
        new Chart(ctxPay, {
            type: 'doughnut',
            data: {
                labels: ['Tiền mặt (COD)', 'VNPay Online'],
                datasets: [{
                    data: [<?= $codCount ?>, <?= $vnpayCount ?>],
                    backgroundColor: ['#64748b', '#0284c7'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/layouts/master.php";
?>
