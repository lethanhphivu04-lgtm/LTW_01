<?php
$pageTitle = $pageTitle ?? "Thêm người dùng";
$errors = $errors ?? [];
ob_start();
?>
<h4 class="fw-bold mb-3">THÊM NGƯỜI DÙNG</h4>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="POST" action="index.php?area=admin&controller=user&action=create" class="card card-body">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Họ và tên <span class="text-danger">*</span></label><input name="fullname" class="form-control" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>" required></div>
        <div class="col-md-6"><label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label><input name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required></div>
        <div class="col-md-6"><label class="form-label">Mật khẩu <span class="text-danger">*</span></label><input name="password" type="password" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Email <span class="text-danger">*</span></label><input name="email" type="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required></div>
        <div class="col-md-6"><label class="form-label">Số điện thoại</label><input name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Địa chỉ</label><input name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Vai trò</label><select name="role" class="form-select"><option value="0" <?= (isset($_POST['role']) && (int)$_POST['role'] === 0) ? 'selected' : '' ?>>Nhân viên</option><option value="1" <?= (isset($_POST['role']) && (int)$_POST['role'] === 1) ? 'selected' : '' ?>>Quản trị</option></select></div>
        <div class="col-md-6"><label class="form-label">Trạng thái</label><select name="status" class="form-select"><option value="1" <?= (!isset($_POST['status']) || (int)$_POST['status'] === 1) ? 'selected' : '' ?>>Hoạt động</option><option value="0" <?= (isset($_POST['status']) && (int)$_POST['status'] === 0) ? 'selected' : '' ?>>Khóa</option></select></div>
        <div class="col-12"><button class="btn btn-success" type="submit">Lưu</button> <a href="index.php?area=admin&controller=user&action=index" class="btn btn-secondary">Quay lại</a></div>
    </div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
