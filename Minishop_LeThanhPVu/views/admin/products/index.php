<?php
$pageTitle = "Danh sách sản phẩm";
require_once __DIR__ . "/../../../dao/ProductDAO.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../dao/BrandDAO.php";

$dao = new ProductDAO();
$catDAO = new CategoryDAO();
$brandDAO = new BrandDAO();

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $dao->delete((int)$_GET['id']);
    header("Location: index.php");
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name = trim($_POST['proname'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $p = new Product(
        (int)$_POST['category_id'],
        (int)$_POST['brand_id'],
        $name,
        $slug,
        (float)$_POST['price'],
        (float)$_POST['discount_price'],
        (int)$_POST['quantity'],
        '',
        '',
        1
    );
    if (!empty($_POST['id'])) {
        $p->id = (int)$_POST['id'];
        $dao->update($p);
    } else {
        $dao->insert($p);
    }
    header("Location: index.php");
    exit;
}

$edit = isset($_GET['id']) ? $dao->findById((int)$_GET['id']) : null;
$list = $dao->getAll();
$categories = $catDAO->getAll();
$brands = $brandDAO->getAll();

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH SẢN PHẨM</h4>
    <button class="btn btn-success btn-sm" onclick="document.getElementById('formBox').classList.toggle('d-none')">+ Thêm sản phẩm</button>
</div>

<form id="formBox" method="POST" class="card card-body mb-3 <?= $edit ? '' : 'd-none' ?>">
    <input type="hidden" name="id" value="<?= $edit->id ?? 0 ?>">
    <div class="row g-2">
        <div class="col-md-4"><input name="proname" class="form-control" placeholder="Tên sản phẩm" value="<?= htmlspecialchars($edit->name ?? '') ?>" required></div>
        <div class="col-md-4">
            <select name="category_id" class="form-select" required>
                <option value="">-- Loại sản phẩm --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c->id ?>" <?= ($edit && $edit->categoryId == $c->id) ? 'selected' : '' ?>><?= htmlspecialchars($c->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="brand_id" class="form-select" required>
                <option value="">-- Thương hiệu --</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= $b->id ?>" <?= ($edit && $edit->brandId == $b->id) ? 'selected' : '' ?>><?= htmlspecialchars($b->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4"><input type="number" name="price" class="form-control" placeholder="Giá gốc" value="<?= $edit->price ?? 0 ?>" required></div>
        <div class="col-md-4"><input type="number" name="discount_price" class="form-control" placeholder="Giá bán" value="<?= $edit->discountPrice ?? 0 ?>" required></div>
        <div class="col-md-2"><input type="number" name="quantity" class="form-control" placeholder="Số lượng" value="<?= $edit->quantity ?? 10 ?>" required></div>
        <div class="col-md-2"><button class="btn btn-success w-100">Lưu</button></div>
    </div>
</form>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>Mã</th><th>Tên sản phẩm</th><th>Giá gốc</th><th>Giá bán</th><th>Số lượng</th><th>Trạng thái</th><th width="120">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php foreach ($list as $p): ?>
        <tr>
            <td><?= $p->id ?></td>
            <td class="fw-bold"><?= htmlspecialchars($p->name) ?></td>
            <td class="text-muted text-decoration-line-through"><?= number_format($p->price, 0, ',', '.') ?> đ</td>
            <td class="text-danger fw-bold"><?= number_format($p->discountPrice, 0, ',', '.') ?> đ</td>
            <td><span class="badge bg-info text-dark"><?= $p->quantity ?></span></td>
            <td><span class="badge bg-success">Còn bán</span></td>
            <td>
                <a href="index.php?id=<?= $p->id ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?action=delete&id=<?= $p->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
