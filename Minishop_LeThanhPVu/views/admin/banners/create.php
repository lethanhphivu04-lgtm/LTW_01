<?php
$pageTitle = $pageTitle ?? "Thêm mới Banner Slider";
ob_start();
?>

<div class="card shadow-sm border-0 max-w-700 mx-auto" style="max-width: 680px;">
    <div class="card-header bg-dark text-white fw-bold py-3">
        <i class="bi bi-plus-circle me-2"></i>Thêm mới Banner Slider
    </div>
    <div class="card-body p-4">
        <?php if (isset($_SESSION["error"])): ?>
            <div class="alert alert-danger"><?= $_SESSION["error"] ?></div>
            <?php unset($_SESSION["error"]); ?>
        <?php endif; ?>

        <form method="POST" action="index.php?area=admin&controller=banner&action=create" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nhãn Badge <span class="text-danger">*</span></label>
                <input type="text" name="badge_text" class="form-control" required placeholder="VD: 🔥 Siêu khuyến mãi, ⚡ Khám phá ngay">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tiêu đề Banner (Hỗ trợ HTML) <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" required placeholder="VD: CÔNG NGHỆ ĐỈNH CAO.<br><span class='text-light'>THIẾT KẾ TINH TẾ.</span>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Phụ đề mô tả</label>
                <textarea name="subtitle" class="form-control" rows="2" placeholder="VD: Khám phá các thiết bị và phụ kiện công nghệ cao cấp chính hãng..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Hình ảnh minh họa Banner (Tùy chọn)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <small class="text-muted">Định dạng hỗ trợ: JPG, PNG, WEBP, GIF (Nếu để trống hệ thống sẽ hiển thị icon công nghệ đẹp mắt).</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Đường dẫn khi bấm nút (Link URL)</label>
                <input type="text" name="link" class="form-control" value="/products" placeholder="VD: /products hoặc /category/chuot-gaming">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Thứ tự hiển thị</label>
                    <input type="number" name="sort_order" class="form-control" value="0" placeholder="Số nhỏ hơn sẽ hiển thị trước">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" selected>Hiển thị</option>
                        <option value="0">Ẩn</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="index.php?area=admin&controller=banner&action=index" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Lưu Banner</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
