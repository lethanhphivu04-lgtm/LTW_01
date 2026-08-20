<?php
$pageTitle = $pageTitle ?? "Chỉnh sửa Mã giảm giá";
ob_start();
?>

<div class="card shadow-sm border-0 max-w-700 mx-auto" style="max-width: 650px;">
    <div class="card-header bg-dark text-white fw-bold py-3">
        <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa Mã giảm giá #<?= $coupon->id ?>
    </div>
    <div class="card-body p-4">
        <?php if (isset($_SESSION["error"])): ?>
            <div class="alert alert-danger"><?= $_SESSION["error"] ?></div>
            <?php unset($_SESSION["error"]); ?>
        <?php endif; ?>

        <form method="POST" action="<?= $baseUrl ?>/index.php?area=admin&controller=coupon&action=edit&id=<?= $coupon->id ?>">
            <div class="mb-3">
                <label class="form-label fw-semibold">Mã giảm giá <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase" required value="<?= htmlspecialchars($coupon->code) ?>">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Loại giảm giá <span class="text-danger">*</span></label>
                    <select name="discount_type" class="form-select" required>
                        <option value="percent" <?= $coupon->discountType === 'percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
                        <option value="fixed" <?= $coupon->discountType === 'fixed' ? 'selected' : '' ?>>Số tiền cố định (VNĐ)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Giá trị giảm <span class="text-danger">*</span></label>
                    <input type="number" step="any" name="discount_value" class="form-control" required value="<?= $coupon->discountValue ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Đơn hàng tối thiểu (VNĐ)</label>
                    <input type="number" name="min_order_value" class="form-control" value="<?= $coupon->minOrderValue ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Giảm tối đa (VNĐ - nếu là %)</label>
                    <input type="number" name="max_discount" class="form-control" value="<?= $coupon->maxDiscount ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Hạn sử dụng</label>
                    <input type="date" name="expiry_date" class="form-control" value="<?= $coupon->expiryDate ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= $coupon->status === 1 ? 'selected' : '' ?>>Kích hoạt (Hoạt động)</option>
                        <option value="0" <?= $coupon->status === 0 ? 'selected' : '' ?>>Tạm khóa</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="<?= $baseUrl ?>/index.php?area=admin&controller=coupon&action=index" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
