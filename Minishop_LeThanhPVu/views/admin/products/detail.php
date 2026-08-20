<?php
$pageTitle = $pageTitle ?? "Chi tiết sản phẩm";
$galleryImages = $galleryImages ?? [];
ob_start();
?>
<h4 class="fw-bold mb-3">CHI TIẾT SẢN PHẨM</h4>
<div class="card card-body">
    <table class="table table-bordered mb-0">
        <tr><th width="200">Mã sản phẩm</th><td><?= $product['id'] ?></td></tr>
        <tr>
            <th>Hình ảnh chính</th>
            <td>
                <?php if (!empty($product['image'])) { ?>
                    <img src="uploads/products/<?= htmlspecialchars($product['image']) ?>" 
                         alt="<?= htmlspecialchars($product['proname']) ?>" 
                         class="img-thumbnail" 
                         width="200"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/200?text=No+Image';">
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
                        <img src="uploads/products/<?= htmlspecialchars($gImg['image']) ?>" 
                             class="img-thumbnail" 
                             width="120" 
                             style="height:120px; object-fit:cover;"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/120?text=No+Image';">
                    <?php endforeach; ?>
                    <?php if (empty($galleryImages)): ?>
                        <span class="text-muted">Chưa có ảnh phụ</span>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <tr><th>Tên sản phẩm</th><td class="fw-bold"><?= htmlspecialchars($product['proname']) ?></td></tr>
        <tr><th>Slug</th><td><code><?= htmlspecialchars($product['slug']) ?></code></td></tr>
        <tr><th>Danh mục</th><td><span class="badge bg-primary fs-6"><?= htmlspecialchars($product['catename'] ?? '') ?></span></td></tr>
        <tr><th>Thương hiệu</th><td><span class="badge bg-secondary fs-6"><?= htmlspecialchars($product['brandname'] ?? '') ?></span></td></tr>
        <tr><th>Giá gốc</th><td class="text-muted text-decoration-line-through"><?= number_format($product['price'], 0, ',', '.') ?> đ</td></tr>
        <tr><th>Giá khuyến mãi</th><td class="text-danger fw-bold fs-5"><?= number_format($product['discount_price'], 0, ',', '.') ?> đ</td></tr>
        <tr><th>Số lượng tồn kho</th><td><span class="badge bg-info text-dark fs-6"><?= $product['quantity'] ?></span></td></tr>
        <tr><th>Mô tả sản phẩm</th><td><?= htmlspecialchars($product['description'] ?? '') ?></td></tr>
        <tr><th>Trạng thái</th><td><span class="badge bg-<?= $product['status'] ? 'success' : 'secondary' ?>"><?= $product['status'] ? 'Còn bán' : 'Ngừng bán' ?></span></td></tr>
        <tr><th>Ngày tạo</th><td><?= $product['created_at'] ?></td></tr>
        <tr><th>Cập nhật lần cuối</th><td><?= $product['updated_at'] ?></td></tr>
    </table>
</div>
<div class="mt-3">
    <a href="index.php?area=admin&controller=product&action=edit&id=<?= $product['id'] ?>" class="btn btn-warning">Sửa</a>
    <a href="index.php?area=admin&controller=product&action=index" class="btn btn-secondary">Quay lại danh sách</a>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
