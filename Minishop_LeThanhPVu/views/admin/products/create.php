<?php
$pageTitle = "Thêm sản phẩm";
require_once __DIR__ . "/../../../dao/ProductDAO.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../dao/BrandDAO.php";

$dao = new ProductDAO();
$catDAO = new CategoryDAO();
$brandDAO = new BrandDAO();

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
            $uploadPath = __DIR__ . "/../../../uploads/products/" . $image;
            move_uploaded_file($tmpName, $uploadPath);
        }

        // luu db
        $p = new Product(
            $categoryId,
            $brandId,
            $name,
            $slug,
            $price,
            $discountPrice,
            $quantity,
            $image,
            $description,
            $status
        );
        $dao->insert($p);
        $productId = $dao->getConnection()->insert_id;

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
                            $dao->insertImage($productId, $gImage);
                        }
                    }
                }
            }
        }

        header("Location: index.php");
        exit;
    }
}

ob_start();
?>
<h4 class="fw-bold mb-3">THÊM SẢN PHẨM MỚI</h4>

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
    <!-- preview anh -->
    <div class="text-center mb-3" id="preview"></div>
    <div class="text-center mb-3" id="preview-gallery"></div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
            <input name="proname" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Slug (để trống tự tạo)</label>
            <input name="slug" class="form-control" value="<?= htmlspecialchars($slug ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Loại sản phẩm <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select" required>
                <option value="0">-- Chọn danh mục --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c->id ?>" <?= isset($categoryId) && $categoryId == $c->id ? 'selected' : '' ?>><?= htmlspecialchars($c->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Thương hiệu <span class="text-danger">*</span></label>
            <select name="brand_id" class="form-select" required>
                <option value="0">-- Chọn thương hiệu --</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= $b->id ?>" <?= isset($brandId) && $brandId == $b->id ? 'selected' : '' ?>><?= htmlspecialchars($b->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Giá gốc (đ) <span class="text-danger">*</span></label>
            <input type="number" name="price" class="form-control" value="<?= $price ?? 0 ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Giá bán (khuyến mãi)</label>
            <input type="number" name="discount_price" class="form-control" value="<?= $discountPrice ?? 0 ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Số lượng kho <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control" value="<?= $quantity ?? 10 ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Hình ảnh chính</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
        </div>
        <div class="col-md-6">
            <label class="form-label">Hình ảnh phụ (Gallery - chọn nhiều)</label>
            <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple>
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1">Còn bán</option>
                <option value="0">Ngừng bán</option>
            </select>
        </div>
        <div class="col-md-12">
            <label class="form-label">Mô tả sản phẩm</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description ?? '') ?></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-success">Lưu sản phẩm</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</form>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
