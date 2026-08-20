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

        <div class="row g-4 mt-2">
            <!-- Cột trái: Mã giảm giá & Tóm tắt đơn hàng -->
            <div class="col-lg-6">
                <!-- Box Mã giảm giá -->
                <div class="card shadow-sm border-0 mb-4 rounded-3">
                    <div class="card-header bg-dark text-white fw-bold py-3">
                        <i class="bi bi-ticket-perforated me-2"></i>Mã Giảm Giá (Coupon / Voucher)
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-2">
                            <input type="text" id="coupon-input" class="form-control text-uppercase" placeholder="Nhập mã (VD: MINISHOP10, GIAM50K)">
                            <button class="btn btn-dark" type="button" id="btn-apply-coupon">Áp dụng</button>
                        </div>
                        <div id="coupon-alert" class="small mt-2 d-none"></div>

                        <!-- Gợi ý mã có sẵn -->
                        <div class="mt-3 pt-3 border-top">
                            <span class="small text-muted d-block mb-2"><i class="bi bi-stars text-warning me-1"></i>Mã ưu đãi có sẵn:</span>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark border p-2 cursor-pointer btn-quick-coupon" style="cursor:pointer;" data-code="MINISHOP10">
                                    <strong>MINISHOP10</strong> (Giảm 10% đơn từ 500k)
                                </span>
                                <span class="badge bg-light text-dark border p-2 cursor-pointer btn-quick-coupon" style="cursor:pointer;" data-code="GIAM50K">
                                    <strong>GIAM50K</strong> (Giảm 50k đơn từ 300k)
                                </span>
                                <span class="badge bg-light text-dark border p-2 cursor-pointer btn-quick-coupon" style="cursor:pointer;" data-code="VIP20">
                                    <strong>VIP20</strong> (Giảm 20% đơn từ 1tr)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Box Tóm tắt thanh toán -->
                <?php
                $appliedCoupon = $_SESSION['coupon'] ?? null;
                $discountVal = (float)($appliedCoupon['discount_amount'] ?? 0);
                $finalTotal = max(0, $total - $discountVal);
                ?>
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-light fw-bold py-3">
                        <i class="bi bi-receipt me-2"></i>Tóm tắt chi phí
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Tạm tính:</span>
                            <span class="fw-semibold cart-total-text"><?= number_format($total, 0, ',', '.') ?> đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success" id="coupon-row" style="<?= $discountVal > 0 ? '' : 'display:none;' ?>">
                            <span>
                                Giảm giá (<span id="coupon-code-text"><?= htmlspecialchars($appliedCoupon['code'] ?? '') ?></span>):
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-1 text-decoration-none" id="btn-remove-coupon" title="Gỡ mã">&times;</button>
                            </span>
                            <span class="fw-semibold" id="coupon-discount-text">-<?= number_format($discountVal, 0, ',', '.') ?> đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-secondary">
                            <span>Phí vận chuyển:</span>
                            <span class="fw-semibold text-success"><?= $total >= 1000000 ? 'Miễn phí' : '30.000 đ' ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5 text-dark">Tổng thanh toán:</span>
                            <span class="fw-bold fs-4 text-danger" id="cart-final-total-text"><?= number_format($finalTotal, 0, ',', '.') ?> đ</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Form Đặt hàng -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header fw-bold bg-dark text-white py-3">
                        <i class="bi bi-person-lines-fill me-2"></i>Thông tin đặt hàng
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="<?= $baseUrl ?>/cart/checkout">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" class="form-control" required placeholder="Nhập họ và tên người nhận">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control" required placeholder="VD: 0901234567">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Email nhận hóa đơn</label>
                                    <input type="email" name="email" class="form-control" placeholder="VD: email@gmail.com">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" rows="2" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ghi chú đơn hàng</label>
                                <textarea name="note" class="form-control" rows="1" placeholder="Yêu cầu giao hàng giờ hành chính, gọi trước..."></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Phương thức thanh toán</label>
                                <div class="p-3 border rounded-3 mb-2 bg-light">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pm_cod" value="cod" checked onchange="togglePaymentBtn()">
                                        <label class="form-check-label fw-semibold" for="pm_cod">
                                            <i class="bi bi-cash-stack text-success me-1"></i> Thanh toán khi nhận hàng (COD)
                                        </label>
                                        <small class="d-block text-muted">Nhận hàng và kiểm tra trước khi trả tiền mặt</small>
                                    </div>
                                </div>
                                <div class="p-3 border rounded-3 bg-light">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pm_vnpay" value="vnpay" onchange="togglePaymentBtn()">
                                        <label class="form-check-label fw-semibold" for="pm_vnpay">
                                            <i class="bi bi-qr-code-scan text-primary me-1"></i> Cổng thanh toán Online VNPay
                                        </label>
                                        <small class="d-block text-muted">Quét mã VNPay-QR hoặc dùng thẻ ATM/Visa/MasterCard</small>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="btn-checkout" class="btn btn-dark w-100 fw-bold py-3 fs-6">
                                <i class="bi bi-bag-check-fill me-1"></i> ĐẶT HÀNG NGAY (COD)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function togglePaymentBtn() {
            const btn = document.getElementById('btn-checkout');
            const isVnpay = document.getElementById('pm_vnpay').checked;
            if (isVnpay) {
                btn.innerHTML = '<i class="bi bi-credit-card-2-front me-1"></i> THANH TOÁN QUA VNPAY';
                btn.className = 'btn btn-primary w-100 fw-bold py-3 fs-6';
            } else {
                btn.innerHTML = '<i class="bi bi-bag-check-fill me-1"></i> ĐẶT HÀNG NGAY (COD)';
                btn.className = 'btn btn-dark w-100 fw-bold py-3 fs-6';
            }
        }

        // JS Xử lý Mã giảm giá (Coupon)
        document.addEventListener('DOMContentLoaded', function () {
            const applyBtn = document.getElementById('btn-apply-coupon');
            const removeBtn = document.getElementById('btn-remove-coupon');
            const input = document.getElementById('coupon-input');
            const alertBox = document.getElementById('coupon-alert');
            const baseUrl = window.BASE_URL || '/LTW_01/Minishop_LeThanhPVu';

            document.querySelectorAll('.btn-quick-coupon').forEach(el => {
                el.addEventListener('click', () => {
                    input.value = el.getAttribute('data-code');
                    applyCoupon();
                });
            });

            if (applyBtn) {
                applyBtn.addEventListener('click', applyCoupon);
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', removeCoupon);
            }

            function applyCoupon() {
                const code = input.value.trim();
                if (!code) return;

                const formData = new FormData();
                formData.append('coupon_code', code);

                fetch(baseUrl + '/index.php?area=client&controller=cart&action=apply_coupon', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    alertBox.classList.remove('d-none', 'alert-danger', 'alert-success');
                    if (data.success) {
                        alertBox.className = 'small mt-2 alert alert-success py-1 px-2';
                        alertBox.textContent = data.message;
                        document.getElementById('coupon-row').style.display = 'flex';
                        document.getElementById('coupon-code-text').textContent = data.coupon_code;
                        document.getElementById('coupon-discount-text').textContent = '-' + data.discount_formatted;
                        document.getElementById('cart-final-total-text').textContent = data.final_total_formatted;
                    } else {
                        alertBox.className = 'small mt-2 alert alert-danger py-1 px-2';
                        alertBox.textContent = data.message;
                    }
                })
                .catch(err => {
                    console.error('Coupon error:', err);
                });
            }

            function removeCoupon() {
                fetch(baseUrl + '/index.php?area=client&controller=cart&action=remove_coupon', {
                    method: 'POST'
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('coupon-row').style.display = 'none';
                        document.getElementById('cart-final-total-text').textContent = data.cart_total_formatted;
                        input.value = '';
                        alertBox.className = 'small mt-2 alert alert-info py-1 px-2';
                        alertBox.textContent = data.message;
                    }
                });
            }
        });
        </script>
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
