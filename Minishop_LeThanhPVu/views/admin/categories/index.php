<?php
$pageTitle = "Danh sách danh mục";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
$dao = new CategoryDAO();

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $dao->delete((int)$_GET['id']);
    header("Location: index.php");
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name = trim($_POST['catename'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $cat = new Category($name, $slug, '', $_POST['description'] ?? '', (int)($_POST['status'] ?? 1));
    if (!empty($_POST['id'])) {
        $cat->id = (int)$_POST['id'];
        $dao->update($cat);
    } else {
        $dao->insert($cat);
    }
    header("Location: index.php");
    exit;
}

$edit = isset($_GET['id']) ? $dao->findById((int)$_GET['id']) : null;
$list = $dao->getAll();

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH LOẠI SẢN PHẨM</h4>
    <button class="btn btn-primary btn-sm" onclick="document.getElementById('formBox').classList.toggle('d-none')">+ Thêm loại</button>
</div>

<form id="formBox" method="POST" class="card card-body mb-3 <?= $edit ? '' : 'd-none' ?>">
    <input type="hidden" name="id" value="<?= $edit->id ?? 0 ?>">
    <div class="row g-2">
        <div class="col-md-5"><input name="catename" class="form-control" placeholder="Tên loại" value="<?= htmlspecialchars($edit->name ?? '') ?>" required></div>
        <div class="col-md-5"><input name="slug" class="form-control" placeholder="Slug (để trống tự tạo)" value="<?= htmlspecialchars($edit->slug ?? '') ?>"></div>
        <div class="col-md-2"><button class="btn btn-success w-100">Lưu</button></div>
    </div>
</form>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>Mã loại</th><th>Tên loại</th><th>Slug</th><th>Trạng thái</th><th width="120">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php foreach ($list as $c): ?>
        <tr>
            <td><?= $c->id ?></td>
            <td class="fw-bold"><?= htmlspecialchars($c->name) ?></td>
            <td><code><?= htmlspecialchars($c->slug) ?></code></td>
            <td><span class="badge bg-<?= $c->status ? 'success' : 'secondary' ?>"><?= $c->status ? 'Hiển thị' : 'Ẩn' ?></span></td>
            <td>
                <a href="index.php?id=<?= $c->id ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?action=delete&id=<?= $c->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
