<?php
$pageTitle = "Cập nhật thương hiệu";
require_once __DIR__ . "/../../../dao/BrandDAO.php";
$dao = new BrandDAO();
$errors = [];

$id = (int)($_GET['id'] ?? 0);
$brand = $dao->findById($id);
if (!$brand) { header("Location: index.php"); exit; }

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name = trim($_POST['brandname'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = (int)($_POST['status'] ?? 1);

    if ($name === '') $errors[] = 'Tên thương hiệu không được để trống';
    if ($slug === '') $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    if (empty($errors)) {
        $brand->name = $name; $brand->slug = $slug; $brand->description = $description; $brand->status = $status;
        $dao->update($brand);
        header("Location: index.php"); exit;
    }
} else {
    $name = $brand->name; $slug = $brand->slug; $description = $brand->description; $status = $brand->status;
}

ob_start();
?>
<h4 class="fw-bold mb-3">CẬP NHẬT THƯƠNG HIỆU</h4>
<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form method="POST" class="card card-body">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Tên thương hiệu <span class="text-danger">*</span></label><input name="brandname" class="form-control" value="<?= htmlspecialchars($name) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Slug</label><input name="slug" class="form-control" value="<?= htmlspecialchars($slug) ?>"></div>
        <div class="col-md-12"><label class="form-label">Mô tả</label><textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description ?? '') ?></textarea></div>
        <div class="col-md-6"><label class="form-label">Trạng thái</label><select name="status" class="form-select"><option value="1" <?= $status == 1 ? 'selected' : '' ?>>Hiển thị</option><option value="0" <?= $status == 0 ? 'selected' : '' ?>>Ẩn</option></select></div>
        <div class="col-12"><button class="btn btn-success">Cập nhật</button> <a href="index.php" class="btn btn-secondary">Quay lại</a></div>
    </div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
