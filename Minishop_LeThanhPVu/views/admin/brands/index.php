<?php
$pageTitle = "Danh sách thương hiệu";
require_once __DIR__ . "/../../../dao/BrandDAO.php";
$dao = new BrandDAO();

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $dao->delete((int)$_GET['id']);
    header("Location: index.php");
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name = trim($_POST['brandname'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $brand = new Brand($name, $slug, '', $_POST['description'] ?? '', 1);
    if (!empty($_POST['id'])) {
        $brand->id = (int)$_POST['id'];
        $dao->update($brand);
    } else {
        $dao->insert($brand);
    }
    header("Location: index.php");
    exit;
}

$edit = isset($_GET['id']) ? $dao->findById((int)$_GET['id']) : null;
$list = $dao->getAll();

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH THƯƠNG HIỆU</h4>
    <button class="btn btn-info text-white btn-sm" onclick="document.getElementById('formBox').classList.toggle('d-none')">+ Thêm thương hiệu</button>
</div>

<form id="formBox" method="POST" class="card card-body mb-3 <?= $edit ? '' : 'd-none' ?>">
    <input type="hidden" name="id" value="<?= $edit->id ?? 0 ?>">
    <div class="row g-2">
        <div class="col-md-5"><input name="brandname" class="form-control" placeholder="Tên thương hiệu" value="<?= htmlspecialchars($edit->name ?? '') ?>" required></div>
        <div class="col-md-5"><input name="slug" class="form-control" placeholder="Slug (để trống tự tạo)" value="<?= htmlspecialchars($edit->slug ?? '') ?>"></div>
        <div class="col-md-2"><button class="btn btn-success w-100">Lưu</button></div>
    </div>
</form>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>Mã</th><th>Tên thương hiệu</th><th>Slug</th><th>Trạng thái</th><th width="120">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php foreach ($list as $b): ?>
        <tr>
            <td><?= $b->id ?></td>
            <td class="fw-bold"><?= htmlspecialchars($b->name) ?></td>
            <td><code><?= htmlspecialchars($b->slug) ?></code></td>
            <td><span class="badge bg-success">Hiển thị</span></td>
            <td>
                <a href="index.php?id=<?= $b->id ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?action=delete&id=<?= $b->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
