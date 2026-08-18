<?php
$pageTitle = $pageTitle ?? "Chi tiết đơn hàng";
$id = $order['id'] ?? 0;
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">CHI TIẾT ĐƠN HÀNG: <span class="text-primary">#<?= htmlspecialchars($order['order_code']) ?></span></h4>
    <a href="index.php?area=admin&controller=order&action=index" class="btn btn-secondary btn-sm">Quay lại danh sách</a>
</div>

<div class="row g-3 mb-4">
    <!-- Thông tin đơn hàng (Master Info) -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-dark text-white fw-bold">Thông tin đơn hàng</div>
            <div class="card-body">
                <p><strong>Mã đơn hàng:</strong> <span class="text-primary fw-bold"><?= htmlspecialchars($order['order_code']) ?></span></p>
                <p><strong>Ngày đặt:</strong> <?= $order['created_at'] ?></p>
                <p><strong>Nhân viên xử lý:</strong> <?= htmlspecialchars($order['user_name'] ?? 'Chưa phân công') ?></p>
                <p><strong>Ghi chú đơn hàng:</strong> <?= htmlspecialchars($order['note'] ?? 'Không có') ?></p>
                <p>
                    <strong>Trạng thái hiện tại:</strong>
                    <?php if ($order['status'] == 1): ?>
                        <span class="badge bg-success">Đã xác nhận</span>
                    <?php elseif ($order['status'] == 2): ?>
                        <span class="badge bg-info text-dark">Đang giao</span>
                    <?php elseif ($order['status'] == 3): ?>
                        <span class="badge bg-primary">Hoàn thành</span>
                    <?php elseif ($order['status'] == 4): ?>
                        <span class="badge bg-danger">Đã hủy</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Chờ xử lý</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Thông tin khách hàng & Cập nhật trạng thái -->
    <div class="col-md-6">
        <div class="card h-100 mb-3">
            <div class="card-header bg-dark text-white fw-bold">Thông tin khách hàng</div>
            <div class="card-body">
                <p><strong>Họ và tên:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></p>
                <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['customer_phone'] ?? 'N/A') ?></p>
                <p><strong>Địa chỉ giao hàng:</strong> <?= htmlspecialchars($order['customer_address'] ?? 'N/A') ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Form cập nhật trạng thái đơn hàng -->
<div class="card mb-4">
    <div class="card-header bg-secondary text-white fw-bold">Cập nhật trạng thái đơn hàng</div>
    <div class="card-body">
        <form method="POST" action="index.php?area=admin&controller=order&action=detail&id=<?= $id ?>" class="row g-2 align-items-center">
            <input type="hidden" name="update_status" value="1">
            <div class="col-md-6">
                <select name="status" class="form-select">
                    <option value="0" <?= $order['status'] == 0 ? 'selected' : '' ?>>0: Chờ xử lý</option>
                    <option value="1" <?= $order['status'] == 1 ? 'selected' : '' ?>>1: Đã xác nhận</option>
                    <option value="2" <?= $order['status'] == 2 ? 'selected' : '' ?>>2: Đang giao hàng</option>
                    <option value="3" <?= $order['status'] == 3 ? 'selected' : '' ?>>3: Hoàn thành</option>
                    <option value="4" <?= $order['status'] == 4 ? 'selected' : '' ?>>4: Đã hủy</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-success" type="submit">Cập nhật trạng thái</button>
            </div>
        </form>
    </div>
</div>

<!-- Danh sách sản phẩm trong đơn hàng (Detail View) -->
<h5 class="fw-bold mb-3">DANH SÁCH SẢN PHẨM TRONG ĐƠN HÀNG</h5>
<table class="table table-bordered table-striped align-middle mb-4">
    <thead class="table-dark">
        <tr>
            <th>STT</th>
            <th>Tên sản phẩm</th>
            <th>Đơn giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
        </tr>
    </thead>
    <tbody>
        <?php $stt = 1; $total = 0; foreach ($details as $d): $total += $d['subtotal']; ?>
        <tr>
            <td><?= $stt++ ?></td>
            <td class="fw-bold"><?= htmlspecialchars($d['proname']) ?></td>
            <td><?= number_format($d['price'], 0, ',', '.') ?> đ</td>
            <td><span class="badge bg-secondary fs-6"><?= $d['quantity'] ?></span></td>
            <td class="fw-bold text-success"><?= number_format($d['subtotal'], 0, ',', '.') ?> đ</td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($details)): ?>
        <tr><td colspan="5" class="text-center text-muted">Không có sản phẩm nào trong đơn hàng này</td></tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr class="table-light">
            <th colspan="4" class="text-end text-uppercase fw-bold fs-5">Tổng cộng thanh toán:</th>
            <th class="text-danger fw-bold fs-4"><?= number_format($order['total_amount'] > 0 ? $order['total_amount'] : $total, 0, ',', '.') ?> đ</th>
        </tr>
    </tfoot>
</table>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
