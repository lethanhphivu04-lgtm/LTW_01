<?php
$pageTitle = "Chi tiết danh mục";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
$dao = new CategoryDAO();

$id = (int)($_GET['id'] ?? 0);
$cat = $dao->findById($id);
if (!$cat) { header("Location: index.php"); exit; }

ob_start();
?>
<h4 class="fw-bold mb-3">CHI TIẾT LOẠI SẢN PHẨM</h4>
<div class="card card-body">
    <table class="table table-bordered mb-0">
        <tr><th width="200">Mã loại</th><td><?= $cat->id ?></td></tr>
        <tr>
            <th>Hình ảnh</th>
            <td>
                <?php if (!empty($cat->image)) { ?>
                    <img src="../../../uploads/categories/<?= $cat->image ?>" alt="<?= htmlspecialchars($cat->name) ?>" class="img-thumbnail" width="150">
                <?php } else { ?>
                    <span class="text-muted">No Image</span>
                <?php } ?>
            </td>
        </tr>
        <tr><th>Tên loại</th><td class="fw-bold"><?= htmlspecialchars($cat->name) ?></td></tr>
        <tr><th>Slug</th><td><code><?= htmlspecialchars($cat->slug) ?></code></td></tr>
        <tr><th>Mô tả</th><td><?= htmlspecialchars($cat->description ?? '') ?></td></tr>
        <tr><th>Trạng thái</th><td><span class="badge bg-<?= $cat->status ? 'success' : 'secondary' ?>"><?= $cat->status ? 'Hiển thị' : 'Ẩn' ?></span></td></tr>
        <tr><th>Ngày tạo</th><td><?= $cat->createdAt ?></td></tr>
        <tr><th>Cập nhật lần cuối</th><td><?= $cat->updatedAt ?></td></tr>
    </table>
</div>
<div class="mt-3">
    <a href="edit.php?id=<?= $cat->id ?>" class="btn btn-warning">Sửa</a>
    <a href="index.php" class="btn btn-secondary">Quay lại danh sách</a>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
