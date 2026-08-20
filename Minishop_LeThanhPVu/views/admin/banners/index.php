<?php
$pageTitle = $pageTitle ?? "Quản lý Banner Slider";
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark mb-0"><i class="bi bi-images me-2"></i>Quản lý Banner Slider Trang Chủ</h3>
    <a href="index.php?area=admin&controller=banner&action=create" class="btn btn-primary fw-bold">
        <i class="bi bi-plus-lg me-1"></i> Thêm banner mới
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
                        <th style="width: 80px;">Hình ảnh</th>
                        <th>Nhãn (Badge)</th>
                        <th>Tiêu đề Banner</th>
                        <th>Phụ đề</th>
                        <th>Liên kết</th>
                        <th>Thứ tự</th>
                        <th>Trạng thái</th>
                        <th class="text-center" style="width: 130px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($banners)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">Chưa có banner nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($banners as $b): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $b->id ?></td>
                                <td>
                                    <?php if (!empty($b->image) && file_exists(__DIR__ . "/../../../uploads/banners/" . $b->image)): ?>
                                        <img src="uploads/banners/<?= htmlspecialchars($b->image) ?>" alt="Banner" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div class="bg-light text-secondary d-flex align-items-center justify-content-center" style="width:60px; height:40px; border-radius:4px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($b->badgeText) ?></span></td>
                                <td class="fw-semibold"><?= strip_tags($b->title) ?></td>
                                <td class="text-muted small text-truncate" style="max-width: 200px;"><?= htmlspecialchars($b->subtitle ?? '') ?></td>
                                <td><code><?= htmlspecialchars($b->link) ?></code></td>
                                <td class="text-center fw-bold"><?= $b->sortOrder ?></td>
                                <td>
                                    <?php if ($b->status === 1): ?>
                                        <span class="badge bg-success">Hiển thị</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Ẩn</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?area=admin&controller=banner&action=edit&id=<?= $b->id ?>" class="btn btn-warning btn-sm me-1" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="index.php?area=admin&controller=banner&action=delete&id=<?= $b->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa banner này?');" title="Xóa">
                                        <i class="bi bi-trash"></i>
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
