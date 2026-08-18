<?php
$pageTitle = $pageTitle ?? "Chi tiết thương hiệu";
ob_start();
?>
<h4 class="fw-bold mb-3">CHI TIẾT THƯƠNG HIỆU</h4>
<div class="card card-body">
    <table class="table table-bordered mb-0">
        <tr><th width="200">Mã</th><td><?= $brand->id ?></td></tr>
        <tr>
            <th>Hình ảnh</th>
            <td>
                <?php if (!empty($brand->image)) { ?>
                    <img src="uploads/brands/<?= $brand->image ?>" alt="<?= htmlspecialchars($brand->name) ?>" class="img-thumbnail" width="150">
                <?php } else { ?>
                    <span class="text-muted">No Image</span>
                <?php } ?>
            </td>
        </tr>
        <tr><th>Tên thương hiệu</th><td class="fw-bold"><?= htmlspecialchars($brand->name) ?></td></tr>
        <tr><th>Slug</th><td><code><?= htmlspecialchars($brand->slug) ?></code></td></tr>
        <tr><th>Mô tả</th><td><?= htmlspecialchars($brand->description ?? '') ?></td></tr>
        <tr><th>Trạng thái</th><td><span class="badge bg-<?= $brand->status ? 'success' : 'secondary' ?>"><?= $brand->status ? 'Hiển thị' : 'Ẩn' ?></span></td></tr>
        <tr><th>Ngày tạo</th><td><?= $brand->createdAt ?></td></tr>
        <tr><th>Cập nhật</th><td><?= $brand->updatedAt ?></td></tr>
    </table>
</div>
<div class="mt-3">
    <a href="index.php?area=admin&controller=brand&action=edit&id=<?= $brand->id ?>" class="btn btn-warning">Sửa</a>
    <a href="index.php?area=admin&controller=brand&action=index" class="btn btn-secondary">Quay lại</a>
</div>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
