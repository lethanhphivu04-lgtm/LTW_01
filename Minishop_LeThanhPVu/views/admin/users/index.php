<?php
$pageTitle = $pageTitle ?? "Danh sách người dùng";
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH NGƯỜI DÙNG</h4>
    <a href="index.php?area=admin&controller=user&action=create" class="btn btn-primary btn-sm">+ Thêm tài khoản</a>
</div>

<?php include __DIR__ . "/../layouts/filter_bar.php"; ?>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>STT</th><th>Họ tên</th><th>Username</th><th>Email</th><th>Vai trò</th><th width="180">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php $stt = ($offset ?? 0) + 1; foreach ($list as $u): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td class="fw-bold"><?= htmlspecialchars($u->fullname) ?></td>
            <td><code><?= htmlspecialchars($u->username) ?></code></td>
            <td><?= htmlspecialchars($u->email) ?></td>
            <td><span class="badge bg-<?= $u->role ? 'danger' : 'info text-dark' ?>"><?= $u->role ? 'Quản trị' : 'Nhân viên' ?></span></td>
            <td>
                <a href="index.php?area=admin&controller=user&action=detail&id=<?= $u->id ?>" class="btn btn-info btn-sm text-white">Chi tiết</a>
                <a href="index.php?area=admin&controller=user&action=edit&id=<?= $u->id ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?area=admin&controller=user&action=delete&id=<?= $u->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($list)): ?><tr><td colspan="6" class="text-center text-muted">Không tìm thấy người dùng nào</td></tr><?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . "/../layouts/pagination.php"; ?>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
