<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <?php if ($paymentSuccess): ?>
                    <div class="text-success mb-3">
                        <i class="bi bi-check-circle" style="font-size: 3.5rem;"></i>
                    </div>
                    <h4 class="text-success mb-3">THANH TOÁN VNPAY THÀNH CÔNG!</h4>
                    <p class="text-muted mb-4">Đơn hàng của bạn đã được thanh toán thành công qua VNPay.</p>

                    <div class="table-responsive text-start mb-4">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <th class="bg-light" style="width: 45%;">Mã đơn hàng:</th>
                                <td class="fw-bold text-primary">#<?= htmlspecialchars($orderCode) ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Mã giao dịch VNPay:</th>
                                <td><?= htmlspecialchars($vnpTransactionNo) ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Ngân hàng:</th>
                                <td><?= htmlspecialchars($vnpBankCode) ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Số tiền:</th>
                                <td class="fw-bold text-danger"><?= number_format($vnpAmount, 0, ',', '.') ?> đ</td>
                            </tr>
                            <?php if ($pendingOrder): ?>
                            <tr>
                                <th class="bg-light">Người nhận:</th>
                                <td><?= htmlspecialchars($pendingOrder['fullname'] ?? '') ?> - <?= htmlspecialchars($pendingOrder['phone'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Địa chỉ giao:</th>
                                <td><?= htmlspecialchars($pendingOrder['address'] ?? '') ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <?php
                        $trackPhone = $pendingOrder['phone'] ?? ($order['customer_phone'] ?? '');
                        ?>
                        <a href="<?= $baseUrl ?>/index.php?area=client&controller=cart&action=tracking&order_code=<?= urlencode($orderCode) ?>&phone=<?= urlencode($trackPhone) ?>" class="btn btn-dark fw-semibold">
                            <i class="bi bi-search me-1"></i> Tra cứu đơn hàng
                        </a>
                        <a href="<?= $baseUrl ?>" class="btn btn-primary">Về trang chủ</a>
                        <a href="<?= $baseUrl ?>/products" class="btn btn-outline-secondary">Tiếp tục mua hàng</a>
                    </div>

                <?php else: ?>
                    <div class="text-danger mb-3">
                        <i class="bi bi-x-circle" style="font-size: 3.5rem;"></i>
                    </div>
                    <h4 class="text-danger mb-3">THANH TOÁN THẤT BẠI</h4>
                    <p class="text-muted mb-2"><?= htmlspecialchars($result['message'] ?? 'Giao dịch không thành công.') ?></p>
                    <p class="text-muted mb-4">Đơn hàng <strong>#<?= htmlspecialchars($orderCode) ?></strong> vẫn được lưu. Bạn có thể liên hệ để thanh toán lại.</p>

                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="<?= $baseUrl ?>" class="btn btn-primary">Về trang chủ</a>
                        <a href="<?= $baseUrl ?>/products" class="btn btn-outline-secondary">Tiếp tục mua hàng</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
