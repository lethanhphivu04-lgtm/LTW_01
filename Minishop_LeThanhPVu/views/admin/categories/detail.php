<?php
$pageTitle = $pageTitle ?? "Chi tiết danh mục";
ob_start();
?>
<h4 class="fw-bold mb-3">CHI TIẾT LOẠI SẢN PHẨM</h4>
<div class="card card-body">
    <table class="table table-bordered mb-0">
        <tr><th width="200">Mã loại</th><td><?= $category->id ?></td></tr>
        <tr>
            <th>Hình ảnh</th>
            <td>
                <?php if (!empty($category->image)) { ?>
                    <img src="uploads/categories/<?= $category->image ?>" alt="<?= htmlspecialchars($category->name) ?>" class="img-thumbnail" width="150">
                <?php } else { ?>
                    <span class="text-muted">No Image</span>
                <?php } ?>
            </td>
        </tr>
        <tr><th>Tên loại</th><td class="fw-bold"><?= htmlspecialchars($category->name) ?></td></tr>
        <tr><th>Slug</th><td><code><?= htmlspecialchars($category->slug) ?></code></td></tr>
        <tr><th>Mô tả</th><td><?= htmlspecialchars($category->description ?? '') ?></td></tr>
        <tr><th>Trạng thái</th><td><span class="badge bg-<?= $category->status ? 'success' : 'secondary' ?>"><?= $category->status ? 'Hiển thị' : 'Ẩn' ?></span></td></tr>
        <tr><th>Ngày tạo</th><td><?= $category->createdAt ?></td></tr>
        <tr><th>Cập nhật lần cuối</th><td><?= $category->updatedAt ?></td></tr>
    </table>
</div>
<div class="mt-3">
    <a href="index.php?area=admin&controller=category&action=edit&id=<?= $category->id ?>" class="btn btn-warning">Sửa</a>
    <a href="index.php?area=admin&controller=category&action=index" class="btn btn-secondary">Quay lại danh sách</a>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
