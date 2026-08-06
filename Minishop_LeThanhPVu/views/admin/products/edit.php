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

    if ($name === '') $errors[] = 'Tên sản phẩm không được để trống';
    if ($categoryId <= 0) $errors[] = 'Vui lòng chọn loại sản phẩm';
    if ($brandId <= 0) $errors[] = 'Vui lòng chọn thương hiệu';
    if ($price <= 0) $errors[] = 'Giá gốc phải lớn hơn 0';
    if ($discountPrice < 0) $errors[] = 'Giá bán không được nhỏ hơn 0';
    if ($quantity < 0) $errors[] = 'Số lượng không được nhỏ hơn 0';

    if ($slug === '') $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    if (empty($errors)) {
        $product->name = $name;
        $product->slug = $slug;
        $product->categoryId = $categoryId;
        $product->brandId = $brandId;
        $product->price = $price;
        $product->discountPrice = $discountPrice;
        $product->quantity = $quantity;
        $product->description = $description;
        $product->status = $status;

        $dao->update($product);
        header("Location: index.php");
        exit;
    }
} else {
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

ob_start();
?>
<h4 class="fw-bold mb-3">CẬP NHẬT SẢN PHẨM</h4>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<form method="POST" class="card card-body">
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
        <div class="col-md-12">
            <label class="form-label">Mô tả sản phẩm</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description ?? '') ?></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Còn bán</option>
                <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Ngừng bán</option>
            </select>
        </div>
        <div class="col-12">
            <button class="btn btn-success">Cập nhật sản phẩm</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</form>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
