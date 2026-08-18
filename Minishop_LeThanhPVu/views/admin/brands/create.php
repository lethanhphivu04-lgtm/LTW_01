<?php
$pageTitle = $pageTitle ?? "Thêm thương hiệu";
$errors = $errors ?? [];
ob_start();
?>
<h4 class="fw-bold mb-3">THÊM THƯƠNG HIỆU</h4>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="index.php?area=admin&controller=brand&action=create" enctype="multipart/form-data" class="card card-body">
    <div class="mb-3">
        <label class="form-label">Tên thương hiệu <span class="text-danger">*</span></label>
        <input type="text" name="brandname" class="form-control" value="<?= htmlspecialchars($_POST['brandname'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Slug (để trống tự tạo)</label>
        <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Hình ảnh</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    <div class="mb-3">
        <label class="form-label">Trạng thái</label>
        <select name="status" class="form-select">
            <option value="1" <?= (!isset($_POST['status']) || $_POST['status'] == 1) ? 'selected' : '' ?>>Hiển thị</option>
            <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == 0) ? 'selected' : '' ?>>Ẩn</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>
    <div>
        <button class="btn btn-primary" type="submit">Lưu thương hiệu</button>
        <a href="index.php?area=admin&controller=brand&action=index" class="btn btn-secondary">Quay lại</a>
    </div>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
