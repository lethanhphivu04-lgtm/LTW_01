<?php
$pageTitle = "Danh sách đơn hàng";
require_once __DIR__ . "/../../../dao/OrderDAO.php";
require_once __DIR__ . "/../../../dao/CustomerDAO.php";

$dao = new OrderDAO();
$catDAO = new CustomerDAO();

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $dao->delete((int)$_GET['id']);
    header("Location: index.php");
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $code = trim($_POST['order_code'] ?? '') ?: 'DH' . rand(1000, 9999);
    $o = new Order(
        (int)$_POST['customer_id'],
        1,
        $code,
        (float)$_POST['total_amount'],
        trim($_POST['note'] ?? ''),
        (int)($_POST['status'] ?? 0)
    );
    if (!empty($_POST['id'])) {
        $o->id = (int)$_POST['id'];
        $dao->update($o);
    } else {
        $dao->insert($o);
    }
    header("Location: index.php");
    exit;
}

$edit = isset($_GET['id']) ? $dao->findById((int)$_GET['id']) : null;
$list = $dao->getAll();
$customers = $catDAO->getAll();

ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">DANH SÁCH ĐƠN HÀNG</h4>
    <button class="btn btn-danger btn-sm" onclick="document.getElementById('formBox').classList.toggle('d-none')">+ Tạo đơn hàng</button>
</div>

<form id="formBox" method="POST" class="card card-body mb-3 <?= $edit ? '' : 'd-none' ?>">
    <input type="hidden" name="id" value="<?= $edit->id ?? 0 ?>">
    <div class="row g-2">
        <div class="col-md-3">
            <select name="customer_id" class="form-select" required>
                <option value="">-- Khách hàng --</option>
                <?php foreach ($customers as $c): ?>
                    <option value="<?= $c->id ?>" <?= ($edit && $edit->customerId == $c->id) ? 'selected' : '' ?>><?= htmlspecialchars($c->fullname) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3"><input name="order_code" class="form-control" placeholder="Mã đơn (tự động nếu trống)" value="<?= htmlspecialchars($edit->orderCode ?? '') ?>"></div>
        <div class="col-md-3"><input type="number" name="total_amount" class="form-control" placeholder="Tổng tiền" value="<?= $edit->totalAmount ?? 0 ?>" required></div>
        <div class="col-md-3"><button class="btn btn-danger w-100">Lưu đơn hàng</button></div>
    </div>
</form>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr><th>Mã đơn</th><th>Mã KH</th><th>Tổng tiền</th><th>Ghi chú</th><th>Trạng thái</th><th width="120">Thao tác</th></tr>
    </thead>
    <tbody>
        <?php foreach ($list as $o): ?>
        <tr>
            <td class="fw-bold text-primary"><?= htmlspecialchars($o->orderCode) ?></td>
            <td><?= $o->customerId ?></td>
            <td class="fw-bold text-success"><?= number_format($o->totalAmount, 0, ',', '.') ?> đ</td>
            <td><?= htmlspecialchars($o->note ?? '') ?></td>
            <td>
                <?php if ($o->status == 1): ?>
                    <span class="badge bg-success">Hoàn thành</span>
                <?php elseif ($o->status == 2): ?>
                    <span class="badge bg-danger">Đã hủy</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">Chờ xử lý</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="index.php?id=<?= $o->id ?>" class="btn btn-warning btn-sm">Sửa</a>
                <a href="index.php?action=delete&id=<?= $o->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
