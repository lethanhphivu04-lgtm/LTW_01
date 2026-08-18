<?php
$pageTitle = $pageTitle ?? "Cập nhật sản phẩm";
$errors = $errors ?? [];
$id = $product->id ?? 0;
ob_start();
?>
<h4 class="fw-bold mb-3">CẬP NHẬT SẢN PHẨM</h4>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="index.php?area=admin&controller=product&action=edit&id=<?= $id ?>" enctype="multipart/form-data" class="card card-body">
    <!-- preview anh hien tai -->
    <div class="text-center mb-3">
        <?php if (!empty($product->image)) { ?>
            <img src="uploads/products/<?= $product->image ?>" class="img-thumbnail" width="150" id="preview">
        <?php } else { ?>
            <div id="preview"><span class="text-muted">No Image</span></div>
        <?php } ?>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
            <input name="proname" class="form-control" value="<?= htmlspecialchars($_POST['proname'] ?? $product->name) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Slug</label>
            <input name="slug" class="form-control" value="<?= htmlspecialchars($_POST['slug'] ?? $product->slug) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Loại sản phẩm <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select" required>
                <option value="0">-- Chọn danh mục --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c->id ?>" <?= ((int)($_POST['category_id'] ?? $product->categoryId) === $c->id) ? 'selected' : '' ?>><?= htmlspecialchars($c->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Thương hiệu <span class="text-danger">*</span></label>
            <select name="brand_id" class="form-select" required>
                <option value="0">-- Chọn thương hiệu --</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= $b->id ?>" <?= ((int)($_POST['brand_id'] ?? $product->brandId) === $b->id) ? 'selected' : '' ?>><?= htmlspecialchars($b->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Giá gốc (đ) <span class="text-danger">*</span></label>
            <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($_POST['price'] ?? $product->price) ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Giá bán (khuyến mãi)</label>
            <input type="number" name="discount_price" class="form-control" value="<?= htmlspecialchars($_POST['discount_price'] ?? $product->discountPrice) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Số lượng kho <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control" value="<?= htmlspecialchars($_POST['quantity'] ?? $product->quantity) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Thay đổi hình ảnh chính</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <?php $currentStatus = (int)($_POST['status'] ?? $product->status); ?>
                <option value="1" <?= $currentStatus === 1 ? 'selected' : '' ?>>Còn bán</option>
                <option value="0" <?= $currentStatus === 0 ? 'selected' : '' ?>>Ngừng bán</option>
            </select>
        </div>
        <div class="col-md-12">
            <label class="form-label">Mô tả sản phẩm</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description'] ?? $product->description ?? '') ?></textarea>
        </div>

        <div class="col-12 mt-3">
            <button class="btn btn-success" type="submit">Cập nhật sản phẩm</button>
            <a href="index.php?area=admin&controller=product&action=index" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</form>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
