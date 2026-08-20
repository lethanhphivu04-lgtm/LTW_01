<?php
$pageTitle = $pageTitle ?? "Lập Phiếu Nhập Kho";
ob_start();
?>

<div class="card shadow-sm border-0 max-w-700 mx-auto" style="max-width: 680px;">
    <div class="card-header bg-dark text-white fw-bold py-3">
        <i class="bi bi-box-arrow-in-down me-2 text-success"></i>Lập Phiếu Nhập Kho Sản Phẩm
    </div>
    <div class="card-body p-4">
        <?php if (isset($_SESSION["error"])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $_SESSION["error"] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION["error"]); ?>
        <?php endif; ?>

        <form method="POST" action="<?= $baseUrl ?>/index.php?area=admin&controller=stock&action=create">
            <div class="mb-3">
                <label class="form-label fw-semibold">Chọn sản phẩm nhập kho <span class="text-danger">*</span></label>
                <select name="product_id" id="productSelect" class="form-select border-primary" required>
                    <option value="">-- Chọn sản phẩm trong kho --</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p->id ?>" data-price="<?= $p->price ?>">
                            #<?= $p->id ?> - <?= htmlspecialchars($p->name) ?> (Tồn hiện tại: <?= $p->quantity ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Số lượng nhập thêm <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" id="quantityInput" class="form-control" min="1" value="10" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Đơn giá vốn nhập (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="import_price" id="importPriceInput" class="form-control" min="0" step="1000" placeholder="VD: 1500000" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nhà cung cấp / Đối tác phân phối</label>
                <input type="text" name="supplier_name" class="form-control" placeholder="VD: Công ty CP Phân phối Digiworld, Synnex FPT, Dell VN...">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ghi chú đợt nhập</label>
                <textarea name="note" class="form-control" rows="2" placeholder="VD: Nhập bổ sung đợt đầu tháng, kiểm tra tem niêm phong đầy đủ..."></textarea>
            </div>

            <div class="alert alert-info py-2 small mb-4">
                <i class="bi bi-info-circle-fill me-1"></i> Sau khi lưu phiếu, hệ thống sẽ <strong>tự động cộng dồn số lượng</strong> vào kho của sản phẩm được chọn.
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="<?= $baseUrl ?>/index.php?area=admin&controller=stock&action=index" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-success px-4 fw-bold">
                    <i class="bi bi-check2-circle me-1"></i> Xác nhận nhập kho
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('productSelect')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const price = selected.getAttribute('data-price');
    // Gợi ý giá vốn ước tính = 70% giá niêm yết
    if (price && !document.getElementById('importPriceInput').value) {
        document.getElementById('importPriceInput').value = Math.round(price * 0.75);
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
