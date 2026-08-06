<?php
$pageTitle = "Danh sách người dùng";
require_once __DIR__ . "/../../../dao/UserDAO.php";
$dao = new UserDAO();

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $dao->delete((int)$_GET['id']);
    header("Location: index.php");
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $u = new User(
        trim($_POST['fullname'] ?? ''),
        trim($_POST['username'] ?? ''),
        trim($_POST['password'] ?? '123456'),
        trim($_POST['email'] ?? ''),
        '',
        '',
        (int)($_POST['role'] ?? 0),
        1
    );
    if (!empty($_POST['id'])) {
        $u->id = (int)$_POST['id'];
        $dao->update($u);
    } else {
        $dao->insert($u);
    }
    header("Location: index.php");
    exit;
}

$edit = isset($_GET['id']) ? $dao->findById((int)$_GET['id']) : null;
$list = $dao->getAll();

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH NGƯỜI DÙNG</h4>
    <button class="btn btn-primary btn-sm" onclick="document.getElementById('formBox').classList.toggle('d-none')">+ Thêm tài khoản</button>
</div>

<form id="formBox" method="POST" class="card card-body mb-3 <?= $edit ? '' : 'd-none' ?>">
    <input type="hidden" name="id" value="<?= $edit->id ?? 0 ?>">
    <div class="row g-2">
        <div class="col-md-3"><input name="fullname" class="form-control" placeholder="Họ và tên" value="<?= htmlspecialchars($edit->fullname ?? '') ?>" required></div>
        <div class="col-md-3"><input name="username" class="form-control" placeholder="Tên đăng nhập" value="<?= htmlspecialchars($edit->username ?? '') ?>" required></div>
        <div class="col-md-3"><input name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($edit->email ?? '') ?>" required></div>
        <div class="col-md-3"><button class="btn btn-success w-100">Lưu</button></div>
    </div>
</form>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>Mã</th><th>Họ tên</th><th>Username</th><th>Email</th><th>Vai trò</th><th width="120">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php foreach ($list as $u): ?>
        <tr>
            <td><?= $u->id ?></td>
            <td class="fw-bold"><?= htmlspecialchars($u->fullname) ?></td>
            <td><code><?= htmlspecialchars($u->username) ?></code></td>
            <td><?= htmlspecialchars($u->email) ?></td>
            <td><span class="badge bg-<?= $u->role ? 'danger' : 'info text-dark' ?>"><?= $u->role ? 'Quản trị' : 'Nhân viên' ?></span></td>
            <td>
                <a href="index.php?id=<?= $u->id ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?action=delete&id=<?= $u->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
