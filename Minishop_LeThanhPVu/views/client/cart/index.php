<?php include __DIR__ . "/../layouts/header.php"; ?>

<div class="container my-4">
    <h3 class="mb-4">Giỏ hàng của bạn</h3>

    <?php if (!empty($_SESSION["checkout_error"])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION["checkout_error"]) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION["checkout_error"]); ?>
    <?php endif; ?>

    <div id="cart-content" class="<?= empty($cart) ? 'd-none' : '' ?>">
        <!-- Bảng hiển thị Giỏ hàng -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle text-center" id="cart-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 100px;">Hình ảnh</th>
                        <th class="text-start">Tên sản phẩm</th>
                        <th style="width: 140px;">Đơn giá</th>
                        <th style="width: 160px;">Số lượng</th>
                        <th style="width: 150px;">Thành tiền</th>
                        <th style="width: 80px;">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                    <tr id="cart-row-<?= $item['productid'] ?>">
                        <td>
                            <img src="<?= $baseUrl ?>/uploads/products/<?= htmlspecialchars($item['image'] ?? 'default.png') ?>" 
                                 alt="<?= htmlspecialchars($item['productname']) ?>" 
                                 style="width: 60px; height: 60px; object-fit: contain;">
                        </td>
                        <td class="text-start fw-semibold">
                            <?= htmlspecialchars($item['productname']) ?>
                        </td>
                        <td>
                            <?= number_format($item['price'], 0, ',', '.') ?> đ
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-secondary btn-sm px-2" onclick="updateCart(<?= $item['productid'] ?>, <?= $item['quantity'] - 1 ?>)">-</button>
                            <span id="qty-<?= $item['productid'] ?>" class="mx-2 fw-bold"><?= $item['quantity'] ?></span>
                            <button type="button" class="btn btn-outline-secondary btn-sm px-2" onclick="updateCart(<?= $item['productid'] ?>, <?= $item['quantity'] + 1 ?>)">+</button>
                        </td>
                        <td class="fw-bold text-danger" id="subtotal-<?= $item['productid'] ?>">
                            <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeCart(<?= $item['productid'] ?>)">
                                Xóa
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <td colspan="4" class="text-end fw-bold fs-5">Tổng tiền:</td>
                        <td colspan="2" class="text-start fw-bold fs-5 text-danger cart-total-text">
                            <?= number_format($total, 0, ',', '.') ?> đ
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row">
            <!-- Form Đặt hàng -->
            <div class="col-md-6 offset-md-6">
                <div class="card">
                    <div class="card-header fw-bold bg-primary text-white">
                        Thông tin đặt hàng
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= $baseUrl ?>/cart/checkout">
                            <div class="mb-3">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" class="form-control" required placeholder="Nhập họ và tên">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" required placeholder="Nhập số điện thoại">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Nhập email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" rows="2" required placeholder="Nhập địa chỉ giao hàng"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="Ghi chú đơn hàng (nếu có)"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100 fw-bold py-2">
                                ĐẶT HÀNG
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông báo khi giỏ hàng trống -->
    <div id="cart-empty" class="text-center py-5 <?= empty($cart) ? '' : 'd-none' ?>">
        <div class="alert alert-info py-4">
            <h5 class="mb-3">Giỏ hàng của bạn đang trống!</h5>
            <a href="<?= $baseUrl ?>/products" class="btn btn-primary">
                Tiếp tục mua hàng
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>
