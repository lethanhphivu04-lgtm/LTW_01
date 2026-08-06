<?php
$pageTitle = "Thêm người dùng";
require_once __DIR__ . "/../../../dao/UserDAO.php";
$dao = new UserDAO();
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $role = (int)($_POST['role'] ?? 0);

    if ($fullname === '') $errors[] = 'Họ tên không được để trống';
    if ($username === '') $errors[] = 'Tên đăng nhập không được để trống';
    if ($password === '') $errors[] = 'Mật khẩu không được để trống';
    if ($email === '') $errors[] = 'Email không được để trống';

    if (empty($errors)) {
        $u = new User($fullname, $username, $password, $email, $phone, $address, $role, 1);
        $dao->insert($u);
        header("Location: index.php"); exit;
    }
}
ob_start();
?>
<h4 class="fw-bold mb-3">THÊM NGƯỜI DÙNG</h4>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="POST" class="card card-body">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Họ và tên <span class="text-danger">*</span></label><input name="fullname" class="form-control" value="<?= htmlspecialchars($fullname ?? '') ?>" required></div>
        <div class="col-md-6"><label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label><input name="username" class="form-control" value="<?= htmlspecialchars($username ?? '') ?>" required></div>
        <div class="col-md-6"><label class="form-label">Mật khẩu <span class="text-danger">*</span></label><input name="password" type="password" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Email <span class="text-danger">*</span></label><input name="email" type="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" required></div>
        <div class="col-md-6"><label class="form-label">Số điện thoại</label><input name="phone" class="form-control" value="<?= htmlspecialchars($phone ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Vai trò</label><select name="role" class="form-select"><option value="0">Nhân viên</option><option value="1">Quản trị</option></select></div>
        <div class="col-12"><button class="btn btn-success">Lưu</button> <a href="index.php" class="btn btn-secondary">Quay lại</a></div>
    </div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
