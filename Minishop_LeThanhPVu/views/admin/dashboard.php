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

    $newestProducts = $productDAO->getNewest(5);
    $newestOrders = $orderDAO->getNewest(5);
}

$pageTitle = $pageTitle ?? "Dashboard - Quản trị hệ thống";

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$pos = strpos($scriptDir, '/views');
$baseUrl = ($pos !== false) ? substr($scriptDir, 0, $pos) : $scriptDir;
$baseUrl = rtrim($baseUrl, '/');
if ($baseUrl === '') $baseUrl = '/LTW_01/Minishop_LeThanhPVu';

ob_start();
?>

<!-- Tiêu đề trang Dashboard -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark mb-0">Dashboard</h3>
    <span class="badge bg-primary fs-6">Hệ thống quản trị MiniShop</span>
</div>

<!-- Thống kê tổng số (Card) -->
<div class="row g-3 mb-4">
    <div class="col">
        <div class="card stat-card bg-primary text-white p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-6 text-white-50">Danh mục</div>
                    <div class="fs-3 fw-bold"><?= $totalCategory ?? 0 ?></div>
                </div>
                <i class="bi bi-folder fs-1 text-white-50"></i>
            </div>
            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=category&action=index" class="text-white text-decoration-none small mt-2 d-block">Xem chi tiết &rarr;</a>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card bg-info text-white p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-6 text-white-50">Thương hiệu</div>
                    <div class="fs-3 fw-bold"><?= $totalBrand ?? 0 ?></div>
                </div>
                <i class="bi bi-tag fs-1 text-white-50"></i>
            </div>
            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=brand&action=index" class="text-white text-decoration-none small mt-2 d-block">Xem chi tiết &rarr;</a>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card bg-success text-white p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-6 text-white-50">Sản phẩm</div>
                    <div class="fs-3 fw-bold"><?= $totalProduct ?? 0 ?></div>
                </div>
                <i class="bi bi-box-seam fs-1 text-white-50"></i>
            </div>
            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=product&action=index" class="text-white text-decoration-none small mt-2 d-block">Xem chi tiết &rarr;</a>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card bg-warning text-dark p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-6 text-black-50">Khách hàng</div>
                    <div class="fs-3 fw-bold"><?= $totalCustomer ?? 0 ?></div>
                </div>
                <i class="bi bi-people fs-1 text-black-50"></i>
            </div>
            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=customer&action=index" class="text-dark text-decoration-none small mt-2 d-block">Xem chi tiết &rarr;</a>
        </div>
    </div>
    <div class="col">
        <div class="card stat-card bg-danger text-white p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-6 text-white-50">Đơn hàng</div>
                    <div class="fs-3 fw-bold"><?= $totalOrder ?? 0 ?></div>
                </div>
                <i class="bi bi-receipt fs-1 text-white-50"></i>
            </div>
            <a href="<?= $baseUrl ?>/index.php?area=admin&controller=order&action=index" class="text-white text-decoration-none small mt-2 d-block">Xem chi tiết &rarr;</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Danh sách 5 sản phẩm mới nhất -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-primary"><i class="bi bi-box-seam"></i> 05 Sản phẩm mới nhất</h5>
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
                                <tr><td colspan="4" class="text-center text-muted">Chưa có dữ liệu sản phẩm</td></tr>
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
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-danger"><i class="bi bi-receipt"></i> 05 Đơn hàng mới nhất</h5>
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
                                <tr><td colspan="4" class="text-center text-muted">Chưa có dữ liệu đơn hàng</td></tr>
                            <?php else: ?>
                                <?php foreach ($newestOrders as $o): ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($o['order_code']) ?></td>
                                        <td><?= htmlspecialchars($o['customer_name'] ?? 'Khách lẻ') ?></td>
                                        <td class="fw-bold text-success"><?= number_format($o['total_amount'], 0, ',', '.') ?> đ</td>
                                        <td>
                                            <?php if ($o['status'] == 1): ?>
                                                <span class="badge bg-success">Hoàn thành</span>
                                            <?php elseif ($o['status'] == 2): ?>
                                                <span class="badge bg-danger">Đã hủy</span>
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

<?php
$content = ob_get_clean();
include __DIR__ . "/layouts/master.php";
?>
