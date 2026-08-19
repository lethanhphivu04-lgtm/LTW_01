<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <div class="text-success mb-3">
                    <i class="bi bi-check-circle" style="font-size: 3.5rem;"></i>
                </div>
                <h4 class="text-success mb-3">ĐẶT HÀNG THÀNH CÔNG!</h4>
                <p class="text-muted mb-4">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ sớm nhất để xác nhận.</p>

                <?php if (!empty($orderInfo)): ?>
                <div class="table-responsive text-start mb-4">
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th class="bg-light" style="width: 40%;">Mã đơn hàng:</th>
                            <td class="fw-bold text-primary">#<?= htmlspecialchars($orderInfo['code'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Người nhận:</th>
                            <td><?= htmlspecialchars($orderInfo['fullname'] ?? '') ?> - <?= htmlspecialchars($orderInfo['phone'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Địa chỉ giao:</th>
                            <td><?= htmlspecialchars($orderInfo['address'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Tổng tiền:</th>
                            <td class="fw-bold text-danger"><?= number_format($orderInfo['total'] ?? 0, 0, ',', '.') ?> đ</td>
                        </tr>
                    </table>
                </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <?php if (!empty($orderInfo)): ?>
                    <a href="<?= $baseUrl ?>/index.php?area=client&controller=cart&action=tracking&order_code=<?= urlencode($orderInfo['code'] ?? '') ?>&phone=<?= urlencode($orderInfo['phone'] ?? '') ?>" class="btn btn-dark fw-semibold">
                        <i class="bi bi-search me-1"></i> Tra cứu đơn hàng
                    </a>
                    <?php endif; ?>
                    <a href="<?= $baseUrl ?>" class="btn btn-primary">Về trang chủ</a>
                    <a href="<?= $baseUrl ?>/products" class="btn btn-outline-secondary">Tiếp tục mua hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
