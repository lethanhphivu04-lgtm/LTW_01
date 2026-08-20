<?php
$pageTitle = $pageTitle ?? "Thêm mới Mã giảm giá";
ob_start();
?>

<div class="card shadow-sm border-0 max-w-700 mx-auto" style="max-width: 650px;">
    <div class="card-header bg-dark text-white fw-bold py-3">
        <i class="bi bi-plus-circle me-2"></i>Thêm mới Mã giảm giá
    </div>
    <div class="card-body p-4">
        <?php if (isset($_SESSION["error"])): ?>
            <div class="alert alert-danger"><?= $_SESSION["error"] ?></div>
            <?php unset($_SESSION["error"]); ?>
        <?php endif; ?>

        <form method="POST" action="index.php?area=admin&controller=coupon&action=create">
            <div class="mb-3">
                <label class="form-label fw-semibold">Mã giảm giá <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase" required placeholder="VD: SALE10, GIAM50K">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Loại giảm giá <span class="text-danger">*</span></label>
                    <select name="discount_type" class="form-select" required>
                        <option value="percent">Phần trăm (%)</option>
                        <option value="fixed">Số tiền cố định (VNĐ)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Giá trị giảm <span class="text-danger">*</span></label>
                    <input type="number" step="any" name="discount_value" class="form-control" required placeholder="VD: 10 (cho 10%) hoặc 50000">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Đơn hàng tối thiểu (VNĐ)</label>
                    <input type="number" name="min_order_value" class="form-control" value="0" placeholder="VD: 300000">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Giảm tối đa (VNĐ - nếu là %)</label>
                    <input type="number" name="max_discount" class="form-control" placeholder="Để trống nếu không giới hạn">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Hạn sử dụng</label>
                    <input type="date" name="expiry_date" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" selected>Kích hoạt (Hoạt động)</option>
                        <option value="0">Tạm khóa</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="index.php?area=admin&controller=coupon&action=index" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Lưu mã</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
