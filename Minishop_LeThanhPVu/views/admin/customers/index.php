<?php
$pageTitle = "Danh sách khách hàng";
require_once __DIR__ . "/../../../dao/CustomerDAO.php";
$dao = new CustomerDAO();

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $dao->delete((int)$_GET['id']);
    header("Location: index.php"); exit;
}

$keyword = trim($_GET['keyword'] ?? '');
$list = $keyword !== '' ? $dao->search($keyword) : $dao->getAll();

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH KHÁCH HÀNG</h4>
    <a href="create.php" class="btn btn-warning text-dark btn-sm">+ Thêm khách hàng</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4"><input name="keyword" class="form-control" placeholder="Tìm theo tên, SĐT, email..." value="<?= htmlspecialchars($keyword) ?>"></div>
    <div class="col-auto">
        <button class="btn btn-outline-primary">Tìm kiếm</button>
        <?php if ($keyword !== ''): ?><a href="index.php" class="btn btn-outline-secondary">Xóa lọc</a><?php endif; ?>
    </div>
</form>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>STT</th><th>Họ tên</th><th>Số điện thoại</th><th>Email</th><th>Địa chỉ</th><th width="180">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php $stt = 1; foreach ($list as $c): ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td class="fw-bold"><?= htmlspecialchars($c->fullname) ?></td>
            <td><?= htmlspecialchars($c->phone) ?></td>
            <td><?= htmlspecialchars($c->email ?? '') ?></td>
            <td><?= htmlspecialchars($c->address ?? '') ?></td>
            <td>
                <a href="detail.php?id=<?= $c->id ?>" class="btn btn-info btn-sm text-white">Chi tiết</a>
                <a href="edit.php?id=<?= $c->id ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?action=delete&id=<?= $c->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($list)): ?><tr><td colspan="6" class="text-center text-muted">Không tìm thấy kết quả</td></tr><?php endif; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
