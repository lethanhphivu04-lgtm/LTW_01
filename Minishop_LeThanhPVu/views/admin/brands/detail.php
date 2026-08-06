<?php
$pageTitle = "Chi tiết thương hiệu";
require_once __DIR__ . "/../../../dao/BrandDAO.php";
$dao = new BrandDAO();
$id = (int)($_GET['id'] ?? 0);
$brand = $dao->findById($id);
if (!$brand) { header("Location: index.php"); exit; }
ob_start();
?>
<h4 class="fw-bold mb-3">CHI TIẾT THƯƠNG HIỆU</h4>
<div class="card card-body">
    <table class="table table-bordered mb-0">
        <tr><th width="200">Mã</th><td><?= $brand->id ?></td></tr>
        <tr><th>Tên thương hiệu</th><td class="fw-bold"><?= htmlspecialchars($brand->name) ?></td></tr>
        <tr><th>Slug</th><td><code><?= htmlspecialchars($brand->slug) ?></code></td></tr>
        <tr><th>Mô tả</th><td><?= htmlspecialchars($brand->description ?? '') ?></td></tr>
        <tr><th>Trạng thái</th><td><span class="badge bg-<?= $brand->status ? 'success' : 'secondary' ?>"><?= $brand->status ? 'Hiển thị' : 'Ẩn' ?></span></td></tr>
        <tr><th>Ngày tạo</th><td><?= $brand->createdAt ?></td></tr>
        <tr><th>Cập nhật</th><td><?= $brand->updatedAt ?></td></tr>
    </table>
</div>
<div class="mt-3"><a href="edit.php?id=<?= $brand->id ?>" class="btn btn-warning">Sửa</a> <a href="index.php" class="btn btn-secondary">Quay lại</a></div>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
