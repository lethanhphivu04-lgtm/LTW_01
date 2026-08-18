<?php
$pageTitle = $pageTitle ?? "Chi tiết người dùng";
ob_start();
?>
<h4 class="fw-bold mb-3">CHI TIẾT NGƯỜI DÙNG</h4>
<div class="card card-body">
    <table class="table table-bordered mb-0">
        <tr><th width="200">Mã</th><td><?= $user->id ?></td></tr>
        <tr><th>Họ tên</th><td class="fw-bold"><?= htmlspecialchars($user->fullname) ?></td></tr>
        <tr><th>Tên đăng nhập</th><td><code><?= htmlspecialchars($user->username) ?></code></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($user->email) ?></td></tr>
        <tr><th>Số điện thoại</th><td><?= htmlspecialchars($user->phone ?? '') ?></td></tr>
        <tr><th>Địa chỉ</th><td><?= htmlspecialchars($user->address ?? '') ?></td></tr>
        <tr><th>Vai trò</th><td><span class="badge bg-<?= $user->role ? 'danger' : 'info text-dark' ?>"><?= $user->role ? 'Quản trị' : 'Nhân viên' ?></span></td></tr>
        <tr><th>Trạng thái</th><td><span class="badge bg-<?= $user->status ? 'success' : 'secondary' ?>"><?= $user->status ? 'Hoạt động' : 'Khóa' ?></span></td></tr>
        <tr><th>Ngày tạo</th><td><?= $user->createdAt ?></td></tr>
    </table>
</div>
<div class="mt-3">
    <a href="index.php?area=admin&controller=user&action=edit&id=<?= $user->id ?>" class="btn btn-warning">Sửa</a>
    <a href="index.php?area=admin&controller=user&action=index" class="btn btn-secondary">Quay lại</a>
</div>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
