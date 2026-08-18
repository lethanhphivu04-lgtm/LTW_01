<?php
$pageTitle = $pageTitle ?? "Thêm sản phẩm";
$errors = $errors ?? [];
ob_start();
?>
<h4 class="fw-bold mb-3">THÊM SẢN PHẨM MỚI</h4>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="index.php?area=admin&controller=product&action=create" enctype="multipart/form-data" class="card card-body">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
            <input name="proname" class="form-control" value="<?= htmlspecialchars($_POST['proname'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Slug (để trống tự tạo)</label>
            <input name="slug" class="form-control" value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Loại sản phẩm <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select" required>
                <option value="0">-- Chọn danh mục --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c->id ?>" <?= (isset($_POST['category_id']) && (int)$_POST['category_id'] === $c->id) ? 'selected' : '' ?>><?= htmlspecialchars($c->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Thương hiệu <span class="text-danger">*</span></label>
            <select name="brand_id" class="form-select" required>
                <option value="0">-- Chọn thương hiệu --</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= $b->id ?>" <?= (isset($_POST['brand_id']) && (int)$_POST['brand_id'] === $b->id) ? 'selected' : '' ?>><?= htmlspecialchars($b->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Giá gốc (đ) <span class="text-danger">*</span></label>
            <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($_POST['price'] ?? '0') ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Giá bán (khuyến mãi)</label>
            <input type="number" name="discount_price" class="form-control" value="<?= htmlspecialchars($_POST['discount_price'] ?? '0') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Số lượng kho <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control" value="<?= htmlspecialchars($_POST['quantity'] ?? '10') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Hình ảnh chính</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1" <?= (!isset($_POST['status']) || $_POST['status'] == 1) ? 'selected' : '' ?>>Còn bán</option>
                <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == 0) ? 'selected' : '' ?>>Ngừng bán</option>
            </select>
        </div>
        <div class="col-md-12">
            <label class="form-label">Mô tả sản phẩm</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-success" type="submit">Lưu sản phẩm</button>
            <a href="index.php?area=admin&controller=product&action=index" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</form>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
