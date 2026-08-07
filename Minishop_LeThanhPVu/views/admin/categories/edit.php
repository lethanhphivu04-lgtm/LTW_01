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

    // doc file anh
    $fileName = $_FILES["image"]["name"] ?? "";
    $tmpName  = $_FILES["image"]["tmp_name"] ?? "";
    $fileSize = $_FILES["image"]["size"] ?? 0;
    $error    = $_FILES["image"]["error"] ?? 0;

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
        $image = $cat->image;
        if ($fileName != "") {
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $image = time() . "_" . $slug . "." . $extension;
            $uploadPath = __DIR__ . "/../../../uploads/categories/" . $image;

            // xoa anh cu
            if (!empty($cat->image)) {
                $oldImage = __DIR__ . "/../../../uploads/categories/" . $cat->image;
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }
            move_uploaded_file($tmpName, $uploadPath);
        }

        $cat->name = $name;
        $cat->slug = $slug;
        $cat->image = $image;
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

<form method="POST" enctype="multipart/form-data" class="card card-body">
    <!-- preview anh hien tai -->
    <div class="text-center mb-3">
        <?php if (!empty($cat->image)) { ?>
            <img src="../../../uploads/categories/<?= $cat->image ?>" class="img-thumbnail" width="150" id="preview">
        <?php } else { ?>
            <div id="preview"><span class="text-muted">No Image</span></div>
        <?php } ?>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
            <input name="catename" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Slug</label>
            <input name="slug" class="form-control" value="<?= htmlspecialchars($slug) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Thay đổi hình ảnh</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Hiển thị</option>
                <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Ẩn</option>
            </select>
        </div>
        <div class="col-md-12">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description ?? '') ?></textarea>
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
