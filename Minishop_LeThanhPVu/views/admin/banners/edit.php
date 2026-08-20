<?php
$pageTitle = $pageTitle ?? "Chỉnh sửa Banner Slider";
ob_start();
?>

<div class="card shadow-sm border-0 max-w-700 mx-auto" style="max-width: 680px;">
    <div class="card-header bg-dark text-white fw-bold py-3">
        <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa Banner Slider #<?= $banner->id ?>
    </div>
    <div class="card-body p-4">
        <?php if (isset($_SESSION["error"])): ?>
            <div class="alert alert-danger"><?= $_SESSION["error"] ?></div>
            <?php unset($_SESSION["error"]); ?>
        <?php endif; ?>

        <form method="POST" action="index.php?area=admin&controller=banner&action=edit&id=<?= $banner->id ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nhãn Badge <span class="text-danger">*</span></label>
                <input type="text" name="badge_text" class="form-control" required value="<?= htmlspecialchars($banner->badgeText) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tiêu đề Banner (Hỗ trợ HTML) <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($banner->title) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Phụ đề mô tả</label>
                <textarea name="subtitle" class="form-control" rows="2"><?= htmlspecialchars($banner->subtitle ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Hình ảnh Banner</label>
                <?php if (!empty($banner->image) && file_exists(__DIR__ . "/../../../uploads/banners/" . $banner->image)): ?>
                    <div class="mb-2 p-2 border rounded bg-light d-flex align-items-center gap-3">
                        <img src="uploads/banners/<?= htmlspecialchars($banner->image) ?>" alt="Banner Image" style="height: 60px; max-width: 150px; object-fit: contain;">
                        <span class="small text-muted">Ảnh hiện tại: <code><?= htmlspecialchars($banner->image) ?></code></span>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/*">
                <small class="text-muted">Chọn file ảnh mới nếu muốn thay đổi ảnh.</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Đường dẫn khi bấm nút (Link URL)</label>
                <input type="text" name="link" class="form-control" value="<?= htmlspecialchars($banner->link) ?>">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Thứ tự hiển thị</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= $banner->sortOrder ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= $banner->status === 1 ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= $banner->status === 0 ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="index.php?area=admin&controller=banner&action=index" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
