<?php
$pageTitle = "Cập nhật danh mục";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
$dao = new CategoryDAO();
$errors = [];

$id = (int)($_GET['id'] ?? 0);
$cat = $dao->findById($id);
if (!$cat) { header("Location: index.php"); exit; }

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name = trim($_POST['catename'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = (int)($_POST['status'] ?? 1);

    if ($name === '') $errors[] = 'Tên danh mục không được để trống';
    if ($slug === '') $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    if (empty($errors)) {
        $cat->name = $name;
        $cat->slug = $slug;
        $cat->description = $description;
        $cat->status = $status;
        $dao->update($cat);
        header("Location: index.php");
        exit;
    }
} else {
    $name = $cat->name;
    $slug = $cat->slug;
    $description = $cat->description;
    $status = $cat->status;
}

ob_start();
?>
<h4 class="fw-bold mb-3">CẬP NHẬT LOẠI SẢN PHẨM</h4>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST" class="card card-body">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
            <input name="catename" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Slug</label>
            <input name="slug" class="form-control" value="<?= htmlspecialchars($slug) ?>">
        </div>
        <div class="col-md-12">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description ?? '') ?></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Hiển thị</option>
                <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Ẩn</option>
            </select>
        </div>
        <div class="col-12">
            <button class="btn btn-success">Cập nhật</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</form>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
