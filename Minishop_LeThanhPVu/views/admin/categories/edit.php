<?php
$pageTitle = $pageTitle ?? "Cập nhật danh mục";
$errors = $errors ?? [];
$id = $category->id ?? 0;
ob_start();
?>
<h4 class="fw-bold mb-3">CẬP NHẬT LOẠI SẢN PHẨM</h4>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="index.php?area=admin&controller=category&action=edit&id=<?= $id ?>" enctype="multipart/form-data" class="card card-body">
    <div class="text-center mb-3">
        <?php if (!empty($category->image)) { ?>
            <img src="uploads/categories/<?= $category->image ?>" class="img-thumbnail" width="120" id="preview">
        <?php } else { ?>
            <div id="preview"><span class="text-muted">Chưa có ảnh</span></div>
        <?php } ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Tên loại sản phẩm <span class="text-danger">*</span></label>
        <input type="text" name="catename" class="form-control" value="<?= htmlspecialchars($_POST['catename'] ?? $category->name) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($_POST['slug'] ?? $category->slug) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Thay đổi hình ảnh</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    <div class="mb-3">
        <label class="form-label">Trạng thái</label>
        <select name="status" class="form-select">
            <?php $statusVal = (int)($_POST['status'] ?? $category->status); ?>
            <option value="1" <?= $statusVal === 1 ? 'selected' : '' ?>>Hiển thị</option>
            <option value="0" <?= $statusVal === 0 ? 'selected' : '' ?>>Ẩn</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description'] ?? $category->description ?? '') ?></textarea>
    </div>
    <div>
        <button class="btn btn-success" type="submit">Cập nhật</button>
        <a href="index.php?area=admin&controller=category&action=index" class="btn btn-secondary">Quay lại</a>
    </div>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
