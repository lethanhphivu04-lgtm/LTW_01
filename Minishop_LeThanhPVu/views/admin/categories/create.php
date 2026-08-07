<?php
$pageTitle = "Thêm danh mục";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
$dao = new CategoryDAO();
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name = trim($_POST['catename'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = (int)($_POST['status'] ?? 1);

    // Validation
    if ($name === '') $errors[] = 'Tên danh mục không được để trống';
    if ($slug === '') $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    // doc file anh
    $fileName = $_FILES["image"]["name"] ?? "";
    $tmpName  = $_FILES["image"]["tmp_name"] ?? "";
    $fileSize = $_FILES["image"]["size"] ?? 0;
    $error    = $_FILES["image"]["error"] ?? 0;
    $image    = "";

    // validate anh
    if ($fileName != "" && $error != UPLOAD_ERR_OK) {
        $errors[] = "Upload hình ảnh không thành công.";
    }

    $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($fileName != "" && !in_array($extension, $allowExtensions)) {
        $errors[] = "Chỉ cho phép file JPG, JPEG, PNG hoặc WEBP.";
    }

    $maxSize = 200 * 1024;
    if ($fileName != "" && $fileSize > $maxSize) {
        $errors[] = "Kích thước hình ảnh <= 200 KB.";
    }

    if (empty($errors)) {
        // upload anh
        if ($fileName != "") {
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $image = time() . "_" . $slug . "." . $extension;
            $uploadPath = __DIR__ . "/../../../uploads/categories/" . $image;
            move_uploaded_file($tmpName, $uploadPath);
        }

        $cat = new Category($name, $slug, $image, $description, $status);
        $dao->insert($cat);
        header("Location: index.php");
        exit;
    }
}

ob_start();
?>
<h4 class="fw-bold mb-3">THÊM LOẠI SẢN PHẨM</h4>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="card card-body">
    <!-- preview anh -->
    <div class="text-center mb-3" id="preview"></div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
            <input name="catename" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Slug (để trống tự tạo)</label>
            <input name="slug" class="form-control" value="<?= htmlspecialchars($slug ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Hình ảnh</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1">Hiển thị</option>
                <option value="0">Ẩn</option>
            </select>
        </div>
        <div class="col-md-12">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description ?? '') ?></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-success">Lưu danh mục</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</form>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
