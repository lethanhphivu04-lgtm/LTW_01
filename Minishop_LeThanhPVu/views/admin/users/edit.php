<?php
$pageTitle = $pageTitle ?? "Cập nhật người dùng";
$errors = $errors ?? [];
$id = $user->id ?? 0;
ob_start();
?>
<h4 class="fw-bold mb-3">CẬP NHẬT NGƯỜI DÙNG</h4>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="POST" action="index.php?area=admin&controller=user&action=edit&id=<?= $id ?>" class="card card-body">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Họ và tên <span class="text-danger">*</span></label><input name="fullname" class="form-control" value="<?= htmlspecialchars($_POST['fullname'] ?? $user->fullname) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label><input name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? $user->username) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Mật khẩu mới (để trống giữ nguyên)</label><input name="password" type="password" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Email <span class="text-danger">*</span></label><input name="email" type="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? $user->email) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Số điện thoại</label><input name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? $user->phone ?? '') ?>"></div>
        <div class="col-md-6"><label class="form-label">Địa chỉ</label><input name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? $user->address ?? '') ?>"></div>
        <div class="col-md-6">
            <label class="form-label">Vai trò</label>
            <?php $currentRole = (int)($_POST['role'] ?? $user->role); ?>
            <select name="role" class="form-select">
                <option value="0" <?= $currentRole === 0 ? 'selected' : '' ?>>Nhân viên</option>
                <option value="1" <?= $currentRole === 1 ? 'selected' : '' ?>>Quản trị</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <?php $currentStatus = (int)($_POST['status'] ?? $user->status); ?>
            <select name="status" class="form-select">
                <option value="1" <?= $currentStatus === 1 ? 'selected' : '' ?>>Hoạt động</option>
                <option value="0" <?= $currentStatus === 0 ? 'selected' : '' ?>>Khóa</option>
            </select>
        </div>
        <div class="col-12"><button class="btn btn-success" type="submit">Cập nhật</button> <a href="index.php?area=admin&controller=user&action=index" class="btn btn-secondary">Quay lại</a></div>
    </div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
