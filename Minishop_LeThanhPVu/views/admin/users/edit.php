<?php
$pageTitle = "Cập nhật người dùng";
require_once __DIR__ . "/../../../dao/UserDAO.php";
$dao = new UserDAO();
$errors = [];
$id = (int)($_GET['id'] ?? 0);
$user = $dao->findById($id);
if (!$user) { header("Location: index.php"); exit; }

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
    if ($email === '') $errors[] = 'Email không được để trống';
    // Nếu không nhập password mới, giữ password cũ
    if ($password === '') $password = $user->password;

    if (empty($errors)) {
        $user->fullname = $fullname; $user->username = $username; $user->password = $password;
        $user->email = $email; $user->phone = $phone; $user->address = $address; $user->role = $role;
        $dao->update($user);
        header("Location: index.php"); exit;
    }
} else {
    $fullname = $user->fullname; $username = $user->username; $email = $user->email;
    $phone = $user->phone; $address = $user->address; $role = $user->role;
}
ob_start();
?>
<h4 class="fw-bold mb-3">CẬP NHẬT NGƯỜI DÙNG</h4>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="POST" class="card card-body">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Họ và tên <span class="text-danger">*</span></label><input name="fullname" class="form-control" value="<?= htmlspecialchars($fullname) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label><input name="username" class="form-control" value="<?= htmlspecialchars($username) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Mật khẩu (để trống giữ nguyên)</label><input name="password" type="password" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Email <span class="text-danger">*</span></label><input name="email" type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Số điện thoại</label><input name="phone" class="form-control" value="<?= htmlspecialchars($phone ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Vai trò</label><select name="role" class="form-select"><option value="0" <?= $role == 0 ? 'selected' : '' ?>>Nhân viên</option><option value="1" <?= $role == 1 ? 'selected' : '' ?>>Quản trị</option></select></div>
        <div class="col-12"><button class="btn btn-success">Cập nhật</button> <a href="index.php" class="btn btn-secondary">Quay lại</a></div>
    </div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
