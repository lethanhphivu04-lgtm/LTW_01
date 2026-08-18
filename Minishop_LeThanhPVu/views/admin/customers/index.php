<?php
$pageTitle = $pageTitle ?? "Danh sách khách hàng";
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH KHÁCH HÀNG</h4>
    <a href="index.php?area=admin&controller=customer&action=create" class="btn btn-warning text-dark btn-sm">+ Thêm khách hàng</a>
</div>

<?php include __DIR__ . "/../layouts/filter_bar.php"; ?>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>STT</th><th>Họ tên</th><th>Số điện thoại</th><th>Email</th><th>Địa chỉ</th><th width="180">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php $stt = ($offset ?? 0) + 1; foreach ($list as $c): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td class="fw-bold"><?= htmlspecialchars($c->fullname) ?></td>
            <td><?= htmlspecialchars($c->phone) ?></td>
            <td><?= htmlspecialchars($c->email ?? '') ?></td>
            <td><?= htmlspecialchars($c->address ?? '') ?></td>
            <td>
                <a href="index.php?area=admin&controller=customer&action=detail&id=<?= $c->id ?>" class="btn btn-info btn-sm text-white">Chi tiết</a>
                <a href="index.php?area=admin&controller=customer&action=edit&id=<?= $c->id ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?area=admin&controller=customer&action=delete&id=<?= $c->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($list)): ?><tr><td colspan="6" class="text-center text-muted">Không tìm thấy khách hàng nào</td></tr><?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . "/../layouts/pagination.php"; ?>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
