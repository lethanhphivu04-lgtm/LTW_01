<?php
$pageTitle = "Chi tiết khách hàng";
require_once __DIR__ . "/../../../dao/CustomerDAO.php";
$dao = new CustomerDAO();
$id = (int)($_GET['id'] ?? 0);
$cust = $dao->findById($id);
if (!$cust) { header("Location: index.php"); exit; }
ob_start();
?>
<h4 class="fw-bold mb-3">CHI TIẾT KHÁCH HÀNG</h4>
<div class="card card-body">
    <table class="table table-bordered mb-0">
        <tr><th width="200">Mã KH</th><td><?= $cust->id ?></td></tr>
        <tr><th>Họ tên</th><td class="fw-bold"><?= htmlspecialchars($cust->fullname) ?></td></tr>
        <tr><th>Số điện thoại</th><td><?= htmlspecialchars($cust->phone) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($cust->email ?? '') ?></td></tr>
        <tr><th>Địa chỉ</th><td><?= htmlspecialchars($cust->address ?? '') ?></td></tr>
        <tr><th>Ghi chú</th><td><?= htmlspecialchars($cust->note ?? '') ?></td></tr>
        <tr><th>Trạng thái</th><td><span class="badge bg-<?= $cust->status ? 'success' : 'secondary' ?>"><?= $cust->status ? 'Hoạt động' : 'Khóa' ?></span></td></tr>
        <tr><th>Ngày tạo</th><td><?= $cust->createdAt ?></td></tr>
    </table>
</div>
<div class="mt-3"><a href="edit.php?id=<?= $cust->id ?>" class="btn btn-warning">Sửa</a> <a href="index.php" class="btn btn-secondary">Quay lại</a></div>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
