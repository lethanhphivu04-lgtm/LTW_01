<?php
$pageTitle = "Danh sách khách hàng";
require_once __DIR__ . "/../../../dao/CustomerDAO.php";
$dao = new CustomerDAO();

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $dao->delete((int)$_GET['id']);
    header("Location: index.php");
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $c = new Customer(
        trim($_POST['fullname'] ?? ''),
        trim($_POST['phone'] ?? ''),
        trim($_POST['email'] ?? ''),
        trim($_POST['address'] ?? ''),
        '',
        1
    );
    if (!empty($_POST['id'])) {
        $c->id = (int)$_POST['id'];
        $dao->update($c);
    } else {
        $dao->insert($c);
    }
    header("Location: index.php");
    exit;
}

$edit = isset($_GET['id']) ? $dao->findById((int)$_GET['id']) : null;
$list = $dao->getAll();

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH KHÁCH HÀNG</h4>
    <button class="btn btn-warning text-dark btn-sm" onclick="document.getElementById('formBox').classList.toggle('d-none')">+ Thêm khách hàng</button>
</div>

<form id="formBox" method="POST" class="card card-body mb-3 <?= $edit ? '' : 'd-none' ?>">
    <input type="hidden" name="id" value="<?= $edit->id ?? 0 ?>">
    <div class="row g-2">
        <div class="col-md-3"><input name="fullname" class="form-control" placeholder="Họ và tên" value="<?= htmlspecialchars($edit->fullname ?? '') ?>" required></div>
        <div class="col-md-3"><input name="phone" class="form-control" placeholder="Số điện thoại" value="<?= htmlspecialchars($edit->phone ?? '') ?>" required></div>
        <div class="col-md-3"><input name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($edit->email ?? '') ?>"></div>
        <div class="col-md-3"><button class="btn btn-success w-100">Lưu</button></div>
    </div>
</form>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>Mã KH</th><th>Họ tên</th><th>Số điện thoại</th><th>Email</th><th>Địa chỉ</th><th width="120">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php foreach ($list as $c): ?>
        <tr>
            <td><?= $c->id ?></td>
            <td class="fw-bold"><?= htmlspecialchars($c->fullname) ?></td>
            <td><?= htmlspecialchars($c->phone) ?></td>
            <td><?= htmlspecialchars($c->email ?? '') ?></td>
            <td><?= htmlspecialchars($c->address ?? '') ?></td>
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
