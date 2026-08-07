<?php
$pageTitle = "Chi tiết sản phẩm";
require_once __DIR__ . "/../../../dao/ProductDAO.php";

$dao = new ProductDAO();
$id = (int)($_GET['id'] ?? 0);
$p = $dao->findByIdWithJoin($id);
if (!$p) { header("Location: index.php"); exit; }
$galleryImages = $dao->getImagesByProductId($id);

ob_start();
?>
<h4 class="fw-bold mb-3">CHI TIẾT SẢN PHẨM</h4>
<div class="card card-body">
    <table class="table table-bordered mb-0">
        <tr><th width="200">Mã sản phẩm</th><td><?= $p['id'] ?></td></tr>
        <!-- hien thi hinh anh -->
        <tr>
            <th>Hình ảnh chính</th>
            <td>
                <?php if (!empty($p['image'])) { ?>
                    <img src="../../../uploads/products/<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['proname']) ?>" class="img-thumbnail" width="200">
                <?php } else { ?>
                    <span class="text-muted">No Image</span>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th>Ảnh phụ (Gallery)</th>
            <td>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($galleryImages as $gImg): ?>
                        <img src="../../../uploads/products/<?= $gImg['image'] ?>" class="img-thumbnail" width="120" style="height:120px; object-fit:cover;">
                    <?php endforeach; ?>
                    <?php if (empty($galleryImages)): ?>
                        <span class="text-muted">Chưa có ảnh phụ</span>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <tr><th>Tên sản phẩm</th><td class="fw-bold"><?= htmlspecialchars($p['proname']) ?></td></tr>
        <tr><th>Slug</th><td><code><?= htmlspecialchars($p['slug']) ?></code></td></tr>
        <tr><th>Danh mục</th><td><span class="badge bg-primary fs-6"><?= htmlspecialchars($p['catename']) ?></span></td></tr>
        <tr><th>Thương hiệu</th><td><span class="badge bg-secondary fs-6"><?= htmlspecialchars($p['brandname']) ?></span></td></tr>
        <tr><th>Giá gốc</th><td class="text-muted text-decoration-line-through"><?= number_format($p['price'], 0, ',', '.') ?> đ</td></tr>
        <tr><th>Giá khuyến mãi</th><td class="text-danger fw-bold fs-5"><?= number_format($p['discount_price'], 0, ',', '.') ?> đ</td></tr>
        <tr><th>Số lượng tồn kho</th><td><span class="badge bg-info text-dark fs-6"><?= $p['quantity'] ?></span></td></tr>
        <tr><th>Mô tả sản phẩm</th><td><?= htmlspecialchars($p['description'] ?? '') ?></td></tr>
        <tr><th>Trạng thái</th><td><span class="badge bg-<?= $p['status'] ? 'success' : 'secondary' ?>"><?= $p['status'] ? 'Còn bán' : 'Ngừng bán' ?></span></td></tr>
        <tr><th>Ngày tạo</th><td><?= $p['created_at'] ?></td></tr>
        <tr><th>Cập nhật lần cuối</th><td><?= $p['updated_at'] ?></td></tr>
    </table>
</div>
<div class="mt-3">
    <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-warning">Sửa</a>
    <a href="index.php" class="btn btn-secondary">Quay lại danh sách</a>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
