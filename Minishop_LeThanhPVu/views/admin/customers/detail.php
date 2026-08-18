<?php
$pageTitle = $pageTitle ?? "Chi tiết khách hàng";
ob_start();
?>
<h4 class="fw-bold mb-3">CHI TIẾT KHÁCH HÀNG</h4>
<div class="card card-body">
    <table class="table table-bordered mb-0">
        <tr><th width="200">Mã KH</th><td><?= $customer->id ?></td></tr>
        <tr><th>Họ tên</th><td class="fw-bold"><?= htmlspecialchars($customer->fullname) ?></td></tr>
        <tr><th>Số điện thoại</th><td><?= htmlspecialchars($customer->phone) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($customer->email ?? '') ?></td></tr>
        <tr><th>Địa chỉ</th><td><?= htmlspecialchars($customer->address ?? '') ?></td></tr>
        <tr><th>Ghi chú</th><td><?= htmlspecialchars($customer->note ?? '') ?></td></tr>
        <tr><th>Trạng thái</th><td><span class="badge bg-<?= $customer->status ? 'success' : 'secondary' ?>"><?= $customer->status ? 'Hoạt động' : 'Khóa' ?></span></td></tr>
        <tr><th>Ngày tạo</th><td><?= $customer->createdAt ?></td></tr>
    </table>
</div>
<div class="mt-3">
    <a href="index.php?area=admin&controller=customer&action=edit&id=<?= $customer->id ?>" class="btn btn-warning">Sửa</a>
    <a href="index.php?area=admin&controller=customer&action=index" class="btn btn-secondary">Quay lại</a>
</div>
<?php $content = ob_get_clean(); include __DIR__ . "/../layouts/master.php"; ?>
