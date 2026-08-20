<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Hóa đơn bán hàng') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1e293b;
        }
        .invoice-container {
            max-width: 800px;
            margin: 30px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .invoice-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .table-invoice th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 600;
            border-bottom: 2px solid #cbd5e1;
        }
        .table-invoice td, .table-invoice th {
            padding: 12px 14px;
        }
        @media print {
            body {
                background-color: #ffffff;
            }
            .no-print {
                display: none !important;
            }
            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Thanh công cụ in (Ẩn khi in ra giấy) -->
<div class="no-print bg-dark text-white py-3 shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center" style="max-width: 800px;">
        <div class="fw-semibold">
            <i class="bi bi-receipt me-2"></i>Xem & In Hóa đơn bán hàng #<?= htmlspecialchars($order['order_code']) ?>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold px-3">
                <i class="bi bi-printer-fill me-1"></i> In hóa đơn ngay
            </button>
            <button onclick="window.close()" class="btn btn-outline-light btn-sm px-3">
                Đóng
            </button>
        </div>
    </div>
</div>

<div class="invoice-container">
    <!-- Header Hóa Đơn -->
    <div class="invoice-header d-flex justify-content-between align-items-start flex-wrap">
        <div>
            <h2 class="fw-bold text-primary mb-1">MINISHOP</h2>
            <p class="text-secondary small mb-1">Cửa hàng công nghệ & Phụ kiện chính hãng</p>
            <p class="text-secondary small mb-0"><i class="bi bi-geo-alt me-1"></i>Đại học Công nghiệp TP.HCM (IUH)</p>
            <p class="text-secondary small mb-0"><i class="bi bi-telephone me-1"></i>Hotline: 0123-456-789 | Email: contact@minishop.com</p>
        </div>
        <div class="text-end mt-2 mt-md-0">
            <div class="invoice-title">HÓA ĐƠN BÁN HÀNG</div>
            <div class="text-danger fw-bold fs-6">Mã ĐH: #<?= htmlspecialchars($order['order_code']) ?></div>
            <div class="text-muted small">Ngày lập: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
        </div>
    </div>

    <!-- Thông tin người mua & thanh toán -->
    <div class="row mb-4">
        <div class="col-6">
            <h6 class="fw-bold text-dark mb-2 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Khách hàng:</h6>
            <div class="fw-bold text-primary"><?= htmlspecialchars($order['customer_name'] ?? 'Khách vãng lai') ?></div>
            <div class="small text-secondary"><i class="bi bi-telephone me-1"></i>SĐT: <?= htmlspecialchars($order['customer_phone'] ?? 'Chưa cập nhật') ?></div>
            <div class="small text-secondary"><i class="bi bi-geo-alt me-1"></i>Địa chỉ: <?= htmlspecialchars($order['customer_address'] ?? 'Chưa cập nhật') ?></div>
            <?php if (!empty($order['customer_email'])): ?>
                <div class="small text-secondary"><i class="bi bi-envelope me-1"></i>Email: <?= htmlspecialchars($order['customer_email']) ?></div>
            <?php endif; ?>
        </div>
        <div class="col-6 text-end">
            <h6 class="fw-bold text-dark mb-2 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Thanh toán:</h6>
            <div class="small">
                <strong>Phương thức:</strong>
                <?php if (strtolower($order['payment_method'] ?? 'cod') === 'vnpay'): ?>
                    <span class="badge bg-primary">VNPay (Đã thanh toán)</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Tiền mặt (COD)</span>
                <?php endif; ?>
            </div>
            <div class="small mt-1">
                <strong>Trạng thái:</strong>
                <?php if ($order['status'] == 3): ?>
                    <span class="badge bg-success">Đã hoàn thành</span>
                <?php elseif ($order['status'] == 4): ?>
                    <span class="badge bg-danger">Đã hủy</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">Đang xử lý</span>
                <?php endif; ?>
            </div>
            <?php if (!empty($order['note'])): ?>
                <div class="small text-muted mt-1"><em>Ghi chú: <?= htmlspecialchars($order['note']) ?></em></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bảng danh sách mặt hàng -->
    <div class="table-responsive mb-4">
        <table class="table table-invoice align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">STT</th>
                    <th>Tên sản phẩm</th>
                    <th class="text-end" style="width: 140px;">Đơn giá</th>
                    <th class="text-center" style="width: 80px;">SL</th>
                    <th class="text-end" style="width: 150px;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $stt = 1;
                $calcSubtotal = 0;
                foreach ($details as $item): 
                    $sub = (float)$item['price'] * (int)$item['quantity'];
                    $calcSubtotal += $sub;
                ?>
                <tr>
                    <td class="text-center text-muted"><?= $stt++ ?></td>
                    <td>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($item['proname']) ?></div>
                    </td>
                    <td class="text-end"><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                    <td class="text-center fw-bold"><?= $item['quantity'] ?></td>
                    <td class="text-end fw-semibold"><?= number_format($sub, 0, ',', '.') ?> đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Tổng kết hóa đơn -->
    <div class="row justify-content-end mb-4">
        <div class="col-md-6">
            <div class="bg-light p-3 rounded border">
                <div class="d-flex justify-content-between mb-2 small text-secondary">
                    <span>Tạm tính tiền hàng:</span>
                    <span class="fw-semibold text-dark"><?= number_format($calcSubtotal, 0, ',', '.') ?> đ</span>
                </div>
                
                <?php if (!empty($order['discount_amount']) && (float)$order['discount_amount'] > 0): ?>
                <div class="d-flex justify-content-between mb-2 small text-danger">
                    <span>Mã giảm giá (<?= htmlspecialchars($order['coupon_code'] ?? '') ?>):</span>
                    <span class="fw-bold">-<?= number_format($order['discount_amount'], 0, ',', '.') ?> đ</span>
                </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between mb-2 small text-secondary">
                    <span>Phí vận chuyển:</span>
                    <span class="text-success fw-semibold">Miễn phí (0 đ)</span>
                </div>
                
                <hr class="my-2">

                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark">TỔNG THANH TOÁN:</span>
                    <span class="fs-5 fw-bold text-danger"><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chữ ký người bán / người mua -->
    <div class="row text-center mt-5 pt-3">
        <div class="col-6">
            <div class="fw-bold text-dark">Khách hàng nhận hàng</div>
            <div class="text-muted small" style="font-size: 0.75rem;">(Ký và ghi rõ họ tên)</div>
            <div style="height: 70px;"></div>
            <div class="fw-semibold text-secondary"><?= htmlspecialchars($order['customer_name'] ?? '') ?></div>
        </div>
        <div class="col-6">
            <div class="fw-bold text-dark">Đại diện cửa hàng MiniShop</div>
            <div class="text-muted small" style="font-size: 0.75rem;">(Ký, đóng dấu xác nhận)</div>
            <div style="height: 70px;"></div>
            <div class="fw-semibold text-secondary">Lê Thanh Phi Vũ</div>
        </div>
    </div>

    <!-- Lời cảm ơn -->
    <div class="text-center text-muted small mt-5 pt-3 border-top">
        <em>Cảm ơn Quý khách đã mua sắm tại MiniShop! Hotline hỗ trợ kỹ thuật: 0123-456-789.</em>
    </div>
</div>

</body>
</html>
