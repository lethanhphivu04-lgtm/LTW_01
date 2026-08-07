<?php
$pageTitle = "Cập nhật sản phẩm";
require_once __DIR__ . "/../../../dao/ProductDAO.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../dao/BrandDAO.php";

$dao = new ProductDAO();
$catDAO = new CategoryDAO();
$brandDAO = new BrandDAO();

$id = (int)($_GET['id'] ?? 0);
$product = $dao->findById($id);
if (!$product) { header("Location: index.php"); exit; }

$categories = $catDAO->getAll();
$brands = $brandDAO->getAll();
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name = trim($_POST['proname'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $brandId = (int)($_POST['brand_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $discountPrice = (float)($_POST['discount_price'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = (int)($_POST['status'] ?? 1);

    // validate thong tin
    if ($name === '') $errors[] = 'Tên sản phẩm không được để trống.';
    if ($categoryId <= 0) $errors[] = 'Vui lòng chọn loại sản phẩm.';
    if ($brandId <= 0) $errors[] = 'Vui lòng chọn thương hiệu.';
    if ($price <= 0) $errors[] = 'Giá gốc phải lớn hơn 0.';
    if ($discountPrice < 0) $errors[] = 'Giá bán không được nhỏ hơn 0.';
    if ($quantity < 0) $errors[] = 'Số lượng không được nhỏ hơn 0.';

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
        $image = $product->image;
        if ($fileName != "") {
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $image = time() . "_" . $slug . "." . $extension;
            $uploadPath = __DIR__ . "/../../../uploads/products/" . $image;

            // xoa anh cu
            if (!empty($product->image)) {
                $oldImage = __DIR__ . "/../../../uploads/products/" . $product->image;
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }
            move_uploaded_file($tmpName, $uploadPath);
        }

        $product->name = $name;
        $product->slug = $slug;
        $product->categoryId = $categoryId;
        $product->brandId = $brandId;
        $product->price = $price;
        $product->discountPrice = $discountPrice;
        $product->quantity = $quantity;
        $product->image = $image;
        $product->description = $description;
        $product->status = $status;

        $dao->update($product);

        // upload anh phu gallery
        if (isset($_FILES["images"]) && !empty($_FILES["images"]["name"][0])) {
            $totalFiles = count($_FILES["images"]["name"]);
            for ($i = 0; $i < $totalFiles; $i++) {
                $gName = $_FILES["images"]["name"][$i];
                $gTmp  = $_FILES["images"]["tmp_name"][$i];
                $gErr  = $_FILES["images"]["error"][$i];
                $gSize = $_FILES["images"]["size"][$i];

                if ($gName != "" && $gErr == UPLOAD_ERR_OK && $gSize <= $maxSize) {
                    $gExt = strtolower(pathinfo($gName, PATHINFO_EXTENSION));
                    if (in_array($gExt, $allowExtensions)) {
                        $gImage = time() . "_" . $i . "_" . $slug . "." . $gExt;
                        $gPath  = __DIR__ . "/../../../uploads/products/" . $gImage;
                        if (move_uploaded_file($gTmp, $gPath)) {
                            $dao->insertImage($id, $gImage);
                        }
                    }
                }
            }
        }

        header("Location: index.php");
        exit;
    }
} else {
    // xoa anh gallery (Cau G)
    if (isset($_GET['action']) && $_GET['action'] === 'delete_image') {
        $imageId = (int)($_GET['image_id'] ?? 0);
        if ($imageId > 0) {
            $dao->deleteImage($imageId);
        }
        header("Location: edit.php?id=" . $id);
        exit;
    }

    $name = $product->name;
    $slug = $product->slug;
    $categoryId = $product->categoryId;
    $brandId = $product->brandId;
    $price = $product->price;
    $discountPrice = $product->discountPrice;
    $quantity = $product->quantity;
    $description = $product->description;
    $status = $product->status;
}

$galleryImages = $dao->getImagesByProductId($id);

ob_start();
?>
<h4 class="fw-bold mb-3">CẬP NHẬT SẢN PHẨM</h4>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
            <li><?= $error ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="card card-body">
    <!-- preview anh hien tai -->
    <div class="text-center mb-3">
        <?php if (!empty($product->image)) { ?>
            <img src="../../../uploads/products/<?= $product->image ?>" class="img-thumbnail" width="150" id="preview">
        <?php } else { ?>
            <div id="preview"><span class="text-muted">No Image</span></div>
        <?php } ?>
    </div>
    <div class="text-center mb-3" id="preview-gallery"></div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
            <input name="proname" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Slug</label>
            <input name="slug" class="form-control" value="<?= htmlspecialchars($slug) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Loại sản phẩm <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select" required>
                <option value="0">-- Chọn danh mục --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c->id ?>" <?= $categoryId == $c->id ? 'selected' : '' ?>><?= htmlspecialchars($c->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Thương hiệu <span class="text-danger">*</span></label>
            <select name="brand_id" class="form-select" required>
                <option value="0">-- Chọn thương hiệu --</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= $b->id ?>" <?= $brandId == $b->id ? 'selected' : '' ?>><?= htmlspecialchars($b->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Giá gốc (đ) <span class="text-danger">*</span></label>
            <input type="number" name="price" class="form-control" value="<?= $price ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Giá bán (khuyến mãi)</label>
            <input type="number" name="discount_price" class="form-control" value="<?= $discountPrice ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Số lượng kho <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control" value="<?= $quantity ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Thay đổi hình ảnh chính</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
        </div>
        <div class="col-md-6">
            <label class="form-label">Thêm ảnh phụ (Gallery - chọn nhiều)</label>
            <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple>
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Còn bán</option>
                <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Ngừng bán</option>
            </select>
        </div>
        <div class="col-md-12">
            <label class="form-label">Mô tả sản phẩm</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description ?? '') ?></textarea>
        </div>

        <!-- bo suu tap anh hien tai (Cau E & G) -->
        <div class="col-md-12">
            <label class="form-label fw-bold">Bộ sưu tập ảnh hiện tại (Gallery)</label>
            <div class="row g-2">
                <?php foreach ($galleryImages as $gImg): ?>
                    <div class="col-auto text-center">
                        <img src="../../../uploads/products/<?= $gImg['image'] ?>" class="img-thumbnail d-block mb-1" width="100" style="height:100px; object-fit:cover;">
                        <a href="edit.php?id=<?= $id ?>&action=delete_image&image_id=<?= $gImg['id'] ?>" class="btn btn-danger btn-sm py-0 px-2" onclick="return confirm('Bạn có chắc muốn xóa ảnh này?')">Xóa</a>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($galleryImages)): ?>
                    <div class="text-muted small">Chưa có hình ảnh phụ.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12 mt-3">
            <button class="btn btn-success">Cập nhật sản phẩm</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</form>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
