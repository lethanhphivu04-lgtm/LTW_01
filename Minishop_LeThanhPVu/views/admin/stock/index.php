<?php
$pageTitle = $pageTitle ?? "Quản lý Nhập kho";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-box-arrow-in-down text-success me-2"></i>Quản lý Nhập kho (Phiếu nhập)</h3>
        <p class="text-secondary small mb-0">Theo dõi lịch sử nhập hàng, giá vốn và tự động cập nhật số lượng tồn kho</p>
    </div>
    <a href="<?= $baseUrl ?>/index.php?area=admin&controller=stock&action=create" class="btn btn-success fw-bold">
        <i class="bi bi-plus-circle me-1"></i> Lập phiếu nhập kho
    </a>
</div>

<?php if (isset($_SESSION["success"])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION["success"] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION["success"]); ?>
<?php endif; ?>

<!-- Thẻ thống kê tổng vốn nhập -->
<div class="card bg-light border-0 shadow-sm mb-4 p-3 rounded-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-success-subtle text-success rounded-3 fs-3">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <div class="text-secondary small text-uppercase fw-semibold">Tổng vốn nhập hàng:</div>
                <div class="fs-4 fw-bold text-success"><?= number_format($totalCost ?? 0, 0, ',', '.') ?> đ</div>
            </div>
        </div>

        <!-- Form tìm kiếm -->
        <form method="GET" action="<?= $baseUrl ?>/index.php" class="d-flex gap-2">
            <input type="hidden" name="area" value="admin">
            <input type="hidden" name="controller" value="stock">
            <input type="hidden" name="action" value="index">
            <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Tìm theo mã, tên SP, NCC..." value="<?= htmlspecialchars($keyword ?? '') ?>" style="min-width: 260px;">
            <button type="submit" class="btn btn-dark btn-sm"><i class="bi bi-search"></i></button>
            <?php if (!empty($keyword)): ?>
                <a href="<?= $baseUrl ?>/index.php?area=admin&controller=stock&action=index" class="btn btn-outline-secondary btn-sm">Xóa lọc</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>Mã phiếu nhập</th>
                        <th>Sản phẩm</th>
                        <th class="text-center">SL nhập</th>
                        <th class="text-end">Đơn giá nhập</th>
                        <th class="text-end">Tổng tiền vốn</th>
                        <th>Nhà cung cấp</th>
                        <th>Người lập</th>
                        <th>Thời gian</th>
                        <th class="text-center" style="width: 110px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($receipts)): ?>
                        <tr><td colspan="10" class="text-center py-4 text-muted">Chưa có phiếu nhập kho nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($receipts as $idx => $r): ?>
                            <?php $totalItemCost = (float)$r['import_price'] * (int)$r['quantity']; ?>
                            <tr>
                                <td class="text-center text-muted"><?= $idx + 1 ?></td>
                                <td>
                                    <span class="badge bg-secondary font-monospace"><?= htmlspecialchars($r['receipt_code']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($r['product_name']) ?></div>
                                    <small class="text-muted">Tồn kho hiện tại: <span class="badge bg-light text-dark border"><?= $r['current_stock'] ?></span></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success fs-6 px-3">+<?= $r['quantity'] ?></span>
                                </td>
                                <td class="text-end fw-semibold">
                                    <?= number_format($r['import_price'], 0, ',', '.') ?> đ
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    <?= number_format($totalItemCost, 0, ',', '.') ?> đ
                                </td>
                                <td>
                                    <?= htmlspecialchars($r['supplier_name'] ?? 'Không ghi rõ') ?>
                                    <?php if (!empty($r['note'])): ?>
                                        <div class="text-muted small"><em>Ghi chú: <?= htmlspecialchars($r['note']) ?></em></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($r['created_by']) ?></span>
                                </td>
                                <td class="small text-secondary">
                                    <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= $baseUrl ?>/index.php?area=admin&controller=stock&action=receipt&id=<?= $r['id'] ?>" target="_blank" class="btn btn-outline-primary btn-sm" title="Xem & In phiếu nhập A4">
                                        <i class="bi bi-printer"></i> In phiếu
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
