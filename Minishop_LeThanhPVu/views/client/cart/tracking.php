<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container my-5" style="max-width: 800px;">
    <div class="text-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-search text-primary me-2"></i>Tra Cứu Đơn Hàng</h3>
        <p class="text-secondary">Nhập mã đơn hàng và số điện thoại mua hàng để kiểm tra trạng thái</p>
    </div>

    <!-- Form tra cứu -->
    <div class="card border rounded-3 shadow-sm p-4 mb-4" style="background:#f8fafc;">
        <form method="GET" action="<?= $baseUrl ?>/index.php" class="row g-3">
            <input type="hidden" name="area" value="client">
            <input type="hidden" name="controller" value="cart">
            <input type="hidden" name="action" value="tracking">

            <div class="col-md-5">
                <label class="form-label fw-semibold">Mã đơn hàng <span class="text-danger">*</span></label>
                <input type="text" name="order_code" class="form-control" placeholder="VD: DH2026081914513299" value="<?= htmlspecialchars($orderCode ?? '') ?>" required>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                <input type="tel" name="phone" class="form-control" placeholder="VD: 0901234567" value="<?= htmlspecialchars($phone ?? '') ?>" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-dark w-100 fw-semibold py-2">
                    <i class="bi bi-search me-1"></i> Tra cứu
                </button>
            </div>
        </form>
    </div>

    <!-- Hiển thị thông báo lỗi nếu không tìm thấy -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-warning text-center shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Hiển thị kết quả đơn hàng -->
    <?php if (!empty($order)): ?>
        <?php
        $statusBadges = [
            0 => ['label' => 'Chờ xử lý', 'class' => 'bg-warning text-dark'],
            1 => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-white'],
            2 => ['label' => 'Đang giao hàng', 'class' => 'bg-primary text-white'],
            3 => ['label' => 'Đã hoàn thành', 'class' => 'bg-success text-white'],
            4 => ['label' => 'Đã hủy', 'class' => 'bg-danger text-white'],
        ];
        $st = $statusBadges[(int)$order['status']] ?? ['label' => 'Không xác định', 'class' => 'bg-secondary text-white'];
        ?>

        <div class="card border rounded-3 shadow-sm overflow-hidden mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-bold">Mã đơn hàng: #<?= htmlspecialchars($order['order_code']) ?></span>
                <span class="badge <?= $st['class'] ?> px-3 py-2 fs-6"><?= $st['label'] ?></span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">Người nhận:</p>
                        <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($order['customer_name']) ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-secondary">Số điện thoại:</p>
                        <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($order['customer_phone']) ?></h6>
                    </div>
                    <div class="col-md-12">
                        <p class="mb-1 text-secondary">Địa chỉ giao hàng:</p>
                        <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($order['customer_address']) ?></h6>
                    </div>
                    <?php if (!empty($order['created_at'])): ?>
                    <div class="col-md-12">
                        <p class="mb-1 text-secondary">Thời gian đặt hàng:</p>
                        <span class="small text-muted"><?= htmlspecialchars($order['created_at']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Danh sách sản phẩm trong đơn -->
                <h6 class="fw-bold border-bottom pb-2 mb-3">Chi tiết sản phẩm đã mua</h6>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Đơn giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details as $d): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= $baseUrl ?>/uploads/products/<?= htmlspecialchars($d['image'] ?? 'default.png') ?>" 
                                             style="width:45px; height:45px; object-fit:contain;" 
                                             alt="<?= htmlspecialchars($d['proname']) ?>">
                                        <span class="fw-semibold small"><?= htmlspecialchars($d['proname']) ?></span>
                                    </div>
                                </td>
                                <td class="text-center small"><?= number_format($d['price'], 0, ',', '.') ?> đ</td>
                                <td class="text-center fw-bold"><?= (int)$d['quantity'] ?></td>
                                <td class="text-end fw-bold text-dark"><?= number_format($d['subtotal'], 0, ',', '.') ?> đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold fs-5">Tổng tiền:</td>
                                <td class="text-end fw-bold fs-5 text-danger"><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
