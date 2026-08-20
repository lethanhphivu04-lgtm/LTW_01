<?php
$pageTitle = $pageTitle ?? "Quản lý Mã giảm giá";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark mb-0"><i class="bi bi-ticket-perforated me-2"></i>Quản lý Mã giảm giá (Coupons)</h3>
    <a href="index.php?area=admin&controller=coupon&action=create" class="btn btn-primary fw-bold">
        <i class="bi bi-plus-lg me-1"></i> Thêm mã mới
    </a>
</div>

<?php if (isset($_SESSION["success"])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION["success"] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION["success"]); ?>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 60px;">ID</th>
                        <th>Mã giảm giá</th>
                        <th>Loại giảm</th>
                        <th>Giá trị</th>
                        <th>Đơn tối thiểu</th>
                        <th>Giảm tối đa</th>
                        <th>Hạn sử dụng</th>
                        <th>Trạng thái</th>
                        <th class="text-center" style="width: 130px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($coupons)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">Chưa có mã giảm giá nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($coupons as $c): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $c->id ?></td>
                                <td><span class="badge bg-dark fs-6 font-monospace"><?= htmlspecialchars($c->code) ?></span></td>
                                <td><?= $c->discountType === 'percent' ? 'Phần trăm (%)' : 'Cố định (VNĐ)' ?></td>
                                <td class="fw-bold text-danger">
                                    <?= $c->discountType === 'percent' ? $c->discountValue . '%' : number_format($c->discountValue, 0, ',', '.') . ' đ' ?>
                                </td>
                                <td><?= number_format($c->minOrderValue, 0, ',', '.') ?> đ</td>
                                <td><?= $c->maxDiscount ? number_format($c->maxDiscount, 0, ',', '.') . ' đ' : 'Không giới hạn' ?></td>
                                <td><?= $c->expiryDate ? date('d/m/Y', strtotime($c->expiryDate)) : 'Vô thời hạn' ?></td>
                                <td>
                                    <?php if ($c->status === 1): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tạm khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?area=admin&controller=coupon&action=edit&id=<?= $c->id ?>" class="btn btn-warning btn-sm" title="Sửa"><i class="bi bi-pencil"></i></a>
                                    <a href="index.php?area=admin&controller=coupon&action=delete&id=<?= $c->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa mã này?');" title="Xóa"><i class="bi bi-trash"></i></a>
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
